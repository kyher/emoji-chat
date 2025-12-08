<?php

namespace Tests\Feature\Settings;

use App\Enum\WorkspaceUserRole;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class RemoveWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_can_be_removed()
    {
        Carbon::setTestNow();
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->delete(route('workspace.destroy', ['workspace' => $workspace->id]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'deleted_at' => now()
        ]);
        $this->assertDatabaseHas('workspace_users', [
            'user_id' => $workspaceOwner->id,
            'workspace_id' => $workspace->id,
            'role' => WorkspaceUserRole::Administrator,
            'deleted_at' => now()
        ]);
    }

    public function test_workspace_cannot_be_removed_by_non_owner()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $otherUser = User::factory()->create();

        $response = $this
            ->actingAs($otherUser)
            ->followingRedirects()
            ->delete(route('workspace.destroy', ['workspace' => $workspace->id]));

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'Could not delete workspace')
        );
    }
}
