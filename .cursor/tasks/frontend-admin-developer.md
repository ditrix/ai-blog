# Задачи для Frontend Admin Developer

## Роль
Frontend разработчик (административная часть) - создание Vue компонентов для админки

## Статус: ⏳ Ожидает начала

## Зависимости

- ⚠️ **Требуется**: Завершение задач backend-api-developer (административные контроллеры и маршруты)

---

## Этап 5: Frontend - Административные компоненты

### 5.1 Создание компонента списка статей в админке (Index.vue)
**Приоритет**: Высокий  
**Оценка времени**: 2 часа

- [ ] Создать `resources/js/pages/Admin/Posts/Index.vue`
- [ ] Определить props через TypeScript:
  ```typescript
  interface Props {
      posts: Array<{
          id: number;
          title: string;
          slug: string;
          published_at: string | null;
          created_at: string;
          user: {
              name: string;
          };
      }>;
  }
  
  const props = defineProps<Props>();
  ```
- [ ] Реализовать структуру компонента:
  ```vue
  <template>
      <div class="container mx-auto px-4 py-8">
          <div class="flex items-center justify-between mb-6">
              <h1 class="text-3xl font-bold">Управление статьями</h1>
              <Link
                  :href="create().url"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                  <Plus class="w-4 h-4 mr-2" />
                  Создать статью
              </Link>
          </div>
          
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                      <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Заголовок
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Slug
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Статус
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Дата
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Автор
                          </th>
                          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                              Действия
                          </th>
                      </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                      <tr v-for="post in posts" :key="post.id">
                          <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                  {{ post.title }}
                              </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-500 dark:text-gray-400">
                                  {{ post.slug }}
                              </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                              <Badge :variant="post.published_at ? 'success' : 'secondary'">
                                  {{ post.published_at ? 'Опубликовано' : 'Черновик' }}
                              </Badge>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                              {{ formatDate(post.published_at || post.created_at) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                              {{ post.user.name }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                              <Link
                                  :href="edit(post.id).url"
                                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-4"
                              >
                                  Редактировать
                              </Link>
                              <Form
                                  :action="destroy(post.id).url"
                                  method="delete"
                                  @submit="confirmDelete"
                              >
                                  <button
                                      type="submit"
                                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                  >
                                      Удалить
                                  </button>
                              </Form>
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
      </div>
  </template>
  ```
- [ ] Импортировать необходимые компоненты:
  ```typescript
  import { Link, Form } from '@inertiajs/vue3';
  import { create, edit, destroy } from '@/actions/App/Http/Controllers/Admin/PostController';
  import { Plus } from 'lucide-vue-next';
  import { Badge } from '@/components/ui/badge';
  ```
- [ ] Реализовать удаление статьи с подтверждением:
  ```typescript
  const confirmDelete = (e: Event) => {
      if (!confirm('Вы уверены, что хотите удалить эту статью?')) {
          e.preventDefault();
      }
  };
  ```
- [ ] Использовать компонент `<Form>` из Inertia для удаления
- [ ] Использовать существующие UI компоненты из проекта (Badge, Button и т.д.)
- [ ] Добавить функцию форматирования даты
- [ ] Поддержка dark mode

**Результат**: Компонент списка статей в админке создан и работает

---

### 5.2 Создание компонента создания статьи (Create.vue)
**Приоритет**: Высокий  
**Оценка времени**: 2.5 часа

