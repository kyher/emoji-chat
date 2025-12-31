<?php

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RemoveChannelUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $channel = $this->route('channel');
        $user = $this->route('user');

        // Only the channel owner can remove users
        if (!$channel || $channel->owner_id !== Auth::id()) {
            return false;
        }

        // Cannot remove the owner from the channel
        if ($user && $channel->owner_id === $user->id) {
            abort(403, 'Cannot remove the owner from the channel.');
        }

        // User must be a member of the channel
        if ($user && !$channel->users()->where('user_id', $user->id)->exists()) {
            abort(404, 'User is not a member of this channel.');
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
