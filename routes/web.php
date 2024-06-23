<?php

use App\Models\User;
use App\Livewire\Chat;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', function() {
    return view('dashboard', [
        'users' => User::where('id', '!=', auth()->user()->id)->get(),
    ]);
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/chat/{user}', Chat::class)->name('chat');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
