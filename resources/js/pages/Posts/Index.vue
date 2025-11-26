<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/PostController';
import { Link } from '@inertiajs/vue3';

interface Props {
    posts: Array<{
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        published_at: string;
        user: {
            name: string;
        };
    }>;
}

defineProps<Props>();

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="mb-8 text-3xl font-bold">Статьи блога</h1>

        <div v-if="posts.length === 0" class="py-12 text-center">
            <p class="text-gray-500 dark:text-gray-400">Статей пока нет</p>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="post in posts"
                :key="post.id"
                class="rounded-lg bg-white p-6 shadow-md transition-shadow hover:shadow-lg dark:bg-gray-800"
            >
                <h2 class="mb-2 text-xl font-semibold">
                    <Link :href="show(post.slug).url" class="hover:underline">
                        {{ post.title }}
                    </Link>
                </h2>
                <p
                    v-if="post.excerpt"
                    class="mb-4 text-gray-600 dark:text-gray-300"
                >
                    {{ post.excerpt }}
                </p>
                <div
                    class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"
                >
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span>{{ post.user.name }}</span>
                </div>
            </article>
        </div>
    </div>
</template>
