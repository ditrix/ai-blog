<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $posts = Post::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Автогенерация slug если не указан
        if (empty($data['slug'])) {
            $data['slug'] = Post::generateUniqueSlug($data['title']);
        }

        $post = $request->user()->posts()->create($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Статья успешно создана');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        // Обновление slug если изменился title
        if ($post->title !== $data['title'] && empty($data['slug'])) {
            $data['slug'] = Post::generateUniqueSlug($data['title'], $post->id);
        }

        $post->update($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Статья успешно обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Статья успешно удалена');
    }
}
