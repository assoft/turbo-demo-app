<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('attachments', function () {
        request()->validate([
            'attachment' => ['required', 'file'],
        ]);

        $path = request()->file('attachment')->store('trix-attachments', 'public');

        return [
            'image_url' => Storage::disk('public')->url($path),
        ];
    })->name('attachments.store');

    Route::get('mentions', [Controllers\MentionsController::class, 'index'])->name('mentions.index');

    Route::resource('posts', Controllers\PostsController::class);
    Route::get('posts/{post}/delete', [Controllers\PostsController::class, 'delete'])->name('posts.delete');

    Route::resource('entries.comments', Controllers\EntryCommentsController::class)->only(['index', 'create', 'store']);
    Route::resource('comments', Controllers\CommentsController::class)->only(['show', 'edit', 'update', 'destroy']);

    Route::resource('entries.reactions', Controllers\EntryReactionsController::class)->only(['index', 'create', 'store']);

    Route::prefix('merch')->group(function () {
        Route::get('/', [Controllers\ShopController::class, 'index'])->name('shop.index');
        Route::resource('carts', Controllers\CartsController::class)->only(['index']);
        Route::resource('cart-items', Controllers\CartItemsController::class)->only(['store', 'update', 'destroy']);
        Route::resource('checkout', Controllers\CheckoutsController::class)->only(['index', 'store']);
        Route::resource('orders', Controllers\OrdersController::class)->only(['index', 'show']);
    });

    Route::resource('tweets', Controllers\TweetsController::class);

    Route::get('trending', function () {
        sleep(2);
        return view('trending');
    })->name('trending');

    Route::get('livewire-integration', function () {
        return view('livewire-integration');
    })->name('livewire.integration');

    Route::get('notifications', function () {
        return view('notifications.index', [
            'notifications' => auth()->user()
                ->notifications()
                ->paginate(),
            'frame' => request()->input('notifications-frame', '') == "box" ? 'notifications-box' : 'notifications',
        ]);
    })->name('notifications.index');
});
