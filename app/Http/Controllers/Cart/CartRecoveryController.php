<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartRecovery;
use Illuminate\Http\RedirectResponse;
use App\Services\Cart\SavedCart;
use Illuminate\Support\Facades\Auth;

class CartRecoveryController extends Controller
{
    public function recover(string $token): RedirectResponse
    {
        $recovery = AbandonedCartRecovery::query()
            ->with(['cart', 'cart.items'])
            ->where('recovery_token', $token)
            ->firstOrFail();

        $reopened = $recovery->status === 'recovered' && $recovery->expires_at?->isFuture();

        if (!$recovery->isRecoverable() && !$reopened) {
            return redirect('/')
                ->with('error', __('This recovery link is no longer valid.'));
        }

        $cart = $recovery->cart;

        if (!$cart || !$cart->items()->exists()) {
            return redirect('/')
                ->with('error', __('This cart is no longer available.'));
        }

        if ($cart->user_id && Auth::check() && Auth::id() !== $cart->user_id) {
            return redirect('/')
                ->with('error', __('This cart belongs to another user.'));
        }

        if ($cart->user_id && !Auth::check()) {
            // Come back to this link after signing in.
            return redirect()->guest(route('login'))
                ->with('info', __('Please log in to restore your cart.'));
        }

        // Put the saved items back into this browser's cart.
        $savedCart = app(SavedCart::class);
        session()->put('cart', $savedCart->merge(session()->get('cart', []), $savedCart->restore($cart)));

        $cart->touch();

        $recovery->update([
            'status'       => 'recovered',
            'recovered_at' => now(),
        ]);

        return redirect()->route('cart.index')->with('success', __('Your cart has been recovered successfully.'));
    }
}
