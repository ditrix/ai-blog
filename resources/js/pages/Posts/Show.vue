<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/PostController';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        content: string;
        published_at: string;
        user: {
            name: string;
        };
    };
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
    <Head>
        <title>{{ post.title }}</title>
        <meta name="description" :content="post.excerpt || post.title" />
    </Head>

    <div class="container mx-auto max-w-4xl px-4 py-8">
        <Link
            :href="index().url"
            class="mb-6 inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
        >
            <ArrowLeft class="mr-2 h-4 w-4" />
            Назад к списку статей
        </Link>

        <article>
            <header class="mb-8">
                <h1 class="mb-4 text-4xl font-bold">{{ post.title }}</h1>
                <div
                    class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400"
                >
                    <time :datetime="post.published_at">
                        {{ formatDate(post.published_at) }}
                    </time>
                    <span>•</span>
                    <span>{{ post.user.name }}</span>
                </div>
            </header>

            <div
                class="prose prose-lg dark:prose-invert max-w-none"
                v-html="post.content"
            ></div>
        </article>
    </div>
</template>
