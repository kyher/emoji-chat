<?php

namespace Tests\Feature\Settings;

use App\Enum\WorkspaceUserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class StoreWorkspaceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_workspace_can_be_stored()
    {
        $user = User::factory()->create();

        $name = $this->faker()->word();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('workspace.store'), [
                'name' => $name
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('workspaces', [
            'name' => $name,
            'owner_id' => $user->id
        ]);
        $this->assertDatabaseHas('workspace_users', [
            'user_id' => $user->id,
            'role' => WorkspaceUserRole::Administrator
        ]);
    }

    public function test_name_is_required()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('workspace.store'), []);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field is required.')
        );
    }

    public function test_name_is_cannot_be_empty()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('workspace.store'), [
                'name' => ''
            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field is required.')
        );
    }

    public function test_name_is_cannot_be_more_than_255_chars()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('workspace.store'), [
                'name' => $this->faker->sentence(256)
            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field must not be greater than 255 characters.')
        );
    }
}
