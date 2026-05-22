<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 4);
        $categories = Category::orderBy('id')->paginate($perPage);

        return response()->json($categories);
    }
}
