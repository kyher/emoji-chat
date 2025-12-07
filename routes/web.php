<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('workspace')->group(function () {
    Route::post('workspaces', [WorkspaceController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
});

require __DIR__ . '/settings.php';
