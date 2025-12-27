<?php

namespace Tests\Feature\Workspaces;

use App\Enum\WorkspaceUserRole;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveWorkspaceUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_user_can_be_removed()
    {
        Carbon::setTestNow();
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $workspaceMember = User::factory()->create();
        $workspace->users()->attach($workspaceMember->id, ['role' => WorkspaceUserRole::Member]);


        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->delete(route('workspace.user.destroy', ['workspace' => $workspace->id, 'user' => $workspaceMember->id]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('workspace_users', [
            'user_id' => $workspaceMember->id,
            'workspace_id' => $workspace->id,
            'role' => WorkspaceUserRole::Member,
            'deleted_at' => now()
        ]);
    }

    public function test_workspace_user_cannot_be_removed_by_a_non_admin()
    {
        $workspace = WorkspaceFactory::new()->create();

        $nonAdmin = User::factory()->create();
        $workspace->users()->attach($nonAdmin->id, ['role' => WorkspaceUserRole::Member]);
        $workspaceMember = User::factory()->create();
        $workspace->users()->attach($workspaceMember->id, ['role' => WorkspaceUserRole::Member]);

        $response = $this
            ->actingAs($nonAdmin)
            ->followingRedirects()
            ->delete(route('workspace.user.destroy', ['workspace' => $workspace->id, 'user' => $workspaceMember->id]));

        $response->assertStatus(403);
    }

    public function test_workspace_owner_cannot_be_removed()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $workspaceMember = User::factory()->create();
        $workspace->users()->attach($workspaceMember->id, ['role' => WorkspaceUserRole::Member]);

        $response = $this
            ->actingAs($workspaceMember)
            ->followingRedirects()
            ->delete(route('workspace.user.destroy', ['workspace' => $workspace->id, 'user' => $workspaceOwner->id]));

        $response->assertStatus(403);
    }
}
