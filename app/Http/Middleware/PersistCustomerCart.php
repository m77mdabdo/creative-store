<?php

namespace App\Http\Middleware;

use App\Services\Cart\SavedCart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * After each request by a signed-in customer, save the session cart if it
 * changed. On the first request of a session (right after logging in) the
 * guest cart is merged with the cart saved earlier, so nothing is lost and
 * a cart started on the phone shows up on the laptop.
 */
class PersistCustomerCart
{
    private const FINGERPRINT = 'cart_saved_fingerprint';

    public function __construct(private readonly SavedCart $savedCart)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();

        if (! $user || ! $request->hasSession() || $request->routeIs('dashboard.*')) {
            return $response;
        }

        $session = $request->session();

        try {
            $cart = (array) $session->get('cart', []);

            if (! $session->has(self::FINGERPRINT)) {
                $saved = $this->savedCart->for($user);

                if ($saved) {
                    $cart = $this->savedCart->merge($cart, $this->savedCart->restore($saved));
                    $cart === [] ? $session->forget('cart') : $session->put('cart', $cart);
                }
            }

            $fingerprint = SavedCart::fingerprint($cart);

            if ($session->get(self::FINGERPRINT) !== $fingerprint) {
                $this->savedCart->persist($user, $cart, $session->getId());
                $session->put(self::FINGERPRINT, $fingerprint);
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $response;
    }
}