- [ ] Создать `resources/js/pages/Admin/Posts/Create.vue`
- [ ] Реализовать форму с использованием компонента `<Form>` из Inertia.js:
  ```vue
  <template>
      <div class="container mx-auto px-4 py-8 max-w-4xl">
          <h1 class="text-3xl font-bold mb-6">Создать статью</h1>
          
          <Form
              :action="store().url"
              method="post"
              #default="{ processing, errors, hasErrors }"
          >
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
                  <div>
                      <Label for="title">Заголовок *</Label>
                      <Input
                          id="title"
                          v-model="form.title"
                          type="text"
                          required
                          :class="{ 'border-red-500': errors.title }"
                      />
                      <InputError v-if="errors.title" :message="errors.title" />
                  </div>
                  
                  <div>
                      <Label for="slug">Slug *</Label>
                      <Input
                          id="slug"
                          v-model="form.slug"
                          type="text"
                          required
                          :class="{ 'border-red-500': errors.slug }"
                      />
                      <InputError v-if="errors.slug" :message="errors.slug" />
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
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
                      <InputError v-if="errors.excerpt" :message="errors.excerpt" />
                  </div>
                  
                  <div>
                      <Label for="content">Содержание *</Label>
                      <textarea
                          id="content"
                          v-model="form.content"
                          rows="15"
                          required
                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                          :class="{ 'border-red-500': errors.content }"
                      ></textarea>
                      <InputError v-if="errors.content" :message="errors.content" />
                  </div>
                  
                  <div>
                      <Label for="published_at">Дата публикации</Label>
                      <Input
                          id="published_at"
                          v-model="form.published_at"
                          type="datetime-local"
                      />
                      <InputError v-if="errors.published_at" :message="errors.published_at" />
                  </div>
                  
                  <div class="flex items-center justify-end gap-4">
                      <Link
                          :href="index().url"
                          class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                      >
                          Отмена
                      </Link>
                      <Button
                          type="submit"
                          :disabled="processing"
                      >
                          {{ processing ? 'Сохранение...' : 'Сохранить' }}
                      </Button>
                  </div>
              </div>
          </Form>
      </div>
  </template>
  ```
- [ ] Импортировать необходимые компоненты:
  ```typescript
  import { Form, Link } from '@inertiajs/vue3';
  import { store, index } from '@/actions/App/Http/Controllers/Admin/PostController';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Button } from '@/components/ui/button';
  import { InputError } from '@/components/InputError';
  ```
- [ ] Реализовать реактивную форму:
  ```typescript
  import { useForm } from '@inertiajs/vue3';
  import { watch } from 'vue';
  import { Str } from '@/lib/utils'; // или использовать библиотеку для slug
  
  const form = useForm({
      title: '',
      slug: '',
      excerpt: '',
      content: '',
      published_at: '',
  });
  
  // Автогенерация slug при вводе title
  watch(() => form.title, (newTitle) => {
      if (newTitle && !form.slug) {
          form.slug = slugify(newTitle);
      }
  });
  
  const slugify = (text: string): string => {
      return text
          .toLowerCase()
          .replace(/[^\w\s-]/g, '')
          .replace(/\s+/g, '-')
          .replace(/-+/g, '-')
          .trim();
  };
  ```
- [ ] Добавить валидацию на фронтенде (опционально, основная валидация на бэкенде)
- [ ] Отображение ошибок валидации через компонент `InputError`
- [ ] Кнопка "Сохранить" с индикатором загрузки (через `processing`)
- [ ] Кнопка "Отмена" (возврат к списку)
- [ ] Автогенерация slug при вводе title (через watch)

**Результат**: Компонент создания статьи создан и работает

---

### 5.3 Создание компонента редактирования статьи (Edit.vue)
**Приоритет**: Высокий  
**Оценка времени**: 2 часа

- [ ] Создать `resources/js/pages/Admin/Posts/Edit.vue`
- [ ] Определить props:
  ```typescript
  interface Props {
      post: {
          id: number;
          title: string;
          slug: string;
          excerpt: string | null;
          content: string;
          published_at: string | null;
      };
  }
  
  const props = defineProps<Props>();
  ```
- [ ] Реализовать форму аналогичную Create.vue
- [ ] Предзаполнить поля данными статьи:
  ```typescript
  const form = useForm({
      title: props.post.title,
      slug: props.post.slug,
      excerpt: props.post.excerpt || '',
      content: props.post.content,
      published_at: props.post.published_at 
          ? new Date(props.post.published_at).toISOString().slice(0, 16)
          : '',
  });
  ```
