<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Post::factory()
            ->count(10)
            ->published()
            ->for($user)
            ->create();

        Post::factory()
            ->count(3)
            ->unpublished()
            ->for($user)
            ->create();
    }
}
