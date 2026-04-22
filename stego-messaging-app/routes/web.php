<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Storage;

Route::get('/test-upload', function () {
    $storage = new \Google\Cloud\Storage\StorageClient([
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'keyFile' => json_decode(file_get_contents(storage_path('app/firebase-auth.json')), true)
    ]);

    try {
        $bucket = $storage->bucket(env('FIREBASE_STORAGE_BUCKET'));
        $bucket->upload('Direct Google Test', [
            'name' => 'test-from-google-client.txt'
        ]);
        return "SUCCESS: File uploaded via direct Google Client!";
    } catch (\Exception $e) {
        return "DETAILED ERROR: " . $e->getMessage();
    }
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Chats page — uses our own layout so navigation stays visible
Route::get('/chats', function () {
    return view('chats');
})->middleware(['auth', 'verified'])
  ->name('chats')
  ->name('wirechat.chats.chats'); // This adds the second name the package wants

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';