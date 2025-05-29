<?php

use App\Http\Controllers\OcrController;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', [OcrController::class, 'index'])->name('ocr.form');
Route::get('/batch-status/{batchId}', [OcrController::class, 'batchStatus'])->name('ocr.batch-status');
Route::post('/ocr', [OcrController::class, 'process'])->name('ocr.process');

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'id'])) abort(400);
    session(['locale' => $locale]);
    App::setLocale($locale);
    return redirect()->back();
});


Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate([
        'email' => $googleUser->getEmail(),
    ], [
        'name' => $googleUser->getName(),
        'google_id' => $googleUser->getId(),
        'avatar' => $googleUser->getAvatar(),
    ]);

    Auth::login($user);

    return redirect('/'); // atau route tujuan setelah login
});

Route::post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

