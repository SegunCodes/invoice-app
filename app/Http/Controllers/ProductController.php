<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function allProducts(){
        $products = Product::orderBy('id', 'DESC')->get();

        return response()->json([
            "products" => $products
        ], 200);
    }
}
