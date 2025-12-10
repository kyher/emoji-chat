<?php

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workspaceId = $this->input('workspace_id');
        $workspace = Workspace::find($workspaceId);
        if (!$workspace instanceof Workspace) {
            return false;
        }
        return $workspace->administrators()->get()->contains('id', Auth::id());
    }

    public function rules(): array
    {
        return [
            'workspace_id' => 'required|exists:workspaces,id',
            'name' => 'required|string|min:1|max:255'
        ];
    }
}
