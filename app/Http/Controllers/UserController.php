<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $user = new User();
        $user->login = $request->login;
        $user->full_name = $request->full_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->role = 'user';

        if ($request->hasFile('avatar')) {
            $path = Storage::disk('public')->putFile('avatars', $request->file('avatar'));
            $user->avatar = $path;
        }

        $user->save();

        return response()->json(['token' => $user->createToken('api')->plainTextToken]);
    }

    public function auth(UserAuthRequest $request)
    {
        $user = User::where('login', $request->login)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('api')->plainTextToken;
            return response()->json([
                'message' => 'ok',
                'token' => $token,
            ]);
        }
        
        return response()->json([
            'message' => 'Неверный логин или пароль',
            'errors' => ['login' => ['Неверный логин или пароль']],
        ], 422);
    }

    public function user()
    {
        $user = User::with(['services.category', 'comments.category', 'comments.replies'])->find(Auth::id());

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ok']);
    }

    public function users()
    {
        if (Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        $users = User::select('id', 'login', 'full_name', 'email', 'phone', 'role', 'avatar', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['users' => $users]);
    }

    public function show($user)
    {
        $user = User::with(['services.category', 'comments.category', 'comments.replies'])->findOrFail($user);

        return response()->json($user);
    }

    public function update(Request $request, $user)
    {
        $user = User::findOrFail($user);

        if (Auth::id() != $user->id && Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        if ($request->hasFile('avatar')) {
            $path = Storage::disk('public')->putFile('avatars', $request->file('avatar'));
            $user->avatar = $path;
            $user->save();
        } else {
            $validated = $request->validate([
                'full_name' => ['required', 'string', 'min:5', 'max:255', 'regex:/^[А-Яа-яЁё\s]+$/u'],
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => ['required', 'string', 'regex:/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', 'unique:users,phone,' . $user->id],
            ]);

            $user->full_name = $validated['full_name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->save();
        }

        $user->load(['services.category', 'comments.category']);

        return response()->json($user);
    }
}
