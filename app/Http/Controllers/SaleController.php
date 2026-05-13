<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function store(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ], [
            'quantity.required' => 'Indica la cantidad.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad mínima es 1.',
        ]);

        $sale = DB::transaction(function () use ($product, $data, $request) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            $quantity = (int) $data['quantity'];

            if ($product->stock < $quantity) {
                abort(422, 'No hay existencias suficientes para completar la operación.');
            }

            $unitPrice = (float) $product->price;
            $total = $unitPrice * $quantity;

            $product->decrement('stock', $quantity);

            return Sale::create([
                'product_id' => $product->id,
                'user_id' => $request->session()->get('user_id'),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
            ]);
        });

        $message = $request->session()->get('user_role') === 'admin'
            ? 'Venta confirmada correctamente.'
            : 'Compra realizada correctamente.';

        return response()->json([
            'ok' => true,
            'message' => $message,
            'sale' => $sale,
            'product' => $product->fresh(),
            'stats' => [
                'totalProducts' => Product::count(),
                'totalStock' => (int) Product::sum('stock'),
                'inventoryValue' => number_format((float) Product::selectRaw('COALESCE(SUM(price * stock), 0) as total')->value('total'), 2, '.', ''),
            ],
        ]);
    }
}
