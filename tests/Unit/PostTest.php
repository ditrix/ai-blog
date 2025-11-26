<?php

use App\Models\Post;
use App\Models\User;

it('belongs to user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    expect($post->user)->toBeInstanceOf(User::class);
    expect($post->user->id)->toBe($user->id);
});

it('filters published posts', function () {
    $user = User::factory()->create();
    $publishedPost = Post::factory()->published()->for($user)->create();
    $unpublishedPost = Post::factory()->unpublished()->for($user)->create();

    $publishedPosts = Post::published()->get();

    expect($publishedPosts)->toHaveCount(1);
    expect($publishedPosts->first()->id)->toBe($publishedPost->id);
});

it('casts published_at to datetime', function () {
    $post = Post::factory()->create(['published_at' => '2024-01-01 12:00:00']);

    expect($post->published_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
