<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;

class WorkspaceUserController extends Controller
{
    public function destroy($workspaceId, $userId): \Illuminate\Http\RedirectResponse
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = User::findOrFail($userId);
        if (!$workspace || !$user) {
            abort(404);
        }

        if (!$workspace->administrators->pluck('id')->contains(auth()->id())) {
            abort(403, 'Only workspace administrators can remove users.');
        }

        if ($workspace->owner_id === $user->id) {
            abort(403, 'Cannot remove the owner from the workspace.');
        }

        $workspace->users()->detach($user);
        return redirect()->back();
    }
}
