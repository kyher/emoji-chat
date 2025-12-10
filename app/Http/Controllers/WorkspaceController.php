<?php

namespace App\Http\Controllers;

use App\Enum\WorkspaceUserRole;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Models\Workspace;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class WorkspaceController extends Controller
{
    public function store(StoreWorkspaceRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $workspace = Workspace::create([
                    'name' => $request->name,
                    'owner_id' => Auth::id(),
                ]);

                $workspace->users()->attach(Auth::id(), [
                    'role' => WorkspaceUserRole::Administrator,
                ]);
            });
        } catch (Exception $e) {
            Log::error($e);
            return redirect(route('dashboard'))->withErrors('Could not create workspace');
        }

        return redirect(route('dashboard'));
    }

    public function destroy(Workspace $workspace): Response
    {
        if (Auth::id() !== $workspace->owner_id) {
            return redirect(route('dashboard'))->withErrors('Could not delete workspace');
        }

        try {
            DB::transaction(function () use ($workspace) {
                $workspace->users()->detach();
                $workspace->delete();
            });
        } catch (Exception $e) {
            Log::error($e);
            return redirect(route('dashboard'))->withErrors('Could not delete workspace');
        }

        return redirect(route('dashboard'));
    }

    public function view(Workspace $workspace): InertiaResponse|RedirectResponse
    {
        if (!$workspace->users->contains(Auth::id())) {
            return redirect(route('dashboard'))->withErrors('Could not access workspace');
        }

        return Inertia::render('workspace/ViewWorkspace', [
            'workspace' => $workspace->load('channels'),
        ]);
    }
}
