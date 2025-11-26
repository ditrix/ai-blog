# Задачи для QA Engineer

## Роль
QA инженер - написание и запуск тестов для обеспечения качества кода

## Статус: ⏳ Ожидает начала

## Зависимости

- ⚠️ **Требуется**: Завершение задач database-engineer (модели и factories)
- ⚠️ **Требуется**: Завершение задач backend-api-developer (контроллеры)
- ⚠️ **Требуется**: Завершение задач frontend-public-developer (публичные компоненты)
- ⚠️ **Требуется**: Завершение задач frontend-admin-developer (административные компоненты)

---

## Этап 7: Тестирование

### 7.1 Feature тесты для публичных маршрутов
**Приоритет**: Высокий  
**Оценка времени**: 1.5 часа

- [ ] Создать `tests/Feature/PostTest.php`
  - Команда: `php artisan make:test PostTest --pest`
- [ ] Тест `it('displays list of published posts')`:
  ```php
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
  ```
  - Создать опубликованные и неопубликованные статьи
  - Проверить, что на главной странице отображаются только опубликованные
  - Проверить структуру данных через assertInertia
- [ ] Тест `it('displays single post by slug')`:
  ```php
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
  ```
  - Создать опубликованную статью
  - Проверить отображение полной версии статьи
  - Проверить структуру данных
- [ ] Тест `it('returns 404 for non-existent post')`:
  ```php
  it('returns 404 for non-existent post', function () {
      $response = $this->get('/non-existent-slug');
      
      $response->assertNotFound();
  });
  ```
  - Проверить 404 для несуществующего slug
- [ ] Тест `it('returns 404 for unpublished post')`:
  ```php
  it('returns 404 for unpublished post', function () {
      $user = User::factory()->create();
      $post = Post::factory()->unpublished()->for($user)->create();
      
      $response = $this->get("/{$post->slug}");
      
      $response->assertNotFound();
  });
  ```
  - Проверить 404 для неопубликованной статьи

**Результат**: Feature тесты для публичных маршрутов написаны и проходят

---

### 7.2 Feature тесты для административных маршрутов
**Приоритет**: Высокий  
**Оценка времени**: 2 часа

- [ ] Создать `tests/Feature/Admin/PostTest.php`
  - Команда: `php artisan make:test Admin/PostTest --pest`
- [ ] Тест `it('displays list of all posts for authenticated user')`:
  ```php
  it('displays list of all posts for authenticated user', function () {
      $user = User::factory()->create();
      $publishedPost = Post::factory()->published()->for($user)->create();
      $unpublishedPost = Post::factory()->unpublished()->for($user)->create();
      
      $response = $this->actingAs($user)->get('/adm');
      
      $response->assertSuccessful();
      $response->assertInertia(fn (Assert $page) => $page
          ->component('Admin/Posts/Index')
          ->has('posts', 2)
      );
  });
  ```
  - Авторизовать пользователя
  - Создать статьи
  - Проверить отображение всех статей в админке
- [ ] Тест `it('allows authenticated user to create post')`:
  ```php
  it('allows authenticated user to create post', function () {
      $user = User::factory()->create();
      
      $response = $this->actingAs($user)->post('/adm/posts', [
          'title' => 'Test Post',
          'slug' => 'test-post',
          'content' => 'Test content',
      ]);
      
      $response->assertRedirect('/adm');
      $this->assertDatabaseHas('posts', [
          'title' => 'Test Post',
          'slug' => 'test-post',
          'user_id' => $user->id,
      ]);
  });
  ```
  - Авторизовать пользователя
  - Отправить POST запрос с валидными данными
  - Проверить создание статьи в БД
  - Проверить редирект
- [ ] Тест `it('validates post creation data')`:
  ```php
  it('validates post creation data', function () {
      $user = User::factory()->create();
      
      $response = $this->actingAs($user)->post('/adm/posts', []);
      
      $response->assertSessionHasErrors(['title', 'slug', 'content']);
  });
  
  it('validates unique slug', function () {
      $user = User::factory()->create();
      Post::factory()->create(['slug' => 'existing-slug']);
      
      $response = $this->actingAs($user)->post('/adm/posts', [
          'title' => 'Test Post',
          'slug' => 'existing-slug',
          'content' => 'Test content',
      ]);
      
      $response->assertSessionHasErrors(['slug']);
  });
  ```
  - Проверить валидацию обязательных полей
  - Проверить валидацию уникальности slug
