<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->string('q')->trim();

        $query = Post::with('author')
            ->withCount(['likes', 'comments'])
            ->latest();

        if ($search->isNotEmpty()) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Статья создана');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author'])->loadCount('likes');

        $userLike = Auth::check()
            ? $post->likes()->where('user_id', Auth::id())->exists()
            : false;

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['author', 'replies.author'])
            ->latest()
            ->get();

        return view('posts.show', compact('post', 'comments', 'userLike'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }
        $post->update($request->validated());

        return redirect()->route('posts.show', $post)->with('success', 'Статья обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Статья удалена');
    }

    public function like(Post $post)
    {
        $user = Auth::user();

        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Лайк удален';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $message = 'Лайк добавлен';
        }

        return back()->with('success', $message);
    }
}
