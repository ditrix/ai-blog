# Архитектура приложения Blog

## Обзор

Приложение представляет собой блог-платформу на базе Laravel 12 с использованием Inertia.js v2 и Vue 3 для фронтенда. Система состоит из двух основных частей: публичной части для просмотра статей и административной панели для управления контентом.

## Технологический стек

### Backend
- **PHP**: 8.2.29
- **Laravel**: 12.40.1
- **База данных**: SQLite
- **Аутентификация**: Laravel Fortify v1.32.1

### Frontend
- **Inertia.js**: v2.0.10 (Laravel) + v2.2.7 (Vue)
- **Vue**: 3.5.22
- **TypeScript**: 5.2.2
- **Tailwind CSS**: 4.1.14
- **Wayfinder**: v0.1.12 (для типизации маршрутов)

### Инструменты разработки
- **Pest**: v3.8.4 (тестирование)
- **Laravel Pint**: v1.26.0 (форматирование кода)
- **Vite**: 7.0.4 (сборка фронтенда)

## Структура маршрутов

### Публичные маршруты

#### `GET /`
- **Назначение**: Главная страница со списком всех статей
- **Контроллер**: `App\Http\Controllers\PostController@index`
- **Компонент**: `resources/js/pages/Posts/Index.vue`
- **Описание**: Отображает краткие версии всех статей (превью) без пагинации. Каждая статья отображается с заголовком, кратким описанием и ссылкой на полную версию.

#### `GET /{slug}`
- **Назначение**: Просмотр полной версии статьи
- **Контроллер**: `App\Http\Controllers\PostController@show`
- **Компонент**: `resources/js/pages/Posts/Show.vue`
- **Описание**: Отображает полную версию статьи по её slug. Используется для SEO-дружественных URL.

### Административные маршруты

#### `GET /adm`
- **Назначение**: Административная панель для управления статьями
- **Контроллер**: `App\Http\Controllers\Admin\PostController@index`
- **Компонент**: `resources/js/pages/Admin/Posts/Index.vue`
- **Middleware**: `auth`, `verified`
- **Описание**: Список всех статей с возможностью управления (создание, редактирование, удаление).

#### `GET /adm/posts/create`
- **Назначение**: Форма создания новой статьи
- **Контроллер**: `App\Http\Controllers\Admin\PostController@create`
- **Компонент**: `resources/js/pages/Admin/Posts/Create.vue`
- **Middleware**: `auth`, `verified`

#### `POST /adm/posts`
- **Назначение**: Сохранение новой статьи
- **Контроллер**: `App\Http\Controllers\Admin\PostController@store`
- **Request**: `App\Http\Requests\Admin\PostStoreRequest`
- **Middleware**: `auth`, `verified`

#### `GET /adm/posts/{post}/edit`
- **Назначение**: Форма редактирования статьи
- **Контроллер**: `App\Http\Controllers\Admin\PostController@edit`
- **Компонент**: `resources/js/pages/Admin/Posts/Edit.vue`
- **Middleware**: `auth`, `verified`

#### `PUT/PATCH /adm/posts/{post}`
- **Назначение**: Обновление статьи
- **Контроллер**: `App\Http\Controllers\Admin\PostController@update`
- **Request**: `App\Http\Requests\Admin\PostUpdateRequest`
- **Middleware**: `auth`, `verified`

#### `DELETE /adm/posts/{post}`
- **Назначение**: Удаление статьи
- **Контроллер**: `App\Http\Controllers\Admin\PostController@destroy`
- **Middleware**: `auth`, `verified`

## Структура базы данных

### Таблица `posts`

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');                    // Заголовок статьи
    $table->string('slug')->unique();           // URL-friendly идентификатор
    $table->text('excerpt')->nullable();        // Краткое описание для превью
    $table->longText('content');                // Полное содержание статьи
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Автор статьи
    $table->timestamp('published_at')->nullable(); // Дата публикации
    $table->timestamps();
    
    $table->index('slug');
    $table->index('published_at');
});
```

## Модели

### `App\Models\Post`

```php
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'user_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### Связь с `User`

Модель `User` получает связь с постами:

```php
public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}
```

## Контроллеры

### `App\Http\Controllers\PostController`

Публичный контроллер для отображения статей:

- `index()`: Получение списка всех опубликованных статей для главной страницы
- `show($slug)`: Получение полной версии статьи по slug

### `App\Http\Controllers\Admin\PostController`

Административный контроллер для управления статьями:

- `index()`: Список всех статей (включая неопубликованные)
- `create()`: Отображение формы создания статьи
- `store(PostStoreRequest $request)`: Сохранение новой статьи
- `edit(Post $post)`: Отображение формы редактирования
- `update(PostUpdateRequest $request, Post $post)`: Обновление статьи
- `destroy(Post $post)`: Удаление статьи

## Form Requests

### `App\Http\Requests\Admin\PostStoreRequest`

