<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('products');

        if ($request->filled('name')) {
            $query->where('name_client', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicial') && $request->filled('data_final')) {
            $query->whereBetween('created_at', [$request->data_inicial, $request->data_final]);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_client' => 'required',
            'products' => 'required|array',
            'products.*.id' => 'exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);
        $order = Order::create(['name_client' => $validated['name_client']]);

        foreach ($validated['products'] as $products) {
            $order->products()->attach($products['id'], ['quantity' => $products['quantity']]);
        }

        $order->load('products');

        $totalAmount = $order->total; // Isso chama o accessor que você já definiu

        // Retorna a ordem com os produtos e o total
        return response()->json([
            'order' => $order,
            'total' => $totalAmount
        ]);
    }

    public function show(Order $order)
    {
        return $order->load('products');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'in:pending,paid',
            'name_client' => 'string|max:255',
            'cellphone' => 'nullable|string',
            'products' => 'nullable|array', 
            'products.*.product_id' => 'exists:products,id', 
            'products.*.quantity' => 'required|integer|min:1' 
        ]);

        $order->update($validated);

        if (isset($validated['products'])) {
            foreach ($validated['products'] as $productData) {
                if ($order->products->contains($productData['product_id'])) {
                    $order->products()->updateExistingPivot($productData['product_id'], [
                        'quantity' => $productData['quantity']
                    ]);
                } else {
                    return response()->json(['message' => 'Produto não encontrado na comanda: ' . $productData['product_id']], 404);
                }
            }
        }
        return response()->json($order->load('products'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Comanda deletada com sucesso!']);
    }
}

