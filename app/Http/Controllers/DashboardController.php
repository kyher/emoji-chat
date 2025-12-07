<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $workspaces = Auth::user()->workspaces()->pluck('id', 'name');
        return Inertia::render('Dashboard', compact('workspaces'));
    }
}
