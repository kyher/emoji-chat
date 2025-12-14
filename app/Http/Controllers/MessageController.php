<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Channel;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request)
    {
        $channel = Channel::find($request->channel_id);
        $channel->messages()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->back();
    }
}
