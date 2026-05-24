<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $products = Product::all();

            return response()->json([
                'success' => true,
                'data' => $products,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'sku' => ['required', 'string', 'unique:products,sku'],
                'name' => ['required', 'string', 'max:255'],
                'unit_price' => ['required', 'numeric', 'decimal:0,2'],
                'stock' => ['required', 'integer', 'min:0'],
            ]);

            $userId = auth()->id();
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;

            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'data' => $product,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto.',
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el producto.',
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                ], 404);
            }

            $data = $request->validate([
                'sku' => ['required', 'string', 'unique:products,sku,' . $id],
                'name' => ['required', 'string', 'max:255'],
                'unit_price' => ['required', 'numeric', 'decimal:0,2'],
                'stock' => ['required', 'integer', 'min:0'],
            ]);

            $data['updated_by'] = auth()->id();

            $product->fill($data);
            $product->save();

            return response()->json([
                'success' => true,
                'data' => $product,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto.',
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                ], 404);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'data' => [],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto.',
            ], 500);
        }
    }
}
