<?php

namespace Tests\Feature\Channels;

use App\Models\User;
use Database\Factories\ChannelFactory;
use Database\Factories\WorkspaceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class EditChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_channel_can_be_edited()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $channel = ChannelFactory::new()->for($workspace)->ownedBy($workspaceOwner)->create();

        $response = $this
            ->actingAs($workspaceOwner)
            ->followingRedirects()
            ->post(route('channel.edit.store', ['channel' => $channel->id]), [
                'name' => 'Updated Channel Name',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('channels', [
            'id' => $channel->id,
            'name' => 'Updated Channel Name',
        ]);
    }

    public function test_channel_cannot_be_edited_by_non_owner()
    {
        $workspaceOwner = User::factory()->create();
        $workspace = WorkspaceFactory::new()->ownedBy($workspaceOwner)->create();
        $channel = ChannelFactory::new()->for($workspace)->ownedBy($workspaceOwner)->create();

        $otherUser = User::factory()->create();
        $workspace->users()->attach($otherUser, ['role' => 'member']);
        $channel->users()->attach($otherUser);

        $response = $this
            ->actingAs($otherUser)
            ->followingRedirects()
            ->post(route('channel.edit.store', ['channel' => $channel->id]), [
                'name' => 'Updated Channel Name',
            ]);

        $response->assertInertia(
            fn(Assert $page) => $page
                ->has('errors')
                ->where('errors.0', 'You do not have permission to edit this channel')
        );
    }
}
