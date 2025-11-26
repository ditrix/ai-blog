# Задачи для Backend API Developer

## Роль
Backend разработчик API - создание контроллеров, маршрутов, Form Requests и валидации

## Статус: ⏳ Ожидает начала

## Зависимости

- ⚠️ **Требуется**: Завершение задач database-engineer (модели и миграции)

---

## Этап 2: Backend - Публичные маршруты и контроллеры

### 2.1 Создание публичного контроллера PostController
**Приоритет**: Критический  
**Оценка времени**: 45 минут

- [ ] Создать `App\Http\Controllers\PostController`
  - Команда: `php artisan make:controller PostController`
- [ ] Реализовать метод `index()`:
  ```php
  public function index(): Response
  {
      $posts = Post::query()
          ->published()
          ->with('user')
          ->orderBy('published_at', 'desc')
          ->get();
      
      return Inertia::render('Posts/Index', [
          'posts' => $posts,
      ]);
  }
  ```
  - Получение всех опубликованных статей (где `published_at` не null)
  - Сортировка по `published_at` DESC
  - Eager loading связи `user`
  - Передача данных в Inertia компонент `Posts/Index`
- [ ] Реализовать метод `show($slug)`:
  ```php
  public function show(string $slug): Response
  {
      $post = Post::query()
          ->published()
          ->where('slug', $slug)
          ->with('user')
          ->firstOrFail();
      
      return Inertia::render('Posts/Show', [
          'post' => $post,
      ]);
  }
  ```
  - Поиск статьи по slug
  - Проверка существования статьи (firstOrFail)
  - Возврат 404 если статья не найдена или не опубликована
  - Eager loading связи `user`
  - Передача данных в Inertia компонент `Posts/Show`

**Результат**: Публичный контроллер создан и работает

---

### 2.2 Регистрация публичных маршрутов
**Приоритет**: Критический  
**Оценка времени**: 15 минут

- [ ] Добавить маршрут `GET /` в `routes/web.php`:
  ```php
  Route::get('/', [PostController::class, 'index'])->name('posts.index');
  ```
  - Привязать к `PostController@index`
  - Имя маршрута: `posts.index`
- [ ] Добавить маршрут `GET /{slug}` в `routes/web.php`:
  ```php
  Route::get('/{slug}', [PostController::class, 'show'])->name('posts.show');
  ```
  - Привязать к `PostController@show`
  - Имя маршрута: `posts.show`
  - **ВАЖНО**: Убедиться, что маршрут не конфликтует с другими маршрутами (должен быть последним в файле)
- [ ] Проверить маршруты:
  - Команда: `php artisan route:list --name=posts`
  - Убедиться, что маршруты зарегистрированы правильно

**Результат**: Публичные маршруты зарегистрированы и работают

---

## Этап 3: Backend - Административные маршруты и контроллеры

### 3.1 Создание Form Request классов
**Приоритет**: Высокий  
**Оценка времени**: 45 минут

- [ ] Создать `App\Http\Requests\Admin\PostStoreRequest`
  - Команда: `php artisan make:request Admin/PostStoreRequest`
- [ ] Настроить правила валидации:
  ```php
  public function rules(): array
  {
      return [
          'title' => ['required', 'string', 'max:255'],
          'slug' => ['required', 'string', 'unique:posts,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
          'excerpt' => ['nullable', 'string'],
          'content' => ['required', 'string'],
          'published_at' => ['nullable', 'date'],
      ];
  }
  ```
  - `title`: required, string, max:255
  - `slug`: required, string, unique:posts,slug, regex для формата slug
  - `excerpt`: nullable, string
  - `content`: required, string
  - `published_at`: nullable, date
- [ ] Добавить кастомные сообщения об ошибках:
  ```php
  public function messages(): array
  {
      return [
          'title.required' => 'Заголовок обязателен для заполнения',
          'slug.unique' => 'Такой slug уже существует',
          'content.required' => 'Содержание статьи обязательно',
      ];
  }
  ```
- [ ] Создать `App\Http\Requests\Admin\PostUpdateRequest`
  - Команда: `php artisan make:request Admin/PostUpdateRequest`
