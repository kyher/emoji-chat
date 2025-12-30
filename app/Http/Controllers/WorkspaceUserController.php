<?php

namespace App\Http\Controllers;

use App\Enum\WorkspaceUserRole;
use App\Http\Requests\AddWorkspaceUserRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;

class WorkspaceUserController extends Controller
{
    public function destroy($workspaceId, $userId): RedirectResponse
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

        $workspace->channels()->get()->map(fn($channel) => $channel->users()->detach($user));
        $workspace->users()->detach($user);
        return redirect()->back();
    }

    public function add(AddWorkspaceUserRequest $request): RedirectResponse
    {
        $workspace = Workspace::findOrFail($request->workspace);
        $user = User::where('email', $request->email)->first();
        if (!$workspace || !$user) {
            abort(404);
        }

        if ($workspace->users()->get()->contains($user->id)) {
            abort(403, 'User already added to workspace.');
        }

        $workspace->users()->attach($user, ['role' => WorkspaceUserRole::Member]);

        return redirect()->back();
    }
}
