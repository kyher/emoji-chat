<?php

namespace Tests\Feature\Workspaces;

use App\Enum\WorkspaceUserRole;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class EditWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_can_be_edited()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->post(route('workspace.edit.store', ['workspace' => $workspace->id]), [
                'name' => 'New Workspace Name',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'name' => 'New Workspace Name',
        ]);
    }

    public function test_workspace_cannot_be_edited_by_non_administrator()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $otherUser = User::factory()->create();
        $workspace->users()->attach($otherUser, ['role' => WorkspaceUserRole::Member]);

        $response = $this
            ->actingAs($otherUser)
            ->followingRedirects()
            ->post(route('workspace.edit.store', ['workspace' => $workspace->id]), [
                'name' => 'New Workspace Name',
            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'Could not edit workspace')
        );
    }
}
