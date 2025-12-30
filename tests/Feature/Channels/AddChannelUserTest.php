<?php

namespace Tests\Feature\Channels;

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Database\Factories\ChannelFactory;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class AddChannelUserTest extends TestCase
{
    use RefreshDatabase;

    private Channel $channel;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->channel = ChannelFactory::new()->ownedBy($this->user)->create();
    }

    public function test_user_can_be_added_to_channel(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create();
        $this->channel->workspace->users()->attach($newUser, ['role' => 'member']);

        $response = $this->followingRedirects()->post(route('channel.user.add', $this->channel), [
            'user_id' => $newUser->id,
            'channel' => $this->channel->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('channel_user', [
            'user_id' => $newUser->id,
            'channel_id' => $this->channel->id,
        ]);
    }

    public function test_user_cannot_be_added_to_channel_if_not_a_member_of_workspace(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create();

        $response = $this->followingRedirects()->post(route('channel.user.add', $this->channel), [
            'user_id' => $newUser->id,
            'channel' => $this->channel->id,
        ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'User must be a member of the workspace to be added to the channel')
        );
        $this->assertDatabaseMissing('channel_user', [
            'user_id' => $newUser->id,
            'channel_id' => $this->channel->id,
        ]);
    }

    public function test_user_cannot_be_added_to_channel_twice(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create();
        $this->channel->workspace->users()->attach($newUser, ['role' => 'member']);
        $this->channel->users()->attach($newUser);

        $response = $this->followingRedirects()->post(route('channel.user.add', $this->channel), [
            'user_id' => $newUser->id,
            'channel' => $this->channel->id,
        ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'User is already a member of this channel')
        );
    }
}
