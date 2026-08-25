<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        return view('cart.index', ['cart' => $cart->snapshot()]);
    }

    public function store(Course $course, CartService $cart): RedirectResponse
    {
        if (! $cart->add($course)) {
            return back()->with('error', 'El curso no está disponible o no tiene un precio comercial válido.');
        }

        return redirect()->route('cart.index')->with('ok', 'Curso agregado al carrito.');
    }

    public function update(Request $request, Course $course, CartService $cart): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'in:1']]);

        if (! in_array($course->getKey(), $cart->ids(), true)) {
            return back()->with('error', 'El curso no está en el carrito.');
        }

        return back()->with('ok', 'La cantidad del curso se mantiene en una unidad.');
    }

    public function destroy(Course $course, CartService $cart): RedirectResponse
    {
        $cart->remove($course);

        return back()->with('ok', 'Curso eliminado del carrito.');
    }

    public function clear(CartService $cart): RedirectResponse
    {
        $cart->clear();

        return back()->with('ok', 'Carrito vaciado.');
    }
}
