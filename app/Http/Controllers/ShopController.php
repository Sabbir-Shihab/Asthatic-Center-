<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->category)))
            ->when($request->filled('brand'), fn ($q) => $q->whereHas('brand', fn ($b) => $b->where('slug', $request->brand)))
            ->when($request->filled('skin_type'), fn ($q) => $q->whereJsonContains('skin_types', $request->skin_type))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->integer('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->integer('max_price')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($s) => $s->where('name', 'like', '%'.$request->q.'%')->orWhere('description', 'like', '%'.$request->q.'%')))
            ->when($request->input('sort') === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($request->input('sort') === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when(!$request->filled('sort'), fn ($q) => $q->orderByDesc('is_featured')->latest())
            ->paginate(12)->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'cartProducts' => Product::where('is_active',true)->where('stock','>',0)->get(['id','name','price','image','stock']),
            'categories' => \App\Models\Category::where('is_active', true)->withCount(['products'=>fn($q)=>$q->where('is_active',true)])->orderBy('sort_order')->get(),
            'brands' => \App\Models\Brand::where('is_active', true)->orderBy('name')->get(),
            'skinTypes' => ['All Skin Types', 'Oily Skin', 'Dry Skin', 'Sensitive Skin', 'Combination Skin'],
        ]);
    }
}
