<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::latest()->get();
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $inventoryValue = Product::selectRaw('COALESCE(SUM(price * stock), 0) as total')->value('total');
        $currentUser = User::find($request->session()->get('user_id'));
        $isAdmin = $request->session()->get('user_role') === 'admin';

        return view('pos.index', compact('products', 'totalProducts', 'totalStock', 'inventoryValue', 'currentUser', 'isAdmin'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $this->validatedProductData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Producto registrado correctamente.',
                'product' => $product->fresh(),
                'stats' => $this->stats(),
            ], 201);
        }

        return redirect()->route('pos.index')->with('success', 'Producto registrado correctamente.');
    }

    public function update(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $data = $this->validatedProductData($request);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Producto actualizado correctamente.',
                'product' => $product->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return redirect()->route('pos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function list(): JsonResponse
    {
        return response()->json([
            'products' => Product::latest()->get(),
            'stats' => $this->stats(),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Producto eliminado correctamente.',
            'stats' => $this->stats(),
        ]);
    }

    private function validatedProductData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'brand.required' => 'La marca es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser numérico.',
            'stock.required' => 'La cantidad en existencia es obligatoria.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no debe pesar más de 2 MB.',
        ]);
    }

    private function stats(): array
    {
        return [
            'totalProducts' => Product::count(),
            'totalStock' => (int) Product::sum('stock'),
            'inventoryValue' => number_format((float) Product::selectRaw('COALESCE(SUM(price * stock), 0) as total')->value('total'), 2, '.', ''),
        ];
    }
}
