<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::orderBy('name')->get();

        return Inertia::render('products/index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $categories = Category::orderBy('name')->get();

    //     return Inertia::render('products/create', [
    //         'categories' => $categories,
    //     ]);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    // public function show(Product $product)
    // {
    //     $product->load('category');

    //     return Inertia::render('products/show', [
    //         'product' => $product,
    //     ]);
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Product $product)
    // {
    //     $categories = Category::orderBy('name')->get();

    //     return Inertia::render('products/edit', [
    //         'product' => $product,
    //         'categories' => $categories,
    //     ]);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index');
    }
}
