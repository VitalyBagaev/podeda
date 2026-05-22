<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddServiceRequest;
use App\Models\UserService;
use Illuminate\Support\Facades\Auth;

class UserCategoryController extends Controller
{
    public function index()
    {
        $services = UserService::where('user_id', Auth::id())->with('category')->get();

        return response()->json($services);
    }

    public function store(AddServiceRequest $request)
    {
        $service = new UserService();
        $service->user_id = Auth::id();
        $service->category_id = $request->category_id;
        $service->service_date = $request->service_date;
        $service->save();
        $service->load('category');

        return response()->json(['message' => 'ok', 'id' => $service->id, 'service' => $service]);
    }

    public function destroy($service)
    {
        UserService::where('id', $service)->where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'ok']);
    }
}
