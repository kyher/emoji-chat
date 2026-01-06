<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChannelUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceUserController;
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
    Route::prefix('channel')->group(function () {
        Route::post('/store', [ChannelController::class, 'store'])->name('channel.store');
        Route::delete('/{channel}/destroy', [ChannelController::class, 'destroy'])->name('channel.destroy');
        Route::get('/{channel}/view', [ChannelController::class, 'view'])->name('channel.view');
        Route::post('/{channel}/user/add', [ChannelUserController::class, 'add'])->name('channel.user.add');
        Route::delete('/{channel}/user/{user}/destroy', [ChannelUserController::class, 'destroy'])->name('channel.user.destroy');

        Route::prefix('message')->group(function () {
            Route::post('/store', [MessageController::class, 'store'])->name('message.store');
        });
    });
    Route::post('/store', [WorkspaceController::class, 'store'])->name('workspace.store');
    Route::delete('/{workspace}/destroy', [WorkspaceController::class, 'destroy'])->name('workspace.destroy');
    Route::get('/{workspace}/view', [WorkspaceController::class, 'view'])->name('workspace.view');
    Route::get('/{workspace}/edit', [WorkspaceController::class, 'edit'])->name('workspace.edit');
    Route::post('/{workspace}/edit', [WorkspaceController::class, 'editStore'])->name('workspace.edit.store');
    Route::delete('/{workspace}/{user}/destroy', [WorkspaceUserController::class, 'destroy'])->name('workspace.user.destroy');
    Route::post('/{workspace}/user/add', [WorkspaceUserController::class, 'add'])->name('workspace.user.add');
})->middleware(['auth', 'verified']);


require __DIR__ . '/settings.php';
