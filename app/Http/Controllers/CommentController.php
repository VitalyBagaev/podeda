<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReply;
use App\Models\CommentPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Get all comments
     */
    public function index()
    {
        $comments = Comment::with(['user', 'replies.user', 'photos'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Get comments by category
     */
    public function create($categoryId)
    {
        $comments = Comment::where('category_id', $categoryId)
            ->with(['user', 'replies.user', 'photos'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $comment = Comment::create([
            'text' => $validated['text'],
            'category_id' => $validated['category_id'],
            'user_id' => Auth::id(),
        ]);

        // Handle photos
        $photoFields = ['photo', 'photo2', 'photo3'];
        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('comments', 'public');
                CommentPhoto::create([
                    'comment_id' => $comment->id,
                    'photo_path' => 'storage/' . $path,
                ]);
            }
        }

        return response()->json($comment->load(['user', 'photos']), 201);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'text' => 'required|string',
        ]);

        $comment->update(['text' => $validated['text']]);

        // Handle new photos if provided
        $photoFields = ['photo', 'photo2', 'photo3'];
        $hasNewPhotos = false;

        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                $hasNewPhotos = true;
                break;
            }
        }

        if ($hasNewPhotos) {
            // Delete old photos
            foreach ($comment->photos as $oldPhoto) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPhoto->photo_path));
                $oldPhoto->delete();
            }

            // Store new photos
            foreach ($photoFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('comments', 'public');
                    CommentPhoto::create([
                        'comment_id' => $comment->id,
                        'photo_path' => 'storage/' . $path,
                    ]);
                }
            }
        }

        return response()->json($comment->load('photos'));
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json(['message' => 'Комментарий удалён']);
    }

    /**
     * Add a reply to a comment
     */
    public function addReply(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'У вас нет прав для этого действия'], 403);
        }

        $validated = $request->validate([
            'text' => 'required|string',
        ]);

        $reply = CommentReply::create([
            'comment_id' => $id,
            'user_id' => Auth::id(),
            'text' => $validated['text'],
        ]);

        return response()->json($reply, 201);
    }

    /**
     * Update a reply
     */
    public function updateReply(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Не достаточно прав'], 403);
        }

        $reply = CommentReply::findOrFail($id);

        $validated = $request->validate([
            'text' => 'required|string',
        ]);

        $reply->update(['text' => $validated['text']]);

        return response()->json($reply);
    }

    /**
     * Show a single comment
     */
    public function show($id)
    {
        $comment = Comment::with(['user', 'replies.user', 'photos'])
            ->withCount('likes')
            ->findOrFail($id);

        return response()->json($comment);
    }

    /**
     * Like/unlike a comment
     */
    public function like($id)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Необходима авторизация'], 401);
        }

        $like = CommentLike::where('user_id', $userId)
            ->where('comment_id', $id)
            ->first();

        if ($like) {
            $like->delete();
            $likesCount = Comment::find($id)->likes()->count();

            return response()->json([
                'message' => 'Вы убрали свой лайк.',
                'liked' => false,
                'is_liked' => false,
                'likes_count' => $likesCount,
            ]);
        }

        CommentLike::create(['user_id' => $userId, 'comment_id' => $id]);
        $likesCount = Comment::find($id)->likes()->count();

        return response()->json([
            'message' => 'Вы поставили лайк.',
            'liked' => true,
            'is_liked' => true,
            'likes_count' => $likesCount,
        ]);
    }

    /**
     * Delete a reply
     */
    public function deleteReply($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Не достаточно прав'], 403);
        }

        CommentReply::destroy($id);

        return response()->json(['message' => 'Удалили ответ']);
    }
}
