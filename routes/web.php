<?php

use App\Http\Controllers\ChannelController;
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
    Route::post('/store', [WorkspaceController::class, 'store'])->name('workspace.store');
    Route::delete('/{workspace}/destroy', [WorkspaceController::class, 'destroy'])->name('workspace.destroy');
    Route::get('/{workspace}/view', [WorkspaceController::class, 'view'])->name('workspace.view');

    Route::prefix('channel')->group(function () {
        Route::post('/store', [ChannelController::class, 'store'])->name('channel.store');
        Route::delete('/{channel}/destroy', [ChannelController::class, 'destroy'])->name('channel.destroy');
        Route::get('/{channel}/view', [ChannelController::class, 'view'])->name('channel.view');
    });
})->middleware(['auth', 'verified']);


require __DIR__ . '/settings.php';
