?>

@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Product Details Section -->
    <div class="row">
        <div class="col-md-8">
            <h1>{{ $product->name }}</h1>
            
            <!-- Average Rating Display -->
            <div class="mb-3">
                <span class="text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->averageRating))
                            ★
                        @elseif($i - 0.5 <= $product->averageRating)
                            ⯨
                        @else
                            ☆
                        @endif
                    @endfor
                </span>
                <span class="ms-2">
                    <strong>{{ $product->averageRating }}</strong> 
                    ({{ $product->reviewCount }} {{ Str::plural('review', $product->reviewCount) }})
                </span>
            </div>

            <!-- Existing product info... -->
            <p>{{ $product->description }}</p>
            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
        </div>
    </div>

    <hr class="my-5">

    <!-- Reviews Section -->
    <div class="row">
        <div class="col-md-12">
            <h3>Customer Reviews</h3>

            @auth
                @php
                    $userReview = $product->reviews()->where('buyer_id', auth()->id())->first();
                    $isSeller = auth()->id() === $product->seller_id;
                @endphp

                @if($isSeller)
                    <div class="alert alert-info">
                        You cannot review your own product.
                    </div>
                @else
                    <!-- Review Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $userReview ? 'Edit Your Review' : 'Write a Review' }}
                            </h5>

                            <form action="{{ $userReview ? route('reviews.update', $userReview) : route('products.reviews.store', $product) }}" 
                                  method="POST">
                                @csrf
                                @if($userReview)
                                    @method('PATCH')
                                @endif

                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating *</label>
                                    <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                        <option value="">Select rating...</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" 
                                                {{ old('rating', $userReview->rating ?? '') == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ Str::plural('Star', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="comment" class="form-label">Your Review</label>
                                    <textarea name="comment" id="comment" rows="4" 
                                              class="form-control @error('comment') is-invalid @enderror" 
                                              placeholder="Share your thoughts about this product...">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    {{ $userReview ? 'Update Review' : 'Submit Review' }}
                                </button>

                                @if($userReview)
                                    <button type="button" class="btn btn-danger" 
                                            onclick="event.preventDefault(); document.getElementById('delete-review-form').submit();">
                                        Delete Review
                                    </button>
                                @endif
                            </form>

                            @if($userReview)
                                <form id="delete-review-form" action="{{ route('reviews.destroy', $userReview) }}" 
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <a href="{{ route('login') }}">Log in</a> to leave a review.
                </div>
            @endguest

            <!-- Reviews List -->
            <div class="reviews-list">
                @forelse($product->approvedReviews()->with('buyer')->latest()->paginate(10) as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $review->buyer->name }}</h6>
                                    <div class="text-warning mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            @if($review->comment)
                                <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                @endforelse

                {{ $product->approvedReviews()->paginate(10)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<?php