<?php

namespace Tests\Feature;

use App\Models\IdeExecutionLog;
use App\Models\IdeNode;
use App\Models\IdeWorkspace;
use App\Services\PythonSandboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class IdeWorkspaceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_initialization_is_idempotent_and_user_scoped(): void
    {
        $student = $this->roleUser();

        $this->authenticateAs($student)->get(route('ide.index'))->assertOk();
        $this->post(route('ide.workspace.initialize'))->assertRedirect(route('ide.index'));
        $this->post(route('ide.workspace.initialize'))->assertRedirect(route('ide.index'));

        $this->assertDatabaseCount('ide_workspaces', 1);
        $this->assertDatabaseHas('ide_workspaces', [
            'user_id' => $student->id,
            'name' => $student->name . "'s Workspace",
        ]);
    }

    public function test_student_can_create_rename_save_move_and_list_workspace_nodes(): void
    {
        [$student, $workspace] = $this->workspace();

        $folderId = $this->authenticateAs($student)->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'type' => 'folder',
            'name' => 'Analysis',
        ])->assertCreated()->json('node.id');

        $fileId = $this->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'parent_id' => $folderId,
            'type' => 'file',
            'name' => 'main.py',
            'content' => 'print(1)',
        ])->assertCreated()
            ->assertJsonPath('node.language', 'python')
            ->json('node.id');

        $file = IdeNode::findOrFail($fileId);
        $this->patchJson(route('ide.nodes.rename', $file), ['name' => 'report.sql'])
            ->assertOk()
            ->assertJsonPath('node.language', 'sql');
        $this->patchJson(route('ide.nodes.save', $file), ['content' => 'SELECT 1;'])
            ->assertOk()
            ->assertJsonPath('saved', true);
        $this->patchJson(route('ide.nodes.move', $file), ['parent_id' => null, 'new_name' => 'report.sql'])
            ->assertOk()
            ->assertJsonPath('moved', true);

        $this->getJson(route('ide.tree'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Analysis', 'type' => 'folder'])
            ->assertJsonFragment(['name' => 'report.sql', 'content' => 'SELECT 1;']);

        $this->assertDatabaseHas('ide_nodes', [
            'id' => $fileId,
            'parent_id' => null,
            'language' => 'sql',
            'content' => 'SELECT 1;',
        ]);
    }

    public function test_node_validation_rejects_traversal_duplicates_and_folder_content_without_partial_changes(): void
    {
        [$student, $workspace] = $this->workspace();
        $folder = $this->node($student->id, $workspace->id, [
            'type' => 'folder',
            'name' => 'Folder',
            'content' => null,
            'language' => null,
        ]);
        $file = $this->node($student->id, $workspace->id, ['name' => 'existing.py']);

        $this->authenticateAs($student)->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'type' => 'file',
            'name' => '../secret.py',
        ])->assertUnprocessable()->assertJsonValidationErrors('name');

        $this->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'type' => 'file',
            'name' => 'existing.py',
        ])->assertConflict()->assertJsonPath('collision', true);

        $this->putJson(route('ide.nodes.update', $folder), ['content' => 'not allowed'])
            ->assertUnprocessable();
        $this->patchJson(route('ide.nodes.rename', $file), ['name' => 'bad/name.py'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseCount('ide_nodes', 2);
        $this->assertNull($folder->fresh()->content);
        $this->assertSame('existing.py', $file->fresh()->name);
    }

    public function test_student_cannot_read_or_mutate_another_students_workspace(): void
    {
        [$owner, $workspace] = $this->workspace();
        $intruder = $this->roleUser();
        $node = $this->node($owner->id, $workspace->id);

        $this->authenticateAs($intruder)->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'type' => 'file',
            'name' => 'stolen.py',
        ])->assertNotFound();
        $this->patchJson(route('ide.nodes.save', $node), ['content' => 'tampered'])->assertForbidden();
        $this->patchJson(route('ide.nodes.rename', $node), ['name' => 'tampered.py'])->assertForbidden();
        $this->deleteJson(route('ide.nodes.delete', $node))->assertForbidden();

        $this->assertSame('main.py', $node->fresh()->name);
        $this->assertSame('print(1)', $node->fresh()->content);
    }

    public function test_folder_cannot_be_moved_into_itself_or_a_descendant(): void
    {
        [$student, $workspace] = $this->workspace();
        $parent = $this->node($student->id, $workspace->id, ['type' => 'folder', 'name' => 'Parent']);
        $child = $this->node($student->id, $workspace->id, [
            'parent_id' => $parent->id,
            'type' => 'folder',
            'name' => 'Child',
        ]);

        $this->authenticateAs($student)
            ->patchJson(route('ide.nodes.move', $parent), ['parent_id' => $parent->id])
            ->assertUnprocessable();
        $this->patchJson(route('ide.nodes.move', $parent), ['parent_id' => $child->id])
            ->assertUnprocessable();

        $this->assertNull($parent->fresh()->parent_id);
        $this->assertSame($parent->id, $child->fresh()->parent_id);
    }

    public function test_deleting_folder_cascades_to_descendants_and_execution_logs(): void
    {
        [$student, $workspace] = $this->workspace();
        $folder = $this->node($student->id, $workspace->id, ['type' => 'folder', 'name' => 'Folder']);
        $file = $this->node($student->id, $workspace->id, ['parent_id' => $folder->id]);
        IdeExecutionLog::create([
            'node_id' => $file->id,
            'user_id' => $student->id,
            'output' => '1',
            'exit_code' => 0,
            'execution_time_ms' => 1,
        ]);

        $this->authenticateAs($student)
            ->deleteJson(route('ide.nodes.delete', $folder))
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseCount('ide_nodes', 0);
        $this->assertDatabaseCount('ide_execution_logs', 0);
    }

    public function test_python_file_execution_returns_sandbox_result_and_records_log(): void
    {
        [$student, $workspace] = $this->workspace();
        $file = $this->node($student->id, $workspace->id);
        $sandbox = Mockery::mock(PythonSandboxService::class);
        $sandbox->shouldReceive('runWorkspace')->once()->andReturn([
            'stdout' => '42',
            'stderr' => '',
            'exit_code' => 0,
            'execution_time_ms' => 12,
            'plots' => [],
        ]);
        $this->app->instance(PythonSandboxService::class, $sandbox);

        $this->authenticateAs($student)
            ->postJson(route('ide.nodes.run', $file), ['content' => 'print(42)'])
            ->assertOk()
            ->assertJsonPath('output', '42')
            ->assertJsonPath('exit_code', 0);

        $this->assertDatabaseHas('ide_execution_logs', [
            'node_id' => $file->id,
            'user_id' => $student->id,
            'output' => '42',
            'exit_code' => 0,
        ]);
    }

    public function test_python_input_prompt_is_returned_without_recording_a_false_failure(): void
    {
        [$student, $workspace] = $this->workspace();
        $file = $this->node($student->id, $workspace->id, [
            'name' => 'practice.py',
            'content' => 'name = input("Enter your name: ")',
        ]);
        $sandbox = Mockery::mock(PythonSandboxService::class);
        $sandbox->shouldReceive('runWorkspace')
            ->once()
            ->withArgs(fn ($workspacePath, $entryPath, $code, $stdin, $options): bool =>
                is_string($workspacePath)
                && $entryPath === 'practice.py'
                && $code === 'name = input("Enter your name: ")'
                && $stdin === ''
                && ($options['interactive_input'] ?? null) === true
                && str_starts_with((string) ($options['session']['key'] ?? ''), 'ide-user-'))
            ->andReturn([
                'stdout' => 'Enter your name:',
                'stderr' => '',
                'exit_code' => 75,
                'execution_time_ms' => 4,
                'plots' => [],
                'input_required' => true,
                'input_prompt' => 'Enter your name: ',
            ]);
        $this->app->instance(PythonSandboxService::class, $sandbox);

        $this->authenticateAs($student)
            ->postJson(route('ide.nodes.run', $file), [
                'content' => 'name = input("Enter your name: ")',
                'stdin' => '',
            ])
            ->assertOk()
            ->assertJsonPath('input_required', true)
            ->assertJsonPath('input_prompt', 'Enter your name: ')
            ->assertJsonPath('error', '');

        $this->assertDatabaseCount('ide_execution_logs', 0);
    }

    public function test_python_run_forwards_all_collected_input_values_and_records_completion(): void
    {
        [$student, $workspace] = $this->workspace();
        $file = $this->node($student->id, $workspace->id, [
            'name' => 'practice.py',
            'content' => 'name = input(); age = input(); print(name, age)',
        ]);
        $sandbox = Mockery::mock(PythonSandboxService::class);
        $sandbox->shouldReceive('runWorkspace')
            ->once()
            ->withArgs(fn ($workspacePath, $entryPath, $code, $stdin, $options): bool =>
                is_string($workspacePath)
                && $entryPath === 'practice.py'
                && $code === 'name = input(); age = input(); print(name, age)'
                && $stdin === "Ada\n21\n"
                && ($options['interactive_input'] ?? null) === true
                && str_starts_with((string) ($options['session']['key'] ?? ''), 'ide-user-'))
            ->andReturn([
                'stdout' => 'Ada 21',
                'stderr' => '',
                'exit_code' => 0,
                'execution_time_ms' => 6,
                'plots' => [],
                'input_required' => false,
                'input_prompt' => null,
            ]);
        $this->app->instance(PythonSandboxService::class, $sandbox);

        $this->authenticateAs($student)
            ->postJson(route('ide.nodes.run', $file), [
                'content' => 'name = input(); age = input(); print(name, age)',
                'stdin' => "Ada\n21\n",
            ])
            ->assertOk()
            ->assertJsonPath('output', 'Ada 21')
            ->assertJsonPath('input_required', false)
            ->assertJsonPath('exit_code', 0);

        $this->assertDatabaseHas('ide_execution_logs', [
            'node_id' => $file->id,
            'user_id' => $student->id,
            'output' => 'Ada 21',
            'exit_code' => 0,
        ]);
    }

    private function workspace(): array
    {
        $student = $this->roleUser();
        $workspace = IdeWorkspace::create(['user_id' => $student->id, 'name' => 'Workspace']);

        return [$student, $workspace];
    }

    private function node(int $userId, int $workspaceId, array $attributes = []): IdeNode
    {
        return IdeNode::create(array_merge([
            'workspace_id' => $workspaceId,
            'parent_id' => null,
            'user_id' => $userId,
            'type' => 'file',
            'name' => 'main.py',
            'content' => 'print(1)',
            'language' => 'python',
        ], $attributes));
    }
}
