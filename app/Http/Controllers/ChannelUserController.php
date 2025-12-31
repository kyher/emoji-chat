<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChannelUserRequest;
use App\Models\Channel;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChannelUserController extends Controller
{
    public function add(AddChannelUserRequest $request): RedirectResponse
    {
        $channel = Channel::find($request->channel);
        $user = User::find($request->user_id);

        if (!$channel || !$user) {
            return redirect()->back()->withErrors('Invalid channel or user');
        }

        if ($channel->workspace->users->doesntContain($user)) {
            return redirect()->back()->withErrors('User must be a member of the workspace to be added to the channel');
        }

        if ($channel->users->contains($user)) {
            return redirect()->back()->withErrors('User is already a member of this channel');
        }

        try {
            DB::transaction(function () use ($channel, $user) {
                $channel->users()->attach($user);
            });
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors('Could not add user to channel');
        }

        return redirect()->back();
    }

    public function destroy($channelId, $userId): RedirectResponse
    {
        $channel = Channel::findOrFail($channelId);
        $user = User::findOrFail($userId);
        if (!$channel || !$user) {
            abort(404);
        }

        if ($channel->owner_id !== Auth::id()) {
            abort(403, 'Only the channel owner can remove users.');
        }

        if ($channel->owner_id === $user->id) {
            abort(403, 'Cannot remove the owner from the channel.');
        }

        $channel->users()->detach($user);
        return redirect()->back();
    }
}
