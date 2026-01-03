namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::take(5)->get();
        $buyers = User::where('role', 'buyer')->take(10)->get();

        foreach ($products as $product) {
            // Each product gets 2-5 random reviews
            $reviewCount = rand(2, 5);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $buyer = $buyers->random();
                
                // Skip if buyer is the seller
                if ($buyer->id === $product->seller_id) {
                    continue;
                }

                ProductReview::create([
                    'product_id' => $product->id,
                    'buyer_id' => $buyer->id,
                    'rating' => rand(3, 5),
                    'comment' => fake()->paragraph(),
                    'is_approved' => true,
                ]);
            }
        }
    }
}