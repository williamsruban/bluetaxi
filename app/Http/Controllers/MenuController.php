<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    // Fetch all menu items
    public function index()
    {
        return response()->json(['message' => 'Menu fetch successfully', 'data' => Menu::all()], 200);
    }

    // Store a new menu item
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:menu,slug',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $menu = Menu::create($request->all());

        return response()->json(['message' => 'Menu created successfully', 'data' => $menu], 201);
    }
}
