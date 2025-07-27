<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\UserProfileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::delete('/profile', [UserProfileController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route pour enregistrer un utilisateur
Route::post('/register', [AuthController::class, 'register']);

//Route pour renvoyer un nouvel email de vérification
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email déjà vérifié.'], 200);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Lien de vérification renvoyé.']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Route pour valider le lien de vérification

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    // Vérifier que le hash correspond bien à l'email
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if ($user->hasVerifiedEmail()) {
        // Rediriger même si déjà vérifié
        return redirect('http://localhost:5173/golle-frontend/dashboard')->with('verified', true);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    // Redirection vers frontend
    return redirect('http://localhost:5173/golle-frontend/dashboard')->with('verified', true);
})->middleware('signed')->name('verification.verify');


// Exemple de route protégée par la vérification
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });
     Route::get('/dashboard', [UserController::class, 'dashboard']);
});
Route::post('/golle-frontend/login', [AuthController::class, 'login']);
