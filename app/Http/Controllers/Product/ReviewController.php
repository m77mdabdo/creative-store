<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function store(Request $request)
    {

        $validated = $request->validate([

            'product_id' => ['required','integer','exists:products,id'],

            'rating' => ['required','integer','min:1','max:5'],

            'title' => ['nullable','string','max:255'],

            'comment' => ['required','string','min:3','max:2000'],

        ]);


        $product = Product::findOrFail($validated['product_id']);


        // منع تكرار التعليق لنفس المستخدم
        if(Auth::check()){

            $alreadyReviewed = Review::where('product_id',$product->id)
                ->where('user_id',Auth::id())
                ->exists();

            if($alreadyReviewed){

                return back()->with('error',__('You already submitted a review for this product.'));

            }

        }


        try {
            Review::create([

                'product_id' => $product->id,

                'user_id' => Auth::id(),

                'rating' => $validated['rating'],

                'title' => $validated['title'] ?? null,

                'comment' => $validated['comment'],

                'is_approved' => true,

            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            // A second, simultaneous submit hit the (product_id, user_id) unique index.
            return back()->with('error', __('You already submitted a review for this product.'));
        }
        app(\App\Services\Dashboard\DashboardStatsService::class)->clear();


        return back()->with('success',__('Your review has been submitted successfully.'));

    }

}
