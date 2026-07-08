<?php

use App\Http\Controllers\ProfileController;
use App\Services\SteganoService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StegoController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/init-super-admin-xyz99', function () {
    try {
        // Look for the specific Gmail address instead
        $adminEmail = 'stegchatadmin@gmail.com';

        if (User::where('email', $adminEmail)->exists()) {
            return 'Admin account already exists.';
        }

        User::create([
            'name' => 'Admin',
            'email' => $adminEmail,
            'password' => Hash::make('YourSuperSecurePassword123'), // Make sure to change this!
        ]);

        return 'Admin account created successfully with ' . $adminEmail;

    } catch (\Exception $e) {
        return 'Error creating admin: ' . $e->getMessage();
    }
});

Route::get('/debug-db', function () {
    try {
        return response()->json([
            'current_connection_driver' => DB::getDefaultConnection(),
            'database_name' => DB::connection()->getDatabaseName(),
            'total_users' => \App\Models\User::truncate(),
            'all_users' => \App\Models\User::select('id', 'name', 'email', 'created_at')->get(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/test-mail', function () {
    try {
        Mail::raw('Hey! If you are reading this, your Laravel Gmail SMTP integration is 100% working!', function ($message) {
            $message->to('your_personal_email@gmail.com') // Put your actual personal email here
                    ->subject('Laravel Gmail Test Delivery');
        });
        return 'Email sent successfully! Check your inbox (and spam folder).';
    } catch (\Exception $e) {
        return 'Mail failed to send. Error: ' . $e->getMessage();
    }
});

Route::post('/stego/extract', [StegoController::class, 'extract'])->name('stego.extract');

// Add this to routes/web.php temporarily
Route::get('/stegano-test', function () {
    $service = app(App\Services\SteganoService::class);
    
    // 1. Path to the image you just sent (check your storage/app/public/chats folder)
    // Or download the image from your chat UI and put it in public/test.png
    $imagePath = public_path('test.png'); 

    if (!file_exists($imagePath)) return "Image not found. Upload a sent image to public/test.png";

    try {
        // 2. Extract the scrambled bits
        $extractedScrambled = $service->extract($imagePath);
        
        // 3. Try to decrypt it
        $decrypted = Illuminate\Support\Facades\Crypt::decryptString($extractedScrambled);
        
        return "Success! Secret Message: " . $decrypted;
    } catch (\Exception $e) {
        return "Extraction failed or data is corrupted. Error: " . $e->getMessage();
    }
});

Route::get('/stego-test', function () {
    $service = new SteganoService();
    
    // 1. Path to a sample image in your public folder
    $inputPath = public_path('Picture11.png'); 
    $outputPath = public_path('stego_result.png');
    $secretMessage = "This is a hidden message for my FYP!";

    try {
        // 2. Run Embedding
        $service->embed($inputPath, $secretMessage, $outputPath);
        echo "<h3>1. Embedding Successful!</h3>";
        echo "Original: test.jpg <br> Result: stego_result.png (Check your public folder)<br>";

        // 3. Run Extraction
        $extracted = $service->extract($outputPath);
        echo "<h3>2. Extraction Result:</h3>";
        echo "Extracted Text: <strong>" . ($extracted ?? "Nothing found") . "</strong>";
        
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Livewire\Dashboard::class)->middleware(['auth', 'verified'])->name('dashboard');

// Chats page — uses our own layout so navigation stays visible
Route::get('/chats', function () {
    return view('chats');
})->middleware(['auth', 'verified'])
  ->name('chats')
  ->name('wirechat.chats.chats'); // This adds the second name the package wants
  // Alias the route name wirechat is searching for directly to your workspace URL
Route::get('/chat', \App\Livewire\CustomChat::class)->name('wirechat.chats.chats');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';