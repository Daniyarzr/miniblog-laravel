<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('author')
            ->withCount('likes')
            ->latest();

        if ($search = $request->string('q')->trim()) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $posts = $query->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load('author')->loadCount('likes');

        return new PostResource($post, true);
    }
}