- [ ] Тест `it('allows authenticated user to update post')`:
  ```php
  it('allows authenticated user to update post', function () {
      $user = User::factory()->create();
      $post = Post::factory()->for($user)->create();
      
      $response = $this->actingAs($user)->put("/adm/posts/{$post->id}", [
          'title' => 'Updated Title',
          'slug' => $post->slug,
          'content' => 'Updated content',
      ]);
      
      $response->assertRedirect('/adm');
      $this->assertDatabaseHas('posts', [
          'id' => $post->id,
          'title' => 'Updated Title',
      ]);
  });
  ```
  - Создать статью
  - Отправить PUT запрос с обновленными данными
  - Проверить обновление в БД
- [ ] Тест `it('allows authenticated user to delete post')`:
  ```php
  it('allows authenticated user to delete post', function () {
      $user = User::factory()->create();
      $post = Post::factory()->for($user)->create();
      
      $response = $this->actingAs($user)->delete("/adm/posts/{$post->id}");
      
      $response->assertRedirect('/adm');
      $this->assertDatabaseMissing('posts', ['id' => $post->id]);
  });
  ```
  - Создать статью
  - Отправить DELETE запрос
  - Проверить удаление из БД
- [ ] Тест `it('redirects unauthenticated user to login')`:
  ```php
  it('redirects unauthenticated user to login', function () {
      $response = $this->get('/adm');
      
      $response->assertRedirect('/login');
  });
  
  it('redirects unverified user', function () {
      $user = User::factory()->unverified()->create();
      
      $response = $this->actingAs($user)->get('/adm');
      
      $response->assertRedirect('/email/verify');
  });
  ```
  - Проверить редирект для неавторизованных пользователей
  - Проверить редирект для неподтвержденных пользователей

**Результат**: Feature тесты для административных маршрутов написаны и проходят

---

### 7.3 Unit тесты для модели Post
**Приоритет**: Средний  
**Оценка времени**: 45 минут

- [ ] Создать `tests/Unit/PostTest.php`
  - Команда: `php artisan make:test PostTest --unit --pest`
- [ ] Тест связей:
  ```php
  it('belongs to user', function () {
      $user = User::factory()->create();
      $post = Post::factory()->for($user)->create();
      
      expect($post->user)->toBeInstanceOf(User::class);
      expect($post->user->id)->toBe($user->id);
  });
  ```
  - Проверить связь `user()`
- [ ] Тест scopes:
  ```php
  it('filters published posts', function () {
      $user = User::factory()->create();
      $publishedPost = Post::factory()->published()->for($user)->create();
      $unpublishedPost = Post::factory()->unpublished()->for($user)->create();
      
      $publishedPosts = Post::published()->get();
      
      expect($publishedPosts)->toHaveCount(1);
      expect($publishedPosts->first()->id)->toBe($publishedPost->id);
  });
  ```
  - Проверить scope `published()`
- [ ] Тест casts:
  ```php
  it('casts published_at to datetime', function () {
      $post = Post::factory()->create(['published_at' => '2024-01-01 12:00:00']);
      
      expect($post->published_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
  });
  ```
  - Проверить cast для `published_at`

**Результат**: Unit тесты для модели Post написаны и проходят

---

### 7.4 Запуск тестов
**Приоритет**: Высокий  
**Оценка времени**: 30 минут

- [ ] Запустить все тесты:
  - Команда: `php artisan test`
- [ ] Запустить конкретные тесты для проверки:
  - `php artisan test tests/Feature/PostTest.php`
  - `php artisan test tests/Feature/Admin/PostTest.php`
  - `php artisan test tests/Unit/PostTest.php`
- [ ] Исправить найденные ошибки:
  - Анализировать ошибки тестов
  - Исправлять код или тесты по необходимости
  - Перезапускать тесты после исправлений
- [ ] Добиться 100% покрытия функциональности:
  - Проверить покрытие кода (если используется инструмент)
  - Убедиться, что все основные сценарии покрыты тестами

**Результат**: Все тесты написаны и проходят успешно

---

## Чеклист завершения этапа

- [ ] Feature тесты для публичных маршрутов написаны и проходят
- [ ] Feature тесты для административных маршрутов написаны и проходят
- [ ] Unit тесты для модели Post написаны и проходят
- [ ] Все тесты запускаются без ошибок
- [ ] Покрытие функциональности достаточное
- [ ] Тесты используют правильные паттерны Pest

## Примечания

- Использовать Pest для написания тестов
- Использовать factories для создания тестовых данных
- Проверять как успешные, так и неуспешные сценарии
- Использовать `assertInertia` для проверки Inertia компонентов
- Использовать `actingAs()` для авторизации пользователей в тестах
- Проверять валидацию через `assertSessionHasErrors()`

## Зависимости

- ⚠️ **Требуется**: Завершение всех предыдущих этапов

## Блокирует

- ⚠️ devops-engineer - не может начать финальную проверку без тестов

