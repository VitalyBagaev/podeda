<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        return Category::all();
    }
}