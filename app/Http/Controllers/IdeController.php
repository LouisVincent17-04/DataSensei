<?php

namespace App\Http\Controllers;

use App\Models\IdeWorkspace;
use App\Models\IdeNode;
use App\Models\IdeExecutionLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class IdeController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  MAIN VIEW
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $user      = Auth::user();
        $workspace = IdeWorkspace::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name . "'s Workspace"]
        );

        $tree = $this->buildTree($workspace);

        return view('ide.index', compact('workspace', 'tree'));
    }

    // ─────────────────────────────────────────────────────────────
    //  TREE BUILDER
    // ─────────────────────────────────────────────────────────────
    private function buildTree(IdeWorkspace $workspace): array
    {
        $allNodes = IdeNode::where('workspace_id', $workspace->id)
                           ->orderByRaw("type = 'folder' DESC")
                           ->orderBy('name')
                           ->get()
                           ->keyBy('id');

        $roots = [];
        foreach ($allNodes as $node) {
            if (is_null($node->parent_id)) {
                $roots[] = $this->nodeToArray($node, $allNodes);
            }
        }
        return $roots;
    }

    private function nodeToArray(IdeNode $node, $allNodes): array
    {
        $data = [
            'id'         => $node->id,
            'name'       => $node->name,
            'type'       => $node->type,
            'language'   => $node->language,
            'parent_id'  => $node->parent_id,
            'content'    => $node->content,
            'children'   => [],
        ];

        foreach ($allNodes as $child) {
            if ($child->parent_id === $node->id) {
                $data['children'][] = $this->nodeToArray($child, $allNodes);
            }
        }

        usort($data['children'], function($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'folder' ? -1 : 1;
            }
            return strcmp($a['name'], $b['name']);
        });

        return $data;
    }

    // ─────────────────────────────────────────────────────────────
    //  NODE CRUD
    // ─────────────────────────────────────────────────────────────
    public function storeNode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workspace_id' => 'required|exists:ide_workspaces,id',
            'parent_id'    => 'nullable|exists:ide_nodes,id',
            'type'         => 'required|in:file,folder',
            'name'         => 'required|string|max:255',
            'content'      => 'nullable|string',
            'language'     => 'nullable|string|max:50',
        ]);

        $workspace = IdeWorkspace::where('id', $validated['workspace_id'])
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();

        $exists = IdeNode::where('workspace_id', $workspace->id)
                         ->where('parent_id', $validated['parent_id'] ?? null)
                         ->where('name', $validated['name'])
                         ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'A file or folder with that name already exists here.', 
                'collision' => true
            ], 409);
        }

        $node = IdeNode::create([
            'workspace_id' => $workspace->id,
            'parent_id'    => $validated['parent_id'] ?? null,
            'user_id'      => Auth::id(),
            'type'         => $validated['type'],
            'name'         => $validated['name'],
            'content'      => $validated['content'] ?? ($validated['type'] === 'file' ? '' : null),
            'language'     => $validated['language'] ?? 'python',
        ]);

        return response()->json(['node' => $node], 201);
    }

    public function updateNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'content'   => 'sometimes|nullable|string',
            'parent_id' => 'sometimes|nullable|exists:ide_nodes,id',
        ]);
        $node->update($validated);
        return response()->json(['node' => $node]);
    }

    public function moveNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:ide_nodes,id',
            'new_name'  => 'nullable|string|max:255'
        ]);
        
        if ($validated['parent_id'] == $node->id) {
            return response()->json(['error' => 'Cannot move a folder into itself.'], 422);
        }

        $targetName = $validated['new_name'] ?? $node->name;
        
        $exists = IdeNode::where('workspace_id', $node->workspace_id)
                         ->where('parent_id', $validated['parent_id'] ?? null)
                         ->where('name', $targetName)
                         ->where('id', '!=', $node->id)
                         ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'A file named "' . $targetName . '" already exists in this location.',
                'collision' => true
            ], 409);
        }
        
        $node->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name'      => $targetName
        ]);
        
        return response()->json(['moved' => true, 'node' => $node]);
    }

    public function deleteNode(IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        $this->deleteRecursive($node);
        return response()->json(['deleted' => true]);
    }

    private function deleteRecursive(IdeNode $node): void
    {
        foreach ($node->children as $child) {
            $this->deleteRecursive($child);
        }
        $node->delete();
    }

    public function renameNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $exists = IdeNode::where('workspace_id', $node->workspace_id)
                         ->where('parent_id', $node->parent_id)
                         ->where('name', $validated['name'])
                         ->where('id', '!=', $node->id)
                         ->exists();
        if ($exists) return response()->json(['error' => 'Name already taken.'], 422);
        $node->update(['name' => $validated['name']]);
        return response()->json(['node' => $node]);
    }

    public function saveContent(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);
        $validated = $request->validate(['content' => 'required|string']);
        $node->update(['content' => $validated['content']]);
        return response()->json(['saved' => true, 'updated_at' => $node->fresh()->updated_at]);
    }

    public function tree(): JsonResponse
    {
        $workspace = IdeWorkspace::where('user_id', Auth::id())->firstOrFail();
        return response()->json(['tree' => $this->buildTree($workspace)]);
    }

    // ─────────────────────────────────────────────────────────────
    //  RUN (Persistent Workspace Execution)
    // ─────────────────────────────────────────────────────────────
    public function runNode(Request $request, IdeNode $node): JsonResponse
    {
        $this->authorizeNode($node);

        if ($node->type !== 'file') {
            return response()->json(['error' => 'Only files can be executed.'], 422);
        }

        $start = microtime(true);
        $python = $this->resolvePython();

        if ($python === null) {
            return response()->json(['error' => 'Python 3 not found on server.'], 500);
        }

        // ── 1. Create a PERSISTENT Workspace Directory ──
        // Instead of a random sandboxId, we use the User ID for consistency.
        $workspacePath = storage_path('app' . DIRECTORY_SEPARATOR . 'workspaces' . DIRECTORY_SEPARATOR . 'user_' . Auth::id());
        
        if (!file_exists($workspacePath)) {
            mkdir($workspacePath, 0777, true);
        }

        try {
            // ── 2. Sync all nodes (folders + files) to the permanent disk ──
            $allNodes = IdeNode::where('workspace_id', $node->workspace_id)
                               ->orderByRaw("type = 'folder' DESC")
                               ->get()
                               ->keyBy('id');

            $resolvePath = function (IdeNode $n) use (&$resolvePath, $allNodes, $workspacePath): string {
                $parts = [$n->name];
                $current = $n;
                while (!is_null($current->parent_id) && $allNodes->has($current->parent_id)) {
                    $current = $allNodes->get($current->parent_id);
                    array_unshift($parts, $current->name);
                }
                return $workspacePath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
            };

            foreach ($allNodes as $n) {
                $fullPath = $resolvePath($n);
                if ($n->type === 'folder') {
                    if (!file_exists($fullPath)) mkdir($fullPath, 0777, true);
                } else {
                    $dir = dirname($fullPath);
                    if (!file_exists($dir)) mkdir($dir, 0777, true);
                    
                    $content = ($n->id == $node->id)
                        ? ($request->input('content', $n->content) ?? '')
                        : ($n->content ?? '');
                    
                    if ($n->id == $node->id) {
                        $content = $this->getPlotShim() . "\n" . $this->getPathShim($workspacePath) . "\n" . $content;
                    }
                    file_put_contents($fullPath, $content);
                }
            }

            // ── 3. Prepare Environment Variables (Fixed for Windows & Matplotlib) ──
            $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];

            // Drive/Path logic for Windows Home determination
            $drive = substr($workspacePath, 0, 2);
            $pathOnly = substr($workspacePath, 2);

            $env = array_merge($_ENV, $_SERVER);
            $env['HOME']         = $workspacePath;
            $env['USERPROFILE']  = $workspacePath; // Fix for Path.home()
            $env['HOMEDRIVE']    = $drive;         // Fix for Windows
            $env['HOMEPATH']     = $pathOnly;      // Fix for Windows
            $env['MPLBACKEND']   = 'Agg';          // Server-side plot rendering
            $env['MPLCONFIGDIR'] = $workspacePath; // Force matplotlib to use workspace for config

            $runningFilePath = $resolvePath($node);
            $runningFileDir  = dirname($runningFilePath);

            $process = proc_open(
                [$python, '-u', $node->name],
                $descriptors,
                $pipes,
                $runningFileDir,
                $env
            );

            if (is_resource($process)) {
                $userInput = $request->input('stdin', '');
                fwrite($pipes[0], $userInput . "\n");
                fclose($pipes[0]);

                $output = stream_get_contents($pipes[1]);
                $error  = stream_get_contents($pipes[2]);

                fclose($pipes[1]);
                fclose($pipes[2]);
                $exitCode = proc_close($process);
            } else {
                $error = 'Failed to start Python process.';
                $exitCode = 1;
            }
        } finally {
            // REMOVED: File::deleteDirectory($sandboxPath). 
            // We leave the files on disk for persistence (SQLite/Data files).
        }

        $elapsed = (int) round((microtime(true) - $start) * 1000);

        $plots = [];
        $cleanOutput = preg_replace_callback(
            '/__PLOT_BASE64__:(.*?):__END_PLOT__/s',
            function ($m) use (&$plots) {
                $plots[] = $m[1];
                return '';
            },
            $output
        );
        $cleanOutput = trim($cleanOutput ?? '');

        IdeExecutionLog::create([
            'node_id'           => $node->id,
            'user_id'           => Auth::id(),
            'output'            => $cleanOutput,
            'error'             => $error,
            'exit_code'         => $exitCode,
            'execution_time_ms' => $elapsed,
        ]);

        return response()->json([
            'output'            => $cleanOutput,
            'error'             => $error,
            'exit_code'         => $exitCode,
            'execution_time_ms' => $elapsed,
            'plots'             => $plots,
        ]);
    }

    private function getPlotShim(): string
    {
        return <<<'PYSHIM'
import sys as _sys, os as _os
_os.environ.setdefault('MPLBACKEND', 'Agg')
try:
    import matplotlib as _mpl
    import matplotlib.pyplot as _plt
    def _patched_show(*a, **kw):
        import io, base64
        for i, fig in enumerate(_plt.get_fignums()):
            _f = _plt.figure(fig)
            _buf = io.BytesIO()
            _f.savefig(_buf, format='png', bbox_inches='tight')
            _buf.seek(0)
            _b64 = base64.b64encode(_buf.read()).decode()
            print(f"__PLOT_BASE64__:{_b64}:__END_PLOT__")
        _plt.close('all')
    _plt.show = _patched_show
except ImportError:
    pass
PYSHIM;
    }

    private function getPathShim(string $sandboxPath): string
    {
        $sp = addslashes(str_replace('\\', '/', $sandboxPath));
        return <<<PYSHIM2
import builtins as _builtins, os as _os, re as _re

_SANDBOX = r"{$sp}"

def _resolve(path):
    if isinstance(path, str) and path.startswith('/'):
        relative = path.lstrip('/')
        resolved = _os.path.join(_SANDBOX, *relative.replace('/', _os.sep).split(_os.sep))
        return resolved
    return path

_orig_open = _builtins.open
def _patched_open(file, *a, **kw):
    return _orig_open(_resolve(file), *a, **kw)
_builtins.open = _patched_open

_orig_exists  = _os.path.exists
_orig_isfile  = _os.path.isfile
_orig_isdir   = _os.path.isdir
_os.path.exists = lambda p: _orig_exists(_resolve(p))
_os.path.isfile = lambda p: _orig_isfile(_resolve(p))
_os.path.isdir  = lambda p: _orig_isdir(_resolve(p))
PYSHIM2;
    }

    private function resolvePython(): ?string
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            $windowsCandidates = ['py', 'python'];
            foreach ($windowsCandidates as $cmd) {
                $result = @shell_exec("{$cmd} --version 2>&1");
                if ($result && stripos($result, 'python') !== false) return $cmd;
            }
            return null;
        }

        $candidates = ['/usr/bin/python3', '/usr/local/bin/python3', '/usr/bin/python'];
        foreach ($candidates as $path) {
            if (is_executable($path)) return $path;
        }

        $found = trim(shell_exec('which python3 2>/dev/null') ?? '');
        return ($found !== '') ? $found : null;
    }

    private function authorizeNode(IdeNode $node): void
    {
        abort_unless($node->user_id === Auth::id(), 403);
    }
}