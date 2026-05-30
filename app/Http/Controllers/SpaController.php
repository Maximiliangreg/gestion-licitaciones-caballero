<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Product;
use Illuminate\Http\Request;

class SpaController extends Controller
{
    /**
     * Renderiza la SPA principal con datos iniciales desde la base de datos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener datos iniciales para renderizar en la SPA
        $tenders = Tender::with('client', 'creator')
            ->orderBy('created_at', 'desc')
            ->get();

        $products = Product::orderBy('created_at', 'desc')
            ->get();

        // Pasar datos al layout principal
        return view('layouts.app', [
            'tenders' => $tenders,
            'products' => $products,
        ]);
    }
}
