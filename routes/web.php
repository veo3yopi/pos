<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pos', function () {
    if (! Auth::check()) {
        return redirect()->route('pos.login');
    }

    return view('pos.index');
})->name('pos.index');

Route::get('/pos/login', function () {
    if (Auth::check()) {
        return redirect()->route('pos.index');
    }

    return view('pos.login');
})->name('pos.login');

Route::post('/pos/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->route('pos.index');
    }

    return back()->withErrors([
        'email' => 'Email atau password tidak valid.',
    ])->onlyInput('email');
})->name('pos.login.submit');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('pos.login');
})->name('logout');
