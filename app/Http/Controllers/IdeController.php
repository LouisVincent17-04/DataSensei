<?php

namespace App\Http\Controllers;

use App\Models\IdeExecutionLog;
use App\Models\IdeNode;
use App\Models\IdeWorkspace;
use App\Services\PythonSandboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class IdeController extends Controller
{
    private const MAX_NODES_PER_WORKSPACE = 250;
    private const MAX_TREE_DEPTH = 20;
    private const NODE_NAME_REGEX = '/^[A-Za-z0-9][A-Za-z0-9 _.\-()]*$/';
    private const NODE_NAME_REGEX_MESSAGE = 'File and folder names may only contain letters, numbers, spaces, underscores, dashes, dots, and parentheses.';

    public function __construct(private readonly PythonSandboxService $pythonSandbox)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $workspace = IdeWorkspace::where('user_id', $user->id)->first();

        if (! $workspace) {
            return view('ide.initialize');
        }

        $tree = $this->buildTree($workspace);

        return view('ide.index', compact('workspace', 'tree'));
    }

    public function initializeWorkspace()
    {
        $user = Auth::user();

        DB::transaction(function () use ($user): void {
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();

            IdeWorkspace::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name . "'s Workspace"]
            );
        }, 3);

        return redirect()->route('ide.index');
    }

    public function storeNode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workspace_id' => ['required', 'integer', 'exists:ide_workspaces,id'],
            'parent_id' => ['nullable', 'integer', 'exists:ide_nodes,id'],
            'type' => ['required', Rule::in(['file', 'folder'])],
            'name' => $this->nodeNameRules('required'),
            'content' => ['nullable', 'string', 'max:50000'],
            'language' => ['nullable', 'string', 'max:50'],
        ], $this->nodeNameValidationMessages());

        $this->assertSafeNodeName($validated['name']);

        return DB::transaction(function () use ($validated) {
            $workspace = $this->lockWorkspace((int) $validated['workspace_id']);

            if ($workspace->nodes()->count() >= self::MAX_NODES_PER_WORKSPACE) {
                return response()->json([
                    'error' => 'This workspace has reached its 250-item limit. Delete unused files or folders before creating another item.',
                ], 422);
            }

            $parentId = $this->authorizedParentId($workspace, $validated['parent_id'] ?? null);

            $exists = IdeNode::where('workspace_id', $workspace->id)
                ->where('parent_id', $parentId)
                ->where('name', $validated['name'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'error' => 'A file or folder with that name already exists here.',
                    'collision' => true,
                ], 409);
            }

            $isFile = $validated['type'] === 'file';

            $node = IdeNode::create([
                'workspace_id' => $workspace->id,
                'parent_id' => $parentId,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'name' => $validated['name'],
                'content' => $isFile ? ($validated['content'] ?? '') : null,
                'language' => $isFile ? $this->languageForFilename($validated['name']) : null,
            ]);

            return response()->json(['node' => $node], 201);
        });
    }

    public function updateNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'name' => $this->nodeNameRules('sometimes'),
            'content' => ['sometimes', 'nullable', 'string', 'max:50000'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:ide_nodes,id'],
        ], $this->nodeNameValidationMessages());

        return DB::transaction(function () use ($node, $validated) {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $node = $this->lockNode((int) $node->id, (int) $workspace->id);

            if (array_key_exists('name', $validated)) {
                $this->assertSafeNodeName($validated['name']);
            }

            if ($node->type !== 'file' && array_key_exists('content', $validated)) {
                return response()->json(['error' => 'Folders cannot store source code.'], 422);
            }

            if (array_key_exists('parent_id', $validated)) {
                $validated['parent_id'] = $this->authorizedParentId($workspace, $validated['parent_id']);
                $this->assertNotMovingIntoSelf($node, $validated['parent_id']);
                $this->assertMoveFitsDepth($node, $validated['parent_id']);
            }

            $targetName = $validated['name'] ?? $node->name;
            $targetParent = array_key_exists('parent_id', $validated) ? $validated['parent_id'] : $node->parent_id;

            $this->assertNoSiblingCollision($node, $targetName, $targetParent);

            if (array_key_exists('name', $validated) && $node->type === 'file') {
                $validated['language'] = $this->languageForFilename($validated['name']);
            }

            $node->update($validated);

            return response()->json(['node' => $node->fresh()]);
        });
    }

    public function moveNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:ide_nodes,id'],
            'new_name' => $this->nodeNameRules('nullable'),
        ], $this->nodeNameValidationMessages());

        return DB::transaction(function () use ($node, $validated) {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $node = $this->lockNode((int) $node->id, (int) $workspace->id);
            $parentId = $this->authorizedParentId($workspace, $validated['parent_id'] ?? null);
            $this->assertNotMovingIntoSelf($node, $parentId);
            $this->assertMoveFitsDepth($node, $parentId);

            $targetName = $validated['new_name'] ?? $node->name;
            $this->assertSafeNodeName($targetName);
            $this->assertNoSiblingCollision($node, $targetName, $parentId);

            $payload = [
                'parent_id' => $parentId,
                'name' => $targetName,
            ];

            if ($node->type === 'file') {
                $payload['language'] = $this->languageForFilename($targetName);
            }

            $node->update($payload);

            return response()->json(['moved' => true, 'node' => $node->fresh()]);
        });
    }

    public function deleteNode(IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        DB::transaction(function () use ($node): void {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $node = $this->lockNode((int) $node->id, (int) $workspace->id);
            $this->deleteRecursive($node);
        });

        return response()->json(['deleted' => true]);
    }

    public function renameNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'name' => $this->nodeNameRules('required'),
        ], $this->nodeNameValidationMessages());

        return DB::transaction(function () use ($node, $validated) {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $node = $this->lockNode((int) $node->id, (int) $workspace->id);

            $this->assertSafeNodeName($validated['name']);
            $this->assertNoSiblingCollision($node, $validated['name'], $node->parent_id);

            $payload = ['name' => $validated['name']];
            if ($node->type === 'file') {
                $payload['language'] = $this->languageForFilename($validated['name']);
            }

            $node->update($payload);

            return response()->json(['node' => $node->fresh()]);
        });
    }

    public function saveContent(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        if ($node->type !== 'file') {
            return response()->json(['error' => 'Only files can store source code.'], 422);
        }

        $validated = $request->validate([
            'content' => ['present', 'string', 'max:50000'],
        ]);

        $updatedAt = DB::transaction(function () use ($node, $validated) {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $lockedNode = $this->lockNode((int) $node->id, (int) $workspace->id);
            abort_unless($lockedNode->type === 'file', 422, 'Only files can store source code.');
            $lockedNode->update(['content' => $validated['content']]);

            return $lockedNode->fresh()->updated_at;
        }, 3);

        return response()->json(['saved' => true, 'updated_at' => $updatedAt]);
    }

    public function tree(): JsonResponse
    {
        $workspace = IdeWorkspace::where('user_id', Auth::id())->firstOrFail();

        return response()->json(['tree' => $this->buildTree($workspace)]);
    }

    public function runNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        if ($node->type !== 'file') {
            return response()->json(['error' => 'Only files can be executed.'], 422);
        }

        if ($node->language !== 'python' || strtolower(pathinfo($node->name, PATHINFO_EXTENSION)) !== 'py') {
            return response()->json(['error' => 'Only Python .py files can be executed.'], 422);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:50000'],
            'stdin' => ['nullable', 'string', 'max:10000'],
        ]);

        $snapshot = DB::transaction(function () use ($node): array {
            $workspace = $this->lockWorkspace((int) $node->workspace_id);
            $runningNode = $this->lockNode((int) $node->id, (int) $workspace->id);

            abort_unless($runningNode->type === 'file', 422, 'Only files can be executed.');
            abort_unless(
                $runningNode->language === 'python'
                    && strtolower(pathinfo($runningNode->name, PATHINFO_EXTENSION)) === 'py',
                422,
                'Only Python .py files can be executed.'
            );

            $allNodes = IdeNode::where('workspace_id', $workspace->id)
                ->where('user_id', Auth::id())
                ->orderByRaw("type = 'folder' DESC")
                ->orderBy('name')
                ->get()
                ->keyBy('id');

            return [
                'workspace_id' => $workspace->id,
                'node' => $runningNode,
                'nodes' => $allNodes,
            ];
        }, 3);

        /** @var IdeNode $runningNode */
        $runningNode = $snapshot['node'];
        $workspacePath = $this->freshRuntimeWorkspace((int) Auth::id());

        try {
            $entryRelativePath = $this->syncWorkspaceToDisk(
                $workspacePath,
                $snapshot['nodes'],
                $runningNode,
                $validated['content'] ?? null
            );

            $result = $this->pythonSandbox->runWorkspace(
                $workspacePath,
                $entryRelativePath,
                $validated['content'] ?? (string) ($runningNode->content ?? ''),
                $validated['stdin'] ?? '',
                ['interactive_input' => true]
            );
        } finally {
            File::deleteDirectory($workspacePath);
        }

        DB::transaction(function () use ($snapshot, $runningNode, $result): void {
            // Waiting for the learner is an intermediate IDE state, not a
            // failed execution. Only the completed or genuinely failed run is
            // stored in history and used by learning analytics.
            if (($result['input_required'] ?? false) === true) {
                return;
            }

            $workspace = IdeWorkspace::query()
                ->whereKey($snapshot['workspace_id'])
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            if (! $workspace) {
                return;
            }

            $nodeStillExists = IdeNode::query()
                ->whereKey($runningNode->id)
                ->where('workspace_id', $workspace->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first(['id']);

            if (! $nodeStillExists) {
                return;
            }

            IdeExecutionLog::create([
                'node_id' => $runningNode->id,
                'user_id' => Auth::id(),
                'output' => $result['stdout'] ?? '',
                'error' => $result['stderr'] ?? '',
                'exit_code' => $result['exit_code'] ?? 1,
                'execution_time_ms' => $result['execution_time_ms'] ?? 0,
            ]);

            $oldLogIds = IdeExecutionLog::where('node_id', $runningNode->id)
                ->where('user_id', Auth::id())
                ->orderByDesc('id')
                ->skip(100)
                ->take(1000)
                ->pluck('id');

            if ($oldLogIds->isNotEmpty()) {
                IdeExecutionLog::whereIn('id', $oldLogIds)->delete();
            }
        }, 3);

        return response()->json([
            'output' => $result['stdout'] ?? '',
            'error' => $result['stderr'] ?? '',
            'exit_code' => $result['exit_code'] ?? 1,
            'execution_time_ms' => $result['execution_time_ms'] ?? 0,
            'plots' => $result['plots'] ?? [],
            'input_required' => (bool) ($result['input_required'] ?? false),
            'input_prompt' => $result['input_prompt'] ?? null,
            // null means this runner build does not report the count, so the
            // browser must not treat it as "nothing was read".
            'inputs_consumed' => isset($result['inputs_consumed']) ? (int) $result['inputs_consumed'] : null,
        ]);
    }

    private function buildTree(IdeWorkspace $workspace): array
    {
        $allNodes = IdeNode::where('workspace_id', $workspace->id)
            ->where('user_id', $workspace->user_id)
            ->orderByRaw("type = 'folder' DESC")
            ->orderBy('name')
            ->get();

        $nodesById = $allNodes->keyBy('id');
        $childrenByParent = [];

        foreach ($allNodes as $node) {
            $parentKey = $node->parent_id === null ? 'root' : (string) $node->parent_id;
            $childrenByParent[$parentKey][] = $node;
        }

        $roots = [];
        foreach ($allNodes as $node) {
            if ($node->parent_id === null || ! $nodesById->has($node->parent_id)) {
                $roots[] = $this->nodeToArray($node, $childrenByParent);
            }
        }

        return $roots;
    }

    private function nodeToArray(IdeNode $node, array $childrenByParent, array $ancestors = [], int $depth = 0): array
    {
        $data = [
            'id' => $node->id,
            'name' => $node->name,
            'type' => $node->type,
            'language' => $node->language,
            'parent_id' => $node->parent_id,
            'content' => $node->content,
            'children' => [],
        ];

        if ($depth >= self::MAX_TREE_DEPTH || isset($ancestors[$node->id])) {
            return $data;
        }

        $ancestors[$node->id] = true;

        foreach ($childrenByParent[(string) $node->id] ?? [] as $child) {
            $data['children'][] = $this->nodeToArray($child, $childrenByParent, $ancestors, $depth + 1);
        }

        usort($data['children'], static function (array $a, array $b): int {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'folder' ? -1 : 1;
            }

            return strcmp($a['name'], $b['name']);
        });

        return $data;
    }

    private function syncWorkspaceToDisk(string $workspacePath, $allNodes, IdeNode $runningNode, ?string $runningContent): string
    {
        $entryRelativePath = $this->relativePathForNode($runningNode, $allNodes);

        foreach ($allNodes as $node) {
            $relativePath = $this->relativePathForNode($node, $allNodes);
            $fullPath = $this->safeWorkspacePath($workspacePath, $relativePath);

            if ($node->type === 'folder') {
                File::ensureDirectoryExists($fullPath, 0755, true);
                continue;
            }

            File::ensureDirectoryExists(dirname($fullPath), 0755, true);
            File::put($fullPath, $node->id === $runningNode->id ? ($runningContent ?? (string) $node->content) : (string) $node->content);
        }

        return $entryRelativePath;
    }

    private function relativePathForNode(IdeNode $node, $allNodes): string
    {
        $parts = [$node->name];
        $current = $node;
        $seen = [$node->id => true];

        while ($current->parent_id !== null && $allNodes->has($current->parent_id)) {
            $current = $allNodes->get($current->parent_id);

            if (isset($seen[$current->id])) {
                abort(422, 'The workspace folder hierarchy contains a cycle.');
            }
            $seen[$current->id] = true;

            array_unshift($parts, $current->name);
        }

        return implode('/', array_map(fn (string $part): string => $this->safePathSegment($part), $parts));
    }

    private function deleteRecursive(IdeNode $node): void
    {
        // The self-referencing foreign key cascades through descendants and also
        // removes their execution logs, avoiding an unbounded N+1 recursion here.
        $node->delete();
    }

    private function authorizeNode(IdeNode $node): void
    {
        abort_unless(
            (int) $node->user_id === (int) Auth::id()
            && (int) $node->workspace()->value('user_id') === (int) Auth::id(),
            403
        );
    }

    private function lockWorkspace(int $workspaceId): IdeWorkspace
    {
        return IdeWorkspace::where('id', $workspaceId)
            ->where('user_id', Auth::id())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function lockNode(int $nodeId, int $workspaceId): IdeNode
    {
        return IdeNode::where('id', $nodeId)
            ->where('workspace_id', $workspaceId)
            ->where('user_id', Auth::id())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function authorizedParentId(IdeWorkspace $workspace, mixed $parentId): ?int
    {
        if ($parentId === null || $parentId === '') {
            return null;
        }

        $parent = IdeNode::where('id', $parentId)
            ->where('workspace_id', $workspace->id)
            ->where('user_id', Auth::id())
            ->where('type', 'folder')
            ->firstOrFail();

        $depth = 0;
        $cursor = $parent;
        $seen = [];

        while ($cursor) {
            if (isset($seen[$cursor->id])) {
                abort(422, 'The destination folder hierarchy is invalid.');
            }

            $seen[$cursor->id] = true;
            $depth++;
            abort_if($depth > self::MAX_TREE_DEPTH, 422, 'Folders cannot be nested more than 20 levels deep.');

            $cursor = $cursor->parent_id === null
                ? null
                : IdeNode::where('id', $cursor->parent_id)
                    ->where('workspace_id', $workspace->id)
                    ->where('user_id', Auth::id())
                    ->first();
        }

        return (int) $parent->id;
    }

    private function assertNotMovingIntoSelf(IdeNode $node, ?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        if ((int) $parentId === (int) $node->id) {
            abort(422, 'Cannot move a folder into itself.');
        }

        $seen = [];
        $current = IdeNode::query()
            ->where('id', $parentId)
            ->where('workspace_id', $node->workspace_id)
            ->where('user_id', Auth::id())
            ->first();

        while ($current) {
            if (isset($seen[$current->id])) {
                abort(422, 'The destination folder hierarchy is invalid.');
            }
            $seen[$current->id] = true;

            if ((int) $current->parent_id === (int) $node->id) {
                abort(422, 'Cannot move a folder into one of its children.');
            }

            if ($current->parent_id === null) {
                break;
            }

            $current = IdeNode::query()
                ->where('id', $current->parent_id)
                ->where('workspace_id', $node->workspace_id)
                ->where('user_id', Auth::id())
                ->first();
        }
    }

    private function assertMoveFitsDepth(IdeNode $node, ?int $parentId): void
    {
        $nodes = IdeNode::where('workspace_id', $node->workspace_id)
            ->where('user_id', Auth::id())
            ->get(['id', 'parent_id'])
            ->keyBy('id');

        $targetDepth = 0;
        $cursorId = $parentId;
        $seen = [];

        while ($cursorId !== null && $nodes->has($cursorId)) {
            abort_if(isset($seen[$cursorId]), 422, 'The destination folder hierarchy is invalid.');
            $seen[$cursorId] = true;
            $targetDepth++;
            $cursorId = $nodes->get($cursorId)->parent_id;
        }

        $children = [];
        foreach ($nodes as $candidate) {
            if ($candidate->parent_id !== null) {
                $children[(int) $candidate->parent_id][] = (int) $candidate->id;
            }
        }

        $maxRelativeDepth = 0;
        $queue = [[(int) $node->id, 0]];
        $visited = [];

        while ($queue) {
            [$currentId, $relativeDepth] = array_shift($queue);
            abort_if(isset($visited[$currentId]), 422, 'The workspace folder hierarchy contains a cycle.');
            $visited[$currentId] = true;
            $maxRelativeDepth = max($maxRelativeDepth, $relativeDepth);

            foreach ($children[$currentId] ?? [] as $childId) {
                $queue[] = [$childId, $relativeDepth + 1];
            }
        }

        abort_if(
            $targetDepth + $maxRelativeDepth > self::MAX_TREE_DEPTH,
            422,
            'That move would nest files or folders more than 20 levels deep.'
        );
    }

    private function assertNoSiblingCollision(IdeNode $node, string $targetName, ?int $parentId): void
    {
        $exists = IdeNode::where('workspace_id', $node->workspace_id)
            ->where('parent_id', $parentId)
            ->where('name', $targetName)
            ->where('id', '!=', $node->id)
            ->exists();

        if ($exists) {
            abort(409, 'A file or folder with that name already exists in this location.');
        }
    }

    private function nodeNameRules(string $presence): array
    {
        return [$presence, 'string', 'max:120', 'regex:' . self::NODE_NAME_REGEX];
    }

    private function nodeNameValidationMessages(): array
    {
        return [
            'name.regex' => self::NODE_NAME_REGEX_MESSAGE,
            'new_name.regex' => self::NODE_NAME_REGEX_MESSAGE,
        ];
    }

    private function assertSafeNodeName(string $name): void
    {
        $trimmed = trim($name);

        if (
            $trimmed === ''
            || $name !== $trimmed
            || $name === '.'
            || $name === '..'
            || str_contains($name, '/')
            || str_contains($name, '\\')
            || preg_match('/[\x00-\x1F\x7F]/', $name) === 1
        ) {
            abort(422, 'Invalid file or folder name.');
        }
    }

    private function safePathSegment(string $segment): string
    {
        $this->assertSafeNodeName($segment);

        return $segment;
    }

    private function safeWorkspacePath(string $workspacePath, string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $fullPath = $workspacePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $base = realpath($workspacePath) ?: $workspacePath;
        $dir = realpath(dirname($fullPath)) ?: dirname($fullPath);

        $base = rtrim(str_replace('\\', '/', $base), '/');
        $dir = str_replace('\\', '/', $dir);

        abort_unless($dir === $base || str_starts_with($dir, $base.'/'), 422, 'Invalid workspace path.');

        return $fullPath;
    }

    private function freshRuntimeWorkspace(int $userId): string
    {
        $root = storage_path('app'.DIRECTORY_SEPARATOR.'workspaces');
        File::ensureDirectoryExists($root, 0755, true);

        $userRoot = $root.DIRECTORY_SEPARATOR.'user_'.$userId;
        File::ensureDirectoryExists($userRoot, 0755, true);

        $workspacePath = $userRoot.DIRECTORY_SEPARATOR.'run_'.bin2hex(random_bytes(12));

        File::ensureDirectoryExists($workspacePath, 0755, true);

        return $workspacePath;
    }

    private function languageForFilename(string $filename): string
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'py' => 'python',
            'csv' => 'csv',
            'json' => 'json',
            'sql' => 'sql',
            'md' => 'markdown',
            default => 'text',
        };
    }
}
