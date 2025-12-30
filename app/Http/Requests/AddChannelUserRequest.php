<?php

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddChannelUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $channelId = $this->channel;
        $channel = Channel::find($channelId);
        return $channel && $channel->owner_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => 'required|exists:channels,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
