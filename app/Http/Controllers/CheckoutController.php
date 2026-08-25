<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    private const TOKEN_KEY = 'checkout.token';

    public function create(CartService $cart): View|RedirectResponse
    {
        $snapshot = $cart->snapshot();
        if ($snapshot['items'] === []) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $token = (string) Str::uuid();
        session()->put(self::TOKEN_KEY, $token);

        return view('checkout.create', ['cart' => $snapshot, 'checkoutToken' => $token]);
    }

    public function store(CheckoutRequest $request, CartService $cart, OrderService $orders): RedirectResponse
    {
        $token = $request->string('checkout_token')->toString();
        $sessionToken = session(self::TOKEN_KEY);
        if (! is_string($sessionToken) || ! hash_equals($sessionToken, $token)) {
            return redirect()->route('checkout.create')->with('error', 'La sesión de checkout venció. Revisa los datos e inténtalo nuevamente.');
        }

        $tokenHash = hash('sha256', $token);
        $existing = Order::query()->where('checkout_token_hash', $tokenHash)->first();
        if ($existing) {
            $cart->clear();
            session()->forget(self::TOKEN_KEY);

            return redirect()->route('orders.show', $existing);
        }

        $snapshot = $cart->snapshot(false);
        if ($snapshot['items'] === [] || $snapshot['invalid_ids'] !== []) {
            return redirect()->route('cart.index')->with('error', 'Uno o más cursos ya no están disponibles. Revisa el carrito.');
        }

        try {
            $order = $orders->create($request->validated(), $cart, $token, $request->user()?->getKey());
        } catch (QueryException $exception) {
            $existing = Order::query()->where('checkout_token_hash', $tokenHash)->first();
            if ($existing) {
                return redirect()->route('orders.show', $existing);
            }

            Log::error('No se pudo crear la orden por un error de base de datos.', ['exception' => $exception::class]);
            return back()->withInput()->with('error', 'No fue posible crear la orden. Inténtalo nuevamente.');
        } catch (Throwable $exception) {
            Log::error('No se pudo crear la orden.', ['exception' => $exception::class]);
            return back()->withInput()->with('error', 'No fue posible crear la orden. Inténtalo nuevamente.');
        }

        $cart->clear();
        session()->forget(self::TOKEN_KEY);

        return redirect()->route('orders.show', $order)->with('ok', 'Orden creada correctamente.');
    }

    public function show(Order $order): View
    {
        return view('orders.show', ['order' => $order->load('items')]);
    }
}
