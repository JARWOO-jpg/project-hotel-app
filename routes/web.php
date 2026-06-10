<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Guest Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/check-availability', [HomeController::class, 'checkAvailability'])->name('check-availability');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Guest Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/status', [BookingController::class, 'status'])->name('booking.status');
    Route::get('/booking/{booking}/invoice', [BookingController::class, 'invoice'])->name('booking.invoice');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('booking.my-bookings');
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
});

/*
|--------------------------------------------------------------------------
| Receptionist Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:receptionist,admin'])->prefix('receptionist')->name('receptionist.')->group(function () {
    Route::get('/', [ReceptionistController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [ReceptionistController::class, 'searchBooking'])->name('search');
    Route::get('/check-in/{booking}', [ReceptionistController::class, 'checkIn'])->name('check-in');
    Route::post('/check-in/{booking}', [ReceptionistController::class, 'processCheckIn'])->name('process-check-in');
    Route::post('/service/{booking}', [ReceptionistController::class, 'addService'])->name('add-service');
    Route::get('/check-out/{booking}', [ReceptionistController::class, 'checkOut'])->name('check-out');
    Route::post('/check-out/{booking}', [ReceptionistController::class, 'processCheckOut'])->name('process-check-out');
    Route::get('/guest-bill/{booking}', [ReceptionistController::class, 'guestBill'])->name('guest-bill');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Room CRUD
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
    Route::get('/rooms/create', [AdminController::class, 'createRoom'])->name('rooms.create');
    Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [AdminController::class, 'editRoom'])->name('rooms.edit');
    Route::put('/rooms/{room}', [AdminController::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room}', [AdminController::class, 'deleteRoom'])->name('rooms.delete');

    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/waiting-list', [AdminController::class, 'handleWaitingList'])->name('bookings.waiting-list');

    // User CRUD
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // CMS (Content Management System)
    Route::get('/cms', [AdminController::class, 'cms'])->name('cms');
    Route::post('/cms', [AdminController::class, 'updateCms'])->name('cms.update');
});
