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

class StoreMessageTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private User $user;
    private Workspace $workspace;
    private Channel $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->workspace = WorkspaceFactory::new()->ownedBy($this->user)->create();
        $this->channel = ChannelFactory::new()->create([
            'name' => 'general',
            'owner_id' => $this->user->getKey(),
            'workspace_id' => $this->workspace->getKey()
        ]);
        $this->channel->users()->attach($this->user);
    }

    public function test_message_can_be_stored()
    {
        $content = $this->faker()->sentence();

        $response = $this
            ->actingAs($this->user)
            ->followingRedirects()
            ->post(route('message.store'), [
                'content' => $content,
                'channel_id' => $this->channel->getKey()
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'content' => $content,
            'channel_id' => $this->channel->getKey(),
            'user_id' => $this->user->getKey()
        ]);
    }

    public function test_only_channel_users_can_be_store_message()
    {
        $otherUser = User::factory()->create();
        $content = $this->faker()->sentence();

        $response = $this
            ->actingAs($otherUser)
            ->followingRedirects()
            ->post(route('message.store'), [
                'content' => $content,
                'channel_id' => $this->channel->getKey()
            ]);

        $response->assertStatus(403);
    }
}
