<?php

use App\Models\Post;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('displays list of published posts', function () {
    $user = User::factory()->create();

    $publishedPost = Post::factory()->published()->for($user)->create();
    $unpublishedPost = Post::factory()->unpublished()->for($user)->create();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Posts/Index')
        ->has('posts', 1)
        ->where('posts.0.id', $publishedPost->id)
    );
});

it('displays single post by slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->published()->for($user)->create();

    $response = $this->get("/{$post->slug}");

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Posts/Show')
        ->where('post.id', $post->id)
        ->where('post.title', $post->title)
    );
});

it('returns 404 for non-existent post', function () {
    $response = $this->get('/non-existent-slug');

    $response->assertNotFound();
});

it('returns 404 for unpublished post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->unpublished()->for($user)->create();

    $response = $this->get("/{$post->slug}");

    $response->assertNotFound();
});
