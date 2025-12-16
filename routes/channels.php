<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('channels.{id}', function ($user, $id) {
    $channel = \App\Models\Channel::find($id);
    return $channel->users()->get()->contains($user->id);
});
