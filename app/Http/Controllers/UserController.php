<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserAuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'login' => $validated['login'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function login(UserAuthRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('login', $validated['login'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Неверный логин или пароль',
                'errors' => ['login' => ['Неверный логин или пароль']],
            ], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function me()
    {
        $user = User::with(['comments.category', 'services.category'])->find(Auth::id());
        return response()->json($user);
    }



    public function getAll()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::select('id', 'login', 'full_name', 'email', 'phone', 'role', 'avatar', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        $user = User::with(['comments.category', 'services.category'])->findOrFail($id);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (Auth::id() != $user->id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
            return response()->json($user);
        }

        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ok']);
    }
}
