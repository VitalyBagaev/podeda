<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Requests\StoreAnswerRequest;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentPhoto;
use App\Models\CommentReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user', 'category', 'replies.user', 'photos'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    public function byCategory($category)
    {
        $comments = Comment::where('category_id', $category)
            ->with(['user', 'category', 'replies.user', 'photos'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    public function show($comment)
    {
        $comment = Comment::with(['user', 'category', 'replies.user', 'photos'])
            ->withCount('likes')
            ->findOrFail($comment);

        return response()->json($comment);
    }

    public function store(CommentStoreRequest $request)
    {
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->category_id = $request->category_id;
        $comment->text = $request->text;
        $comment->save();

        $photoFields = ['photo', 'photo2', 'photo3'];
        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                $path = Storage::disk('public')->putFile('comments', $request->file($field));
                $photo = new CommentPhoto();
                $photo->comment_id = $comment->id;
                $photo->photo_path = $path;
                $photo->save();
            }
        }

        return response()->json(['message' => 'ok', 'id' => $comment->id]);
    }

    public function update(CommentUpdateRequest $request, $comment)
    {
        $comment = Comment::findOrFail($comment);

        if ($comment->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        $comment->text = $request->text;
        $comment->save();

        $photoFields = ['photo', 'photo2', 'photo3'];
        $hasNewPhotos = false;

        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                $hasNewPhotos = true;
                break;
            }
        }

        if ($hasNewPhotos) {
            foreach ($comment->photos as $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto->photo_path);
                $oldPhoto->delete();
            }

            foreach ($photoFields as $field) {
                if ($request->hasFile($field)) {
                    $path = Storage::disk('public')->putFile('comments', $request->file($field));
                    $photo = new CommentPhoto();
                    $photo->comment_id = $comment->id;
                    $photo->photo_path = $path;
                    $photo->save();
                }
            }
        }

        return response()->json(['message' => 'ok', 'id' => $comment->id]);
    }

    public function destroy($comment)
    {
        $comment = Comment::findOrFail($comment);

        if ($comment->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        foreach ($comment->photos as $oldPhoto) {
            Storage::disk('public')->delete($oldPhoto->photo_path);
        }

        $comment->delete();

        return response()->json(['message' => 'ok']);
    }

    public function like($comment)
    {
        $like = CommentLike::where('user_id', Auth::id())
            ->where('comment_id', $comment)
            ->first();

        if ($like) {
            $like->delete();

            return response()->json([
                'message' => 'ok',
                'liked' => false,
                'likes_count' => Comment::find($comment)->likes()->count(),
            ]);
        }

        $newLike = new CommentLike();
        $newLike->user_id = Auth::id();
        $newLike->comment_id = $comment;
        $newLike->save();

        return response()->json([
            'message' => 'ok',
            'liked' => true,
            'likes_count' => Comment::find($comment)->likes()->count(),
        ]);
    }

    public function answer(StoreAnswerRequest $request, $comment)
    {
        if (Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        $reply = new CommentReply();
        $reply->comment_id = $comment;
        $reply->user_id = Auth::id();
        $reply->text = $request->text;
        $reply->save();

        return response()->json(['message' => 'ok', 'id' => $reply->id]);
    }

    public function answerEdit(StoreAnswerRequest $request, $answer)
    {
        if (Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        $reply = CommentReply::findOrFail($answer);
        $reply->text = $request->text;
        $reply->save();

        return response()->json(['message' => 'ok', 'id' => $reply->id]);
    }

    public function answerDel($answer)
    {
        if (Auth::user()->role != 'admin') {
            return response()->json(['message' => 'forbidden'], 403);
        }

        CommentReply::destroy($answer);

        return response()->json(['message' => 'ok']);
    }
}
