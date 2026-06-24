<?php

namespace App\Http\Controllers;

use App\Models\IdeExecutionLog;
use App\Models\IdeNode;
use App\Models\IdeWorkspace;
use App\Services\PythonSandboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class IdeController extends Controller
{
    private const NODE_NAME_REGEX = '/^[A-Za-z0-9][A-Za-z0-9 _.\-()]*$/';
    private const NODE_NAME_REGEX_MESSAGE = 'File and folder names may only contain letters, numbers, spaces, underscores, dashes, dots, and parentheses.';

    public function __construct(private readonly PythonSandboxService $pythonSandbox)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $workspace = IdeWorkspace::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name . "'s Workspace"]
        );

        $tree = $this->buildTree($workspace);

        return view('ide.index', compact('workspace', 'tree'));
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

        $workspace = IdeWorkspace::where('id', $validated['workspace_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

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

        $node = IdeNode::create([
            'workspace_id' => $workspace->id,
            'parent_id' => $parentId,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'name' => $validated['name'],
            'content' => $validated['content'] ?? ($validated['type'] === 'file' ? '' : null),
            'language' => $validated['language'] ?? 'python',
        ]);

        return response()->json(['node' => $node], 201);
    }

    public function updateNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'name' => $this->nodeNameRules('sometimes'),
            'content' => ['sometimes', 'nullable', 'string', 'max:50000'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:ide_nodes,id'],
        ], $this->nodeNameValidationMessages());

        if (array_key_exists('name', $validated)) {
            $this->assertSafeNodeName($validated['name']);
        }

        if (array_key_exists('parent_id', $validated)) {
            $validated['parent_id'] = $this->authorizedParentId($node->workspace, $validated['parent_id']);
            $this->assertNotMovingIntoSelf($node, $validated['parent_id']);
        }

        $targetName = $validated['name'] ?? $node->name;
        $targetParent = array_key_exists('parent_id', $validated) ? $validated['parent_id'] : $node->parent_id;

        $this->assertNoSiblingCollision($node, $targetName, $targetParent);

        $node->update($validated);

        return response()->json(['node' => $node->fresh()]);
    }

    public function moveNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:ide_nodes,id'],
            'new_name' => $this->nodeNameRules('nullable'),
        ], $this->nodeNameValidationMessages());

        $parentId = $this->authorizedParentId($node->workspace, $validated['parent_id'] ?? null);
        $this->assertNotMovingIntoSelf($node, $parentId);

        $targetName = $validated['new_name'] ?? $node->name;
        $this->assertSafeNodeName($targetName);
        $this->assertNoSiblingCollision($node, $targetName, $parentId);

        $node->update([
            'parent_id' => $parentId,
            'name' => $targetName,
        ]);

        return response()->json(['moved' => true, 'node' => $node->fresh()]);
    }

    public function deleteNode(IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        $this->deleteRecursive($node);

        return response()->json(['deleted' => true]);
    }

    public function renameNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        $validated = $request->validate([
            'name' => $this->nodeNameRules('required'),
        ], $this->nodeNameValidationMessages());

        $this->assertSafeNodeName($validated['name']);
        $this->assertNoSiblingCollision($node, $validated['name'], $node->parent_id);

        $node->update(['name' => $validated['name']]);

        return response()->json(['node' => $node->fresh()]);
    }

    public function saveContent(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        if ($node->type !== 'file') {
            return response()->json(['error' => 'Only files can store source code.'], 422);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:50000'],
        ]);

        $node->update(['content' => $validated['content']]);

        return response()->json(['saved' => true, 'updated_at' => $node->fresh()->updated_at]);
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

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:50000'],
            'stdin' => ['nullable', 'string', 'max:10000'],
        ]);

        $workspacePath = storage_path('app' . DIRECTORY_SEPARATOR . 'workspaces' . DIRECTORY_SEPARATOR . 'user_' . Auth::id());
        File::ensureDirectoryExists($workspacePath, 0755, true);

        $allNodes = IdeNode::where('workspace_id', $node->workspace_id)
            ->orderByRaw("type = 'folder' DESC")
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        $entryRelativePath = $this->syncWorkspaceToDisk($workspacePath, $allNodes, $node, $validated['content'] ?? null);

        $result = $this->pythonSandbox->runWorkspace(
            $workspacePath,
            $entryRelativePath,
            $validated['content'] ?? (string) ($node->content ?? ''),
            $validated['stdin'] ?? ''
        );

        IdeExecutionLog::create([
            'node_id' => $node->id,
            'user_id' => Auth::id(),
            'output' => $result['stdout'] ?? '',
            'error' => $result['stderr'] ?? '',
            'exit_code' => $result['exit_code'] ?? 1,
            'execution_time_ms' => $result['execution_time_ms'] ?? 0,
        ]);

        return response()->json([
            'output' => $result['stdout'] ?? '',
            'error' => $result['stderr'] ?? '',
            'exit_code' => $result['exit_code'] ?? 1,
            'execution_time_ms' => $result['execution_time_ms'] ?? 0,
            'plots' => $result['plots'] ?? [],
        ]);
    }

    private function buildTree(IdeWorkspace $workspace): array
    {
        $allNodes = IdeNode::where('workspace_id', $workspace->id)
            ->orderByRaw("type = 'folder' DESC")
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        $roots = [];
        foreach ($allNodes as $node) {
            if ($node->parent_id === null) {
                $roots[] = $this->nodeToArray($node, $allNodes);
            }
        }

        return $roots;
    }

    private function nodeToArray(IdeNode $node, $allNodes): array
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

        foreach ($allNodes as $child) {
            if ((int) $child->parent_id === (int) $node->id) {
                $data['children'][] = $this->nodeToArray($child, $allNodes);
            }
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

        while ($current->parent_id !== null && $allNodes->has($current->parent_id)) {
            $current = $allNodes->get($current->parent_id);
            array_unshift($parts, $current->name);
        }

        return implode('/', array_map(fn (string $part): string => $this->safePathSegment($part), $parts));
    }

    private function deleteRecursive(IdeNode $node): void
    {
        foreach ($node->children as $child) {
            $this->deleteRecursive($child);
        }

        $node->delete();
    }

    private function authorizeNode(IdeNode $node): void
    {
        abort_unless((int) $node->user_id === (int) Auth::id(), 403);
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

        $current = IdeNode::find($parentId);
        while ($current && $current->parent_id !== null) {
            if ((int) $current->parent_id === (int) $node->id) {
                abort(422, 'Cannot move a folder into one of its children.');
            }
            $current = IdeNode::find($current->parent_id);
        }
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

        abort_unless(str_starts_with(str_replace('\\', '/', $dir), str_replace('\\', '/', $base)), 422, 'Invalid workspace path.');

        return $fullPath;
    }
}
