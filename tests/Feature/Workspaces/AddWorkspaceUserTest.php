<?php

namespace Tests\Feature\Workspaces;

use App\Enum\WorkspaceUserRole;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AddWorkspaceUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_user_can_be_added()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $userToAdd = User::factory()->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->post(route('workspace.user.add', ['workspace' => $workspace->id]), [
                'email' => $userToAdd->email
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('workspace_users', [
            'user_id' => $userToAdd->id,
            'workspace_id' => $workspace->id,
            'role' => WorkspaceUserRole::Member,
        ]);
    }

    public function test_workspace_user_cannot_be_added_by_a_non_admin()
    {
        $workspace = WorkspaceFactory::new()->create();

        $nonAdmin = User::factory()->create();
        $workspace->users()->attach($nonAdmin->id, ['role' => WorkspaceUserRole::Member]);

        $response = $this
            ->actingAs($nonAdmin)
            ->followingRedirects()
            ->post(route('workspace.user.add', ['workspace' => $workspace->id]), [
                'email' => fake()->safeEmail()
            ]);

        $response->assertStatus(403);
    }

    public function test_workspace_user_cannot_be_added_to_a_non_existent_workspace()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('workspace.user.add', ['workspace' => 999999]), [
                'email' => fake()->safeEmail()
            ]);

        $response->assertStatus(403);
    }

    public function test_adding_non_existent_user_is_invalid()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->post(route('workspace.user.add', ['workspace' => $workspace->id]), [
                'email' => fake()->safeEmail()
            ]);

        $response->assertInertia(
            fn(AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.email', 'The selected email is invalid.')
        );
    }
}
