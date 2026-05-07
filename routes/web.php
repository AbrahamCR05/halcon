<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

// ── Pública ────────────────────────────────────────────────────────────────
Route::get('/', fn() => inertia('Public/Lookup'))->name('lookup');
Route::post('/lookup', [OrderController::class, 'lookup'])->name('lookup.check');

// ── Auth ───────────────────────────────────────────────────────────────────
Route::get('/login',  fn() => inertia('Auth/Login'))->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Protegidas ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pedidos
    Route::get('/orders',                    [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders',                   [OrderController::class, 'store'])->name('orders.store')->middleware('role:admin,sales');
    Route::put('/orders/{order}',            [OrderController::class, 'update'])->name('orders.update')->middleware('role:admin,sales');
    Route::patch('/orders/{order}/status',   [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('/orders/{order}',         [OrderController::class, 'destroy'])->name('orders.destroy')->middleware('role:admin,sales');
    Route::patch('/orders/{order}/restore',  [OrderController::class, 'restore'])->name('orders.restore')->middleware('role:admin');
    Route::get('/orders/deleted',            [OrderController::class, 'deleted'])->name('orders.deleted');
    Route::post('/orders/{order}/photo',     [OrderController::class, 'uploadPhoto'])->name('orders.photo')->middleware('role:admin,route');

    // Usuarios (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',          [UserController::class, 'index'])->name('users.index');
        Route::post('/users',         [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',   [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',[UserController::class, 'destroy'])->name('users.destroy');
    });
});