<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class TenderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(\App\Http\Middleware\AdminOnly::class)->only(['store', 'update', 'destroy', 'attachProduct', 'detachProduct']);
    }

    /**
     * Obtener todas las licitaciones con sus relaciones (cliente y productos)
     * Eager loading para optimizar consultas (RN-08)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tenders = Tender::with(['client', 'products', 'creator'])
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $tenders->items(),
                'pagination' => [
                    'total' => $tenders->total(),
                    'current_page' => $tenders->currentPage(),
                    'per_page' => $tenders->perPage(),
                    'last_page' => $tenders->lastPage(),
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener licitaciones.',
            ], 500);
        }
    }

    /**
     * Obtener una licitación específica con sus relaciones
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $tender = Tender::with(['client', 'products', 'creator'])->find($id);

            if (!$tender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licitación no encontrada.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tender,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la licitación.',
            ], 500);
        }
    }

    /**
     * Crear una nueva licitación
     * Validación: max_budget > 0, estado inicial 'activa', registrar created_by
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'client_id' => ['required', 'integer', 'exists:clients,id'],
                'max_budget' => ['required', 'numeric', 'gt:0'],
                'delivery_deadline' => ['nullable', 'date'],
            ]);

            // Asignar estado inicial y usuario creador
            $data['status'] = 'activa';
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
            $data['total_amount'] = 0;

            $tender = Tender::create($data);

            // Cargar relaciones
            $tender->load(['client', 'products', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $tender,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la licitación.',
            ], 500);
        }
    }

    /**
     * Actualizar una licitación existente
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $tender = Tender::find($id);

            if (!$tender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licitación no encontrada.',
                ], 404);
            }

            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'client_id' => ['required', 'integer', 'exists:clients,id'],
                'max_budget' => ['required', 'numeric', 'gt:0'],
                'delivery_deadline' => ['nullable', 'date'],
                'status' => ['required', 'string', 'in:activa,pausada,finalizada,cancelada'],
            ]);

            $data['updated_by'] = auth()->id();

            $tender->fill($data);
            $tender->save();

            // Cargar relaciones
            $tender->load(['client', 'products', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $tender,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la licitación.',
            ], 500);
        }
    }

    /**
     * Eliminar una licitación
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $tender = Tender::find($id);

            if (!$tender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licitación no encontrada.',
                ], 404);
            }

            // Eliminar relaciones en tabla pivote
            $tender->products()->detach();

            // Eliminar licitación
            $tender->delete();

            return response()->json([
                'success' => true,
                'data' => [],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la licitación.',
            ], 500);
        }
    }

    /**
     * Adjuntar un producto a una licitación
     * RN-09: Validar que no exista duplicado en tender_products
     * Calcular y acumular total_amount de forma automática
     * Registrar added_by con el usuario logueado
     */
    public function attachProduct(Request $request, int $id): JsonResponse
    {
        try {
            $tender = Tender::find($id);

            if (!$tender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licitación no encontrada.',
                ], 404);
            }

            $data = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['required', 'integer', 'min:1'],
                'unit_price' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            ]);

            $product = Product::find($data['product_id']);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                ], 404);
            }

            // RN-09: Validar que no exista el producto duplicado en la licitación
            $existingProduct = $tender->products()
                ->where('product_id', $data['product_id'])
                ->exists();

            if ($existingProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto ya está asociado a la licitación. No se permiten duplicados.',
                ], 422);
            }

            // Calcular el monto para este producto
            $lineAmount = $data['quantity'] * $data['unit_price'];

            // Adjuntar el producto con los datos adicionales
            $tender->products()->attach($data['product_id'], [
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'added_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Actualizar total_amount de la licitación (acumular)
            $tender->increment('total_amount', $lineAmount);
            $tender->update(['updated_by' => auth()->id()]);

            // Cargar relaciones y retornar
            $tender->load(['client', 'products', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $tender,
                'message' => 'Producto adjuntado exitosamente.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos. Verifica que los datos sean válidos.',
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al adjuntar el producto a la licitación.',
            ], 500);
        }
    }

    /**
     * Desadjuntar un producto de una licitación
     * Restar el monto del total_amount
     */
    public function detachProduct(Request $request, int $id): JsonResponse
    {
        try {
            $tender = Tender::find($id);

            if (!$tender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licitación no encontrada.',
                ], 404);
            }

            $data = $request->validate([
                'product_id' => ['required', 'integer'],
            ]);

            // Obtener el producto pivote antes de desadjuntarlo
            $pivot = $tender->products()
                ->where('product_id', $data['product_id'])
                ->withPivot('quantity', 'unit_price')
                ->first();

            if (!$pivot) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está asociado a esta licitación.',
                ], 404);
            }

            // Calcular el monto a restar
            $lineAmount = $pivot->pivot->quantity * $pivot->pivot->unit_price;

            // Desadjuntar el producto
            $tender->products()->detach($data['product_id']);

            // Actualizar total_amount (restar)
            $tender->decrement('total_amount', $lineAmount);
            $tender->update(['updated_by' => auth()->id()]);

            // Cargar relaciones y retornar
            $tender->load(['client', 'products', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $tender,
                'message' => 'Producto removido exitosamente.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover el producto de la licitación.',
            ], 500);
        }
    }
}
