<?php

namespace Tests\Feature\Channels;

use App\Models\User;
use Database\Factories\ChannelFactory;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class RemoveChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_channel_can_be_removed()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $channel = ChannelFactory::new()->for($workspace)->ownedBy($workspaceOwner)->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->delete(route('channel.destroy', ['channel' => $channel->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('channels', [
            'id' => $channel->id,
        ]);
    }

    public function test_channel_cannot_be_removed_by_non_owner()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $channel = ChannelFactory::new()->for($workspace)->ownedBy($workspaceOwner)->create();

        $otherUser = User::factory()->create();

        $response = $this
            ->actingAs($otherUser)
            ->followingRedirects()
            ->delete(route('channel.destroy', ['channel' => $channel->id]));

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'You do not have permission to delete this channel')
        );
    }
}