- [ ] Использовать метод PUT/PATCH для обновления:
  ```typescript
  import { update } from '@/actions/App/Http/Controllers/Admin/PostController';
  
  // В форме использовать update(post.id).url и method="put"
  ```
- [ ] Обработка ошибок валидации (аналогично Create.vue)
- [ ] Кнопка "Обновить" с индикатором загрузки
- [ ] Кнопка "Отмена"

**Результат**: Компонент редактирования статьи создан и работает

---

### 5.4 Исправление отображения кнопок редактирования и удаления в Index.vue
**Приоритет**: Критический  
**Оценка времени**: 30 минут

- [x] Проверить, почему колонка "Действия" не отображается в браузере:
  - Проверить консоль браузера на наличие JavaScript ошибок
  - Проверить, что функции `edit()` и `destroy()` правильно импортированы из Wayfinder
  - Проверить, что Wayfinder типы сгенерированы: `php artisan wayfinder:generate`
- [x] Исправить проблему с отображением колонки "Действия":
  - Убедиться, что колонка не скрыта CSS стилями
  - Проверить, что все `<td>` элементы правильно закрыты в таблице
  - Проверить, что `v-for` правильно итерирует по `posts`
- [x] Исправить синтаксис кнопки "Отмена" в Create.vue и Edit.vue:
  - В `Create.vue` строка 136: заменено `index['/adm/posts']().url` на `index['/adm']().url`
  - В `Edit.vue` строка 127: заменено `index['/adm/posts']().url` на `index['/adm']().url`
- [x] Проверить работу кнопок:
  - Кнопка "Редактировать" должна вести на страницу редактирования
  - Кнопка "Удалить" должна показывать подтверждение и удалять статью
- [ ] Пересобрать фронтенд после исправлений:
  - Команда: `npm run build` или `npm run dev`
  - Проверить в браузере, что кнопки отображаются и работают

**Результат**: Кнопки редактирования и удаления отображаются и работают корректно

---

### 5.5 Использование Layout для админки
**Приоритет**: Средний  
**Оценка времени**: 30 минут

- [ ] Проверить существующий `AppLayout` или `AppSidebarLayout`
  - Файл: `resources/js/layouts/AppLayout.vue` или `resources/js/layouts/app/AppSidebarLayout.vue`
- [ ] Использовать его для административных страниц:
  ```vue
  <script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue';
  // или
  import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
  </script>
  
  <template>
      <AppLayout>
          <!-- содержимое страницы -->
      </AppLayout>
  </template>
  ```
- [ ] Добавить навигацию в админку (если необходимо):
  - Проверить существующие компоненты навигации
  - Добавить ссылку на админку в меню (если нужно)

**Результат**: Административные страницы используют правильный layout

---

## Чеклист завершения этапа

- [ ] Компонент Admin/Posts/Index.vue создан и работает
- [ ] Компонент Admin/Posts/Create.vue создан и работает
- [ ] Компонент Admin/Posts/Edit.vue создан и работает
- [ ] Кнопки редактирования и удаления отображаются и работают в Index.vue
- [ ] Исправлен синтаксис кнопки "Отмена" в Create.vue и Edit.vue
- [ ] Layout применен к административным страницам
- [ ] Все формы используют компонент `<Form>` из Inertia.js
- [ ] Автогенерация slug реализована на фронтенде
- [ ] Валидация и отображение ошибок работает
- [ ] Поддержка dark mode реализована
- [ ] Код отформатирован через Prettier

## Примечания

- Использовать существующие UI компоненты из `resources/js/components/ui/`
- Следовать существующим паттернам именования и структуры проекта
- Использовать TypeScript для типизации на фронтенде
- Все формы должны использовать компонент `<Form>` из Inertia.js v2
- При работе с датами использовать правильное форматирование
- Использовать Wayfinder для генерации типизированных маршрутов

## Зависимости

- ⚠️ **Требуется**: Завершение задач backend-api-developer

## Блокирует

- ⚠️ qa-engineer - не может тестировать админку без компонентов