Валидация при создании статьи:
- `title`: обязательное, строка, максимум 255 символов
- `slug`: обязательное, строка, уникальное в таблице posts, формат slug
- `excerpt`: опциональное, текст
- `content`: обязательное, текст
- `published_at`: опциональное, дата

### `App\Http\Requests\Admin\PostUpdateRequest`

Валидация при обновлении статьи (аналогично PostStoreRequest, но slug может быть не уникальным для текущей статьи).

## Frontend компоненты

### Публичные страницы

#### `resources/js/pages/Posts/Index.vue`
- Отображает список всех опубликованных статей
- Каждая статья показывает: заголовок, краткое описание (excerpt), дату публикации
- Ссылка на полную версию статьи через slug

#### `resources/js/pages/Posts/Show.vue`
- Отображает полную версию статьи
- Показывает: заголовок, дату публикации, автора, полное содержание
- Навигация назад к списку статей

### Административные страницы

#### `resources/js/pages/Admin/Posts/Index.vue`
- Таблица со списком всех статей
- Кнопки для создания, редактирования и удаления
- Фильтрация по статусу публикации

#### `resources/js/pages/Admin/Posts/Create.vue`
- Форма создания новой статьи
- Поля: title, slug, excerpt, content, published_at
- Использует компонент `<Form>` из Inertia.js

#### `resources/js/pages/Admin/Posts/Edit.vue`
- Форма редактирования существующей статьи
- Аналогична форме создания, но с предзаполненными данными

## Middleware и авторизация

### Middleware
- `auth`: Проверка аутентификации пользователя
- `verified`: Проверка подтверждения email (для административных маршрутов)

### Авторизация
Административные маршруты доступны только аутентифицированным пользователям. В будущем можно добавить роли (например, `admin`) для более детального контроля доступа.

## Генерация slug

Slug генерируется автоматически из заголовка статьи при создании/обновлении. Используется Laravel Str::slug() для создания URL-friendly строки.

## Wayfinder интеграция

Все маршруты генерируются через Wayfinder для обеспечения типизации на фронтенде:

```typescript
// Пример использования на фронтенде
import { index, show } from '@/actions/App/Http/Controllers/PostController';
import { index as adminIndex } from '@/actions/App/Http/Controllers/Admin/PostController';

// Публичные маршруты
index() // { url: "/", method: "get" }
show('my-article-slug') // { url: "/my-article-slug", method: "get" }

// Административные маршруты
adminIndex() // { url: "/adm", method: "get" }
```

## Тестирование

### Feature тесты

- `tests/Feature/PostTest.php`: Тесты публичных маршрутов (просмотр списка и отдельной статьи)
- `tests/Feature/Admin/PostTest.php`: Тесты административных маршрутов (CRUD операции)

### Использование Pest

Все тесты написаны с использованием Pest PHP:

```php
it('displays list of published posts', function () {
    $post = Post::factory()->published()->create();
    
    $response = $this->get('/');
    
    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Posts/Index')
        ->has('posts', 1)
    );
});
```

## Структура файлов

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php          # Публичный контроллер
│   │   └── Admin/
│   │       └── PostController.php       # Административный контроллер
│   └── Requests/
│       └── Admin/
│           ├── PostStoreRequest.php
│           └── PostUpdateRequest.php
├── Models/
│   └── Post.php

database/
├── migrations/
│   └── YYYY_MM_DD_HHMMSS_create_posts_table.php
└── factories/
    └── PostFactory.php

resources/js/
├── pages/
│   ├── Posts/
│   │   ├── Index.vue                    # Список статей
│   │   └── Show.vue                      # Полная версия статьи
│   └── Admin/
│       └── Posts/
│           ├── Index.vue                 # Админка: список
│           ├── Create.vue                # Админка: создание
│           └── Edit.vue                  # Админка: редактирование

routes/
└── web.php                               # Регистрация маршрутов

tests/
├── Feature/
│   ├── PostTest.php
│   └── Admin/
│       └── PostTest.php
```

## Особенности реализации

1. **Без пагинации**: Список статей на главной странице отображает все статьи без разбиения на страницы (как указано в требованиях).

2. **Slug-based routing**: Использование slug вместо ID для публичных URL обеспечивает SEO-дружественность.

3. **Inertia.js**: Все страницы используют Inertia.js для SPA-подобного опыта без необходимости создания отдельного API.

4. **Wayfinder**: Автоматическая генерация типизированных функций для маршрутов на фронтенде.

5. **Form Requests**: Валидация вынесена в отдельные классы Form Request для соблюдения принципов SOLID.

6. **Factories**: Использование Laravel Factories для создания тестовых данных.

## Будущие улучшения

- Добавление категорий/тегов для статей
- Система комментариев
- Поиск по статьям
- RSS-лента
- Система ролей для более детального контроля доступа
- Редактор с поддержкой Markdown или WYSIWYG
- Загрузка изображений для статей
- Кэширование для улучшения производительности

