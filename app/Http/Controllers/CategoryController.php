<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $gyms = $category->gyms()
            ->withCardData()
            ->orderBy('name')
            ->paginate(9);

        return view('categories.show', compact('category', 'gyms'));
    }
}