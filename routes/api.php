<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Middleware\{CheckAccessToken, CheckClientAccessToken, CheckUserAccessToken};

// --- for command generated routes ---
if (file_exists(__DIR__ . '/controllers_routes.php')) {
    require __DIR__ . '/controllers_routes.php';
}

// --- manually added routes ---

Route::get('/api/example/public', [ExampleController::class, 'public'])->name('api_public');

Route::get('/api/example/public2', [ExampleController::class, 'public2'])->name('api_public2');

Route::middleware(CheckAccessToken::class)->group(function () {
    Route::get('/api/example/bearer-protected', [ExampleController::class, 'bearerProtected'])->name('api_bearer_protected');
});

Route::middleware(CheckClientAccessToken::class)->group(function () {
    Route::get('/api/example/client-bearer-protected', [ExampleController::class, 'clientBearerProtected'])->name('api_client_bearer_protected');
});

Route::middleware(CheckUserAccessToken::class)->group(function () {
    Route::get('/api/example/user-bearer-protected', [ExampleController::class, 'userBearerProtected'])->name('api_user_bearer_protected');
});