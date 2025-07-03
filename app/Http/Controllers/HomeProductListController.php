<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeProductListController extends Controller
{
    public function index()
    {
        // Fetch products with their related category data
        $products = Product::with('categoryMaster')  // Eager load the categoryMaster relation
                            ->select('name', 'selling_price', 'category_master_id')
                            ->get()
                            ->groupBy('category_master_id');  // Group by category_master_id

        return view('welcome', compact('products'));
    }
}
