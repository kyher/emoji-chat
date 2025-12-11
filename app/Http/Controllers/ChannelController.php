<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemoveChannelRequest;
use App\Http\Requests\StoreChannelRequest;
use App\Models\Channel;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChannelController extends Controller
{
    public function store(StoreChannelRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $channel = Channel::create([
                    'name' => $request->name,
                    'workspace_id' => $request->workspace_id,
                    'owner_id' => Auth::id()
                ]);

                $channel->users()->attach(Auth::id());
            });
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors('Could not create channel');
        }

        return redirect()->back();
    }

    public function destroy(Channel $channel): RedirectResponse
    {
        if (Auth::id() !== $channel->owner_id) {
            return redirect()->back()->withErrors('You do not have permission to delete this channel');
        }
        try {
            DB::transaction(function () use ($channel) {
                $channel->users()->detach();
                $channel->delete();
            });
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors('Could not delete channel');
        }

        return redirect()->back();
    }

    public function view(Channel $channel)
    {
        if (!$channel->users->contains(Auth::id())) {
            return redirect()->back()->withErrors('You do not have permission to view this channel');
        }

        return inertia('channel/ViewChannel', [
            'channel' => $channel,
            'workspace' => $channel->workspace,
        ]);
    }
}
