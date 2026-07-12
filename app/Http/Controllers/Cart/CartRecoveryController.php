<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartRecovery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CartRecoveryController extends Controller
{
    public function recover(string $token): RedirectResponse
    {
        $recovery = AbandonedCartRecovery::query()
            ->with(['cart', 'cart.items'])
            ->where('recovery_token', $token)
            ->firstOrFail();

        if (!$recovery->isRecoverable()) {
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
            return redirect()->route('login')
                ->with('info', __('Please log in to restore your cart.'));
        }

        $cart->touch();

        $recovery->update([
            'status'       => 'recovered',
            'recovered_at' => now(),
        ]);

        return redirect('/cart')->with('success', __('Your cart has been recovered successfully.'));
    }
}
