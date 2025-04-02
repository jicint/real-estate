<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Remove or comment out any existing routes
// Route::get('/', function () {
//     return view('welcome');
// });

// This route must come BEFORE the catch-all route
Route::get('/compare/{token}', function ($token) {
    try {
        $data = json_decode(base64_decode($token), true);
        if (!$data || !isset($data['properties'])) {
            throw new \RuntimeException('Invalid comparison data');
        }

        return view('welcome', [
            'comparisonData' => $data
        ]);
    } catch (\Throwable $th) {
        return redirect('/')->with('error', 'Invalid comparison link');
    }
})->where('token', '.+');

// This should be the last route
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/compare/{id}', function ($id) {
    return view('properties.compare', ['comparisonId' => $id]);
});

Route::get('/test-email', function () {
    try {
        $testData = [
            'name' => 'Test Comparison',
            'properties' => [1, 2, 3],
            'notes' => 'Test notes'
        ];

        Mail::to('test@example.com')->send(new \App\Mail\ShareComparison(
            $testData,
            'http://localhost:8000/test',
            'Test message'
        ));

        return [
            'success' => true,
            'message' => 'Test email sent! Check Mailtrap inbox.'
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
});

require __DIR__.'/auth.php';
