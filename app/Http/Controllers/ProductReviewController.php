namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Http\Requests\StoreProductReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function store(StoreProductReviewRequest $request, Product $product)
    {
        // Check if buyer is trying to review their own product
        if (Auth::id() === $product->seller_id) {
            return back()->with('error', 'You cannot review your own product.');
        }

        // TODO: Add purchase verification
        // Check if user has purchased this product (requires order_items table)
        // $hasPurchased = OrderItem::where('product_id', $product->id)
        //     ->whereHas('order', function($q) {
        //         $q->where('buyer_id', Auth::id())
        //           ->whereIn('status', ['completed', 'delivered']);
        //     })->exists();
        // 
        // if (!$hasPurchased) {
        //     return back()->with('error', 'You can only review products you have purchased.');
        // }

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('product_id', $product->id)
            ->where('buyer_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product. You can edit your existing review.');
        }

        ProductReview::create([
            'product_id' => $product->id,
            'buyer_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Your review has been submitted successfully!');
    }

    public function update(StoreProductReviewRequest $request, ProductReview $review)
    {
        // Ensure buyer can only update their own review
        if ($review->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Your review has been updated successfully!');
    }

    public function destroy(ProductReview $review)
    {
        // Ensure buyer can only delete their own review
        if ($review->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Your review has been deleted successfully!');
    }
}