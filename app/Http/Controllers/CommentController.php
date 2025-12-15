<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Post $post)
    {
        $parentId = $request->validated('parent_id');

        if ($parentId) {
            $parent = Comment::where('id', $parentId)->where('post_id', $post->id)->first();
            if (! $parent) {
                abort(404);
            }
        }

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'content' => $request->validated('content'),
        ]);

        return back()->with('success', 'Комментарий добавлен');
    }

    public function destroy(Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Комментарий удален');
    }
}
