use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\Admin\AdminReviewController;

// Public routes (product detail already exists)

// Buyer review routes
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])
        ->name('products.reviews.store');
    Route::patch('/reviews/{review}', [ProductReviewController::class, 'update'])
        ->name('reviews.update');
    Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});

// Admin review routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/reviews', [AdminReviewController::class, 'index'])
        ->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'toggleApproval'])
        ->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])
        ->name('reviews.destroy');
})