- [ ] Настроить правила валидации (аналогично PostStoreRequest, но slug уникален исключая текущую статью):
  ```php
  public function rules(): array
  {
      return [
          'title' => ['required', 'string', 'max:255'],
          'slug' => [
              'required',
              'string',
              Rule::unique('posts', 'slug')->ignore($this->route('post')->id),
              'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
          ],
          'excerpt' => ['nullable', 'string'],
          'content' => ['required', 'string'],
          'published_at' => ['nullable', 'date'],
      ];
  }
  ```
  - Использовать `Rule::unique()->ignore($post->id)`

**Результат**: Form Request классы созданы с правильной валидацией

---

### 3.2 Создание административного контроллера
**Приоритет**: Высокий  
**Оценка времени**: 1.5 часа

- [ ] Создать `App\Http\Controllers\Admin\PostController`
  - Команда: `php artisan make:controller Admin/PostController --resource`
- [ ] Реализовать метод `index()`:
  ```php
  public function index(): Response
  {
      $posts = Post::query()
          ->with('user')
          ->orderBy('created_at', 'desc')
          ->get();
      
      return Inertia::render('Admin/Posts/Index', [
          'posts' => $posts,
      ]);
  }
  ```
  - Получение всех статей (включая неопубликованные)
  - Сортировка по `created_at` DESC
  - Eager loading связи `user`
  - Передача данных в Inertia компонент `Admin/Posts/Index`
- [ ] Реализовать метод `create()`:
  ```php
  public function create(): Response
  {
      return Inertia::render('Admin/Posts/Create');
  }
  ```
  - Отображение формы создания статьи
  - Передача данных в Inertia компонент `Admin/Posts/Create`
- [ ] Реализовать метод `store(PostStoreRequest $request)`:
  ```php
  public function store(PostStoreRequest $request): RedirectResponse
  {
      $data = $request->validated();
      
      // Автогенерация slug если не указан
      if (empty($data['slug'])) {
          $data['slug'] = Str::slug($data['title']);
      }
      
      $post = $request->user()->posts()->create($data);
      
      return redirect()
          ->route('admin.posts.index')
          ->with('success', 'Статья успешно создана');
  }
  ```
  - Валидация данных через PostStoreRequest
  - Автоматическая генерация slug из title (если не указан)
  - Создание статьи с привязкой к текущему пользователю
  - Редирект на список статей с сообщением об успехе
- [ ] Реализовать метод `edit(Post $post)`:
  ```php
  public function edit(Post $post): Response
  {
      return Inertia::render('Admin/Posts/Edit', [
          'post' => $post,
      ]);
  }
  ```
  - Проверка существования статьи (Route Model Binding)
  - Передача данных статьи в Inertia компонент `Admin/Posts/Edit`
- [ ] Реализовать метод `update(PostUpdateRequest $request, Post $post)`:
  ```php
  public function update(PostUpdateRequest $request, Post $post): RedirectResponse
  {
      $data = $request->validated();
      
      // Обновление slug если изменился title
      if ($post->title !== $data['title'] && empty($data['slug'])) {
          $data['slug'] = Str::slug($data['title']);
      }
      
      $post->update($data);
      
      return redirect()
          ->route('admin.posts.index')
          ->with('success', 'Статья успешно обновлена');
  }
  ```
  - Валидация данных через PostUpdateRequest
  - Обновление slug (если изменился title)
  - Сохранение изменений
  - Редирект на список статей с сообщением об успехе
- [ ] Реализовать метод `destroy(Post $post)`:
  ```php
  public function destroy(Post $post): RedirectResponse
  {
      $post->delete();
      
      return redirect()
          ->route('admin.posts.index')
          ->with('success', 'Статья успешно удалена');
  }
  ```
  - Удаление статьи
  - Редирект на список статей с сообщением об успехе

**Результат**: Административный контроллер создан со всеми CRUD операциями

---

### 3.3 Регистрация административных маршрутов
**Приоритет**: Высокий  
**Оценка времени**: 20 минут

- [ ] Добавить маршруты в `routes/web.php` с префиксом `/adm`:
  ```php
  Route::middleware(['auth', 'verified'])->prefix('adm')->name('admin.')->group(function () {
      Route::get('/', [Admin\PostController::class, 'index'])->name('posts.index');
      Route::resource('posts', Admin\PostController::class)->except(['show']);
  });
  ```
