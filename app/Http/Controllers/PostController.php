<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->get();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): Response
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->with('user')
            ->firstOrFail();

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }
}
