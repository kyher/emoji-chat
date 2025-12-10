<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Models\Workspace;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class StoreChannelTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private User $user;
    private Workspace $workspace;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->workspace = WorkspaceFactory::new()->ownedBy($this->user)->create();
    }

    public function test_channel_can_be_stored()
    {

        $name = $this->faker()->word();

        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'name' => $name,
                'workspace_id' => $this->workspace->getKey()
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('channels', [
            'name' => $name,
            'workspace_id' => $this->workspace->getKey(),
            'owner_id' => $this->user->getKey()
        ]);
        $this->assertDatabaseHas('channel_user', [
            'user_id' => $this->user->getKey(),
        ]);
    }

    public function test_name_is_required()
    {
        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'workspace_id' => $this->workspace->getKey()
            ]);


        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field is required.')
        );
    }

    public function test_name_is_cannot_be_empty()
    {
        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'name' => '',
                'workspace_id' => $this->workspace->getKey()

            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field is required.')
        );
    }

    public function test_name_is_cannot_be_more_than_255_chars()
    {
        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'name' => $this->faker->sentence(256),
                'workspace_id' => $this->workspace->getKey()
            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors.name')
                ->where('errors.name', 'The name field must not be greater than 255 characters.')
        );
    }

    public function test_invalid_workspace()
    {
        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'name' => $this->faker->sentence(256),
                'workspace_id' => 99999
            ]);

        $response->assertForbidden();
    }

    public function test_workspace_of_which_user_is_not_admin()
    {
        $workspace = WorkspaceFactory::new()->create();
        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('channel.store'), [
                'name' => $this->faker->sentence(256),
                'workspace_id' => $workspace->getKey()
            ]);

        $response->assertForbidden();
    }
}
