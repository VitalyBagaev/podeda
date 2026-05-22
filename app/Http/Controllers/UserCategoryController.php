<?php

namespace App\Http\Controllers;

use App\Models\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCategoryController extends Controller
{
    /**
     * Get user services
     */
    public function index()
    {
        $services = UserService::where('user_id', Auth::id())->with('category')->get();
        return response()->json($services);
    }

    /**
     * Show user service
     */
    public function show($id)
    {
        $service = UserService::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('category')
            ->firstOrFail();

        return response()->json($service);
    }

    /**
     * Store user service
     */
    public function store($id, Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'service_date' => 'required|date',
        ]);

        $service = UserService::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'service_date' => $validated['service_date'],
        ]);

        return response()->json($service->load('category'), 201);
    }

    /**
     * Delete user service
     */
    public function destroy($id)
    {
        UserService::where('id', $id)->where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'Услуга удалена.']);
    }
}
