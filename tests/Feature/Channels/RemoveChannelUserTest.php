<?php

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveChannelUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_remove_user_from_channel()
    {
        $channel = Channel::factory()->create();
        $user = User::factory()->create();
        $channel->users()->attach($user);

        $response = $this->actingAs($channel->owner)->followingRedirects()->delete(route('channel.user.destroy', [$channel, $user]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('channel_user', [
            'channel_id' => $channel->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_remove_user_from_channel_as_non_owner()
    {
        $channel = Channel::factory()->create();
        $user = User::factory()->create();
        $channel->users()->attach($user);

        $response = $this->actingAs($user)->followingRedirects()->delete(route('channel.user.destroy', [$channel, $user]));

        $response->assertStatus(403);
    }

    public function test_removal_of_owner_from_channel()
    {
        $channel = Channel::factory()->create();
        $owner = $channel->owner;

        $response = $this->actingAs($owner)->followingRedirects()->delete(route('channel.user.destroy', [$channel, $owner]));

        $response->assertStatus(403);
    }
}
