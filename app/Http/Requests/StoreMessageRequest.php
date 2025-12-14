<?php

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $channelId = $this->input('channel_id');
        $channel = Channel::find($channelId);
        if (!$channel instanceof Channel) {
            return false;
        }
        return $channel->users()->get()->contains('id', Auth::id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // todo - emoji ONLY
            'content' => 'required|string|max:1000',
        ];
    }
}
