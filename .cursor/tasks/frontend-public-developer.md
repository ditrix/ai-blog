# Задачи для Frontend Public Developer

## Роль
Frontend разработчик (публичная часть) - создание Vue компонентов для публичных страниц

## Статус: ⏳ Ожидает начала

## Зависимости

- ⚠️ **Требуется**: Завершение задач backend-api-developer (публичные контроллеры и маршруты)

---

## Этап 4: Frontend - Публичные компоненты

### 4.1 Создание компонента списка статей (Index.vue)
**Приоритет**: Критический  
**Оценка времени**: 1.5 часа

- [ ] Создать `resources/js/pages/Posts/Index.vue`
- [ ] Определить props через TypeScript:
  ```typescript
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
  
  const props = defineProps<Props>();
  ```
- [ ] Реализовать структуру компонента:
  ```vue
  <template>
      <div class="container mx-auto px-4 py-8">
          <h1 class="text-3xl font-bold mb-8">Статьи блога</h1>
          
          <div v-if="posts.length === 0" class="text-center py-12">
              <p class="text-gray-500 dark:text-gray-400">Статей пока нет</p>
          </div>
          
          <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              <article
                  v-for="post in posts"
                  :key="post.id"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
              >
                  <h2 class="text-xl font-semibold mb-2">
                      <Link :href="show(post.slug).url" class="hover:underline">
                          {{ post.title }}
                      </Link>
                  </h2>
                  <p v-if="post.excerpt" class="text-gray-600 dark:text-gray-300 mb-4">
                      {{ post.excerpt }}
                  </p>
                  <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                      <span>{{ formatDate(post.published_at) }}</span>
                      <span>{{ post.user.name }}</span>
                  </div>
              </article>
          </div>
      </div>
  </template>
  ```
- [ ] Импортировать необходимые компоненты:
  ```typescript
  import { Link } from '@inertiajs/vue3';
  import { show } from '@/actions/App/Http/Controllers/PostController';
  ```
- [ ] Добавить функцию форматирования даты:
  ```typescript
  const formatDate = (date: string): string => {
      return new Date(date).toLocaleDateString('ru-RU', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
      });
  };
  ```
- [ ] Использовать Tailwind CSS для стилизации:
  - Адаптивная сетка (grid)
  - Карточки статей с hover эффектами
  - Поддержка dark mode через `dark:` префиксы
- [ ] Добавить пустое состояние (если статей нет)
- [ ] Использовать компонент `<Link>` из Inertia для навигации
- [ ] Проверить адаптивность на разных размерах экрана

**Результат**: Компонент списка статей создан и работает

---

### 4.2 Создание компонента просмотра статьи (Show.vue)
**Приоритет**: Критический  
**Оценка времени**: 1.5 часа

- [ ] Создать `resources/js/pages/Posts/Show.vue`
- [ ] Определить props через TypeScript:
  ```typescript
  interface Props {
      post: {
          id: number;
          title: string;
          slug: string;
          content: string;
          published_at: string;
          user: {
              name: string;
          };
      };
  }
  
  const props = defineProps<Props>();
  ```
- [ ] Реализовать структуру компонента:
  ```vue
  <template>
      <div class="container mx-auto px-4 py-8 max-w-4xl">
          <Link
              :href="index().url"
              class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-6"
          >
              <ArrowLeft class="w-4 h-4 mr-2" />
              Назад к списку статей
          </Link>
          
          <article>
              <header class="mb-8">
                  <h1 class="text-4xl font-bold mb-4">{{ post.title }}</h1>
                  <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
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
  ```
- [ ] Импортировать необходимые компоненты и функции:
  ```typescript
  import { Link } from '@inertiajs/vue3';
  import { index } from '@/actions/App/Http/Controllers/PostController';
  import { ArrowLeft } from 'lucide-vue-next';
  ```
- [ ] Добавить функцию форматирования даты (аналогично Index.vue)
- [ ] Использовать Tailwind CSS для стилизации:
  - Типографика для чтения длинных текстов
  - Использовать `prose` классы из Tailwind Typography (если установлен)
  - Поддержка dark mode
  - Максимальная ширина контента для удобства чтения
- [ ] Обработка случая, когда статья не найдена (404):
  - Inertia автоматически обработает 404 через `firstOrFail()` на бэкенде
  - Можно добавить обработку через `onError` в app.ts
- [ ] Добавить мета-теги для SEO:
  ```vue
  <Head>
      <title>{{ post.title }}</title>
      <meta name="description" :content="post.excerpt || post.title" />
  </Head>
  ```

**Результат**: Компонент просмотра статьи создан и работает

---

### 4.3 Генерация Wayfinder типов
**Приоритет**: Высокий  
**Оценка времени**: 10 минут

- [ ] Запустить генерацию Wayfinder типов:
  - Команда: `php artisan wayfinder:generate`
- [ ] Проверить создание типов для маршрутов:
  - Проверить файл `resources/js/actions/App/Http/Controllers/PostController.ts`
  - Убедиться, что функции `index()` и `show()` доступны
- [ ] Импортировать и использовать в компонентах:
  ```typescript
  import { index, show } from '@/actions/App/Http/Controllers/PostController';
  ```

**Результат**: Wayfinder типы сгенерированы и используются в компонентах

---

## Чеклист завершения этапа

- [ ] Компонент Posts/Index.vue создан и работает
- [ ] Компонент Posts/Show.vue создан и работает
- [ ] Wayfinder типы сгенерированы
- [ ] Все компоненты используют TypeScript для типизации
- [ ] Поддержка dark mode реализована
- [ ] Адаптивность проверена
- [ ] Код отформатирован через Prettier
- [ ] Используются существующие UI компоненты из проекта (если применимо)

## Примечания

- Использовать существующие UI компоненты из `resources/js/components/ui/`
- Следовать существующим паттернам именования и структуры проекта
- Использовать TypeScript для типизации на фронтенде
- Все ссылки должны использовать компонент `<Link>` из Inertia.js
- При работе с датами использовать форматирование через Vue composables или библиотеки
- Использовать Wayfinder для генерации типизированных маршрутов

## Зависимости

- ⚠️ **Требуется**: Завершение задач backend-api-developer

## Блокирует

- ⚠️ qa-engineer - не может тестировать фронтенд без компонентов

