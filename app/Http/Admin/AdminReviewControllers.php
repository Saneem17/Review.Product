namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']); // Assumes admin middleware exists
    }

    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'buyer']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by approval status
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApproval(ProductReview $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        $status = $review->is_approved ? 'approved' : 'unapproved';
        return back()->with('success', "Review has been {$status}.");
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review has been deleted.');
    }
}