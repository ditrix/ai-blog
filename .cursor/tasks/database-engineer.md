# Задачи для Database Engineer

## Роль
Инженер по базам данных - создание структуры БД, моделей, factories и seeders

## Статус: ⏳ Ожидает начала

## Этап 1: Подготовка базы данных и моделей

### 1.1 Создание миграции для таблицы posts
**Приоритет**: Критический  
**Оценка времени**: 30 минут

- [ ] Создать миграцию `create_posts_table.php`
  - Команда: `php artisan make:migration create_posts_table`
- [ ] Определить структуру таблицы:
  - `id` (primary key)
  - `title` (string)
  - `slug` (string, unique)
  - `excerpt` (text, nullable)
  - `content` (longText)
  - `user_id` (foreign key)
  - `published_at` (timestamp, nullable)
  - `timestamps`
- [ ] Добавить индексы на `slug` и `published_at`
- [ ] Запустить миграцию и проверить создание таблицы
  - Команда: `php artisan migrate`
  - Проверить через `php artisan tinker` или `mcp_laravel-boost_database-schema`

**Результат**: Таблица `posts` создана в базе данных со всеми необходимыми полями и индексами

---

### 1.2 Создание модели Post
**Приоритет**: Критический  
**Оценка времени**: 20 минут

- [ ] Создать модель `App\Models\Post`
  - Команда: `php artisan make:model Post`
- [ ] Настроить `$fillable` поля:
  ```php
  protected $fillable = [
      'title',
      'slug',
      'excerpt',
      'content',
      'user_id',
      'published_at',
  ];
  ```
- [ ] Добавить метод `casts()` для `published_at`:
  ```php
  protected function casts(): array
  {
      return [
          'published_at' => 'datetime',
      ];
  }
  ```
- [ ] Добавить связь `user()` (BelongsTo):
  ```php
  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class);
  }
  ```
- [ ] Добавить scope для опубликованных статей `scopePublished()`:
  ```php
  public function scopePublished(Builder $query): Builder
  {
      return $query->whereNotNull('published_at');
  }
  ```

**Результат**: Модель Post создана со всеми необходимыми связями и scopes

---

### 1.3 Обновление модели User
**Приоритет**: Высокий  
**Оценка времени**: 10 минут

- [ ] Добавить связь `posts()` (HasMany) в модель User:
  ```php
  public function posts(): HasMany
  {
      return $this->hasMany(Post::class);
  }
  ```
- [ ] Проверить корректность связей через tinker:
  ```php
  $user = User::first();
  $user->posts; // Должно работать
  ```

**Результат**: Модель User имеет связь с Post

---

### 1.4 Создание Factory для Post
**Приоритет**: Высокий  
**Оценка времени**: 30 минут

- [ ] Создать `PostFactory`
  - Команда: `php artisan make:factory PostFactory`
- [ ] Настроить генерацию тестовых данных:
  ```php
  return [
      'title' => fake()->sentence(),
      'slug' => fake()->slug(),
      'excerpt' => fake()->paragraph(),
      'content' => fake()->text(),
      'user_id' => User::factory(),
      'published_at' => fake()->optional()->dateTime(),
  ];
  ```
- [ ] Добавить state `published()` для создания опубликованных статей:
  ```php
  public function published(): static
  {
      return $this->state(fn (array $attributes) => [
          'published_at' => now(),
      ]);
  }
  ```
- [ ] Добавить state `unpublished()` для неопубликованных статей:
  ```php
  public function unpublished(): static
  {
      return $this->state(fn (array $attributes) => [
          'published_at' => null,
      ]);
  }
  ```
- [ ] Протестировать factory:
  ```php
  Post::factory()->published()->create();
  Post::factory()->unpublished()->create();
  ```

**Результат**: Factory создан и протестирован, поддерживает states для published/unpublished

---

### 1.5 Создание Seeder (опционально)
**Приоритет**: Низкий  
**Оценка времени**: 20 минут

- [ ] Создать `PostSeeder` для начальных данных
  - Команда: `php artisan make:seeder PostSeeder`
- [ ] Настроить создание тестовых статей:
  ```php
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
  ```
- [ ] Зарегистрировать seeder в `DatabaseSeeder.php`
- [ ] Запустить seeder для проверки
  - Команда: `php artisan db:seed --class=PostSeeder`

**Результат**: Seeder создан и может генерировать тестовые данные

---

## Чеклист завершения этапа

- [ ] Миграция создана и выполнена успешно
- [ ] Модель Post создана со всеми связями и scopes
- [ ] Модель User обновлена со связью posts
- [ ] Factory создан и протестирован
- [ ] Seeder создан (опционально)
- [ ] Все связи проверены через tinker
- [ ] Код отформатирован через Pint

## Примечания

- При создании миграции использовать правильные типы данных Laravel
- Убедиться, что foreign key имеет `onDelete('cascade')`
- Проверить индексы для производительности запросов
- Factory должен использовать существующие паттерны проекта

## Зависимости

- ✅ Нет зависимостей - это первый этап

## Блокирует

- ⚠️ backend-api-developer - не может начать без моделей
- ⚠️ qa-engineer - не может писать тесты без моделей и factories