- [ ] Или создать отдельный файл `routes/admin.php` и подключить в `bootstrap/app.php`
- [ ] Зарегистрировать маршруты:
  - `GET /adm` → `Admin\PostController@index` (имя: `admin.posts.index`)
  - `GET /adm/posts/create` → `Admin\PostController@create` (имя: `admin.posts.create`)
  - `POST /adm/posts` → `Admin\PostController@store` (имя: `admin.posts.store`)
  - `GET /adm/posts/{post}/edit` → `Admin\PostController@edit` (имя: `admin.posts.edit`)
  - `PUT/PATCH /adm/posts/{post}` → `Admin\PostController@update` (имя: `admin.posts.update`)
  - `DELETE /adm/posts/{post}` → `Admin\PostController@destroy` (имя: `admin.posts.destroy`)
- [ ] Применить middleware `auth` и `verified` ко всем административным маршрутам
- [ ] Использовать Route Model Binding для `{post}`
- [ ] Проверить маршруты:
  - Команда: `php artisan route:list --name=admin`

**Результат**: Административные маршруты зарегистрированы и защищены middleware

---

## Этап 6: Дополнительная функциональность (частично)

### 6.1 Автоматическая генерация slug (Backend часть)
**Приоритет**: Средний  
**Оценка времени**: 30 минут

- [ ] Создать helper метод или trait для генерации slug
  - Вариант 1: Создать trait `HasSlug` в `app/Traits/HasSlug.php`
  - Вариант 2: Добавить метод в модель Post
- [ ] Реализовать логику:
  ```php
  protected function generateUniqueSlug(string $title, ?int $exceptId = null): string
  {
      $slug = Str::slug($title);
      $originalSlug = $slug;
      $counter = 1;
      
      while (Post::where('slug', $slug)
          ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
          ->exists()) {
          $slug = $originalSlug . '-' . $counter;
          $counter++;
      }
      
      return $slug;
  }
  ```
  - Генерация из title если slug не указан
  - Проверка уникальности slug
  - Добавление суффикса если slug уже существует (например, `-2`, `-3`)
- [ ] Интегрировать в PostStoreRequest и PostUpdateRequest:
  - В методе `prepareForValidation()` или в контроллере

**Результат**: Автоматическая генерация slug реализована на бэкенде

---

### 6.2 Обработка ошибок (Backend часть)
**Приоритет**: Средний  
**Оценка времени**: 20 минут

- [ ] Настроить обработку 404 для несуществующих статей
  - Использовать `firstOrFail()` в контроллерах
- [ ] Настроить обработку 403 для неавторизованных пользователей
  - Middleware `auth` и `verified` уже применяются
- [ ] Добавить flash сообщения для успешных операций
  - Использовать `with('success', 'Сообщение')` в редиректах
- [ ] Проверить обработку ошибок валидации
  - Inertia автоматически передает ошибки валидации на фронтенд

**Результат**: Обработка ошибок настроена

---

## Чеклист завершения этапа

- [ ] Публичный контроллер PostController создан и работает
- [ ] Публичные маршруты зарегистрированы
- [ ] Form Request классы созданы с правильной валидацией
- [ ] Административный контроллер создан со всеми методами
- [ ] Административные маршруты зарегистрированы и защищены
- [ ] Автогенерация slug реализована
- [ ] Обработка ошибок настроена
- [ ] Код отформатирован через Pint
- [ ] Все маршруты проверены через `php artisan route:list`

## Примечания

- Использовать Route Model Binding для автоматической проверки существования ресурсов
- Все административные маршруты должны быть защищены middleware `auth` и `verified`
- Использовать Inertia::render() для передачи данных на фронтенд
- Flash сообщения передаются через session

## Зависимости

- ⚠️ **Требуется**: Завершение задач database-engineer

## Блокирует

- ⚠️ frontend-public-developer - не может начать без публичных контроллеров
- ⚠️ frontend-admin-developer - не может начать без административных контроллеров
- ⚠️ qa-engineer - не может писать тесты без контроллеров

