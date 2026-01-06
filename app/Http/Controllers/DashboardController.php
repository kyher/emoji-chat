<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $workspaces = Auth::user()->workspaces()->with('users')->get();
        return Inertia::render('Dashboard', [
            'workspaces' =>  WorkspaceResource::collection($workspaces)->all()
        ]);
    }
}
