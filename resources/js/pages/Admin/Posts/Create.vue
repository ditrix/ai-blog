<script setup lang="ts">
import {
    index,
    store,
} from '@/actions/App/Http/Controllers/Admin/PostController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    published_at: '',
});

const slugify = (text: string): string => {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
};

watch(
    () => form.title,
    (newTitle) => {
        if (newTitle && !form.slug) {
            form.slug = slugify(newTitle);
        }
    },
);

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Создать статью" />

    <AppSidebarLayout>
        <div class="container mx-auto max-w-4xl px-4 py-8">
            <h1 class="mb-6 text-3xl font-bold">Создать статью</h1>

            <form @submit.prevent="submit">
                <div
                    class="space-y-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800"
                >
                    <div>
                        <Label for="title">Заголовок *</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <InputError
                            v-if="form.errors.title"
                            :message="form.errors.title"
                        />
                    </div>

                    <div>
                        <Label for="slug">Slug *</Label>
                        <Input
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            required
                            :class="{ 'border-red-500': form.errors.slug }"
                        />
                        <InputError
                            v-if="form.errors.slug"
                            :message="form.errors.slug"
                        />
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Автоматически генерируется из заголовка
                        </p>
                    </div>

                    <div>
                        <Label for="excerpt">Краткое описание</Label>
                        <textarea
                            id="excerpt"
                            v-model="form.excerpt"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                        ></textarea>
                        <InputError
                            v-if="form.errors.excerpt"
                            :message="form.errors.excerpt"
                        />
                    </div>

                    <div>
                        <Label for="content">Содержание *</Label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="15"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            :class="{ 'border-red-500': form.errors.content }"
                        ></textarea>
                        <InputError
                            v-if="form.errors.content"
                            :message="form.errors.content"
                        />
                    </div>

                    <div>
                        <Label for="published_at">Дата публикации</Label>
                        <Input
                            id="published_at"
                            v-model="form.published_at"
                            type="datetime-local"
                        />
                        <InputError
                            v-if="form.errors.published_at"
                            :message="form.errors.published_at"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <Link
                            :href="index['/adm']().url"
                            class="rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Отмена
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{
                                form.processing ? 'Сохранение...' : 'Сохранить'
                            }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppSidebarLayout>
</template>
