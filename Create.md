## Создание мультиязычного сайта на Laravel
* Устанавливаем Laravel 5.8
    ```
    composer create-project --prefer-dist laravel/laravel project
    cd project
    ```
* Устанавливаем коннект с базой данных и создаем авторизацию
  ```
  php artisan make:auth
  php artisan migrate
  ```
* Добавляем переменную locales в файл config/app.php
    ```php
    'languages' => ['en','ru'],
    ```

* В файле routes/web.php создаем редирект для главной '/'
    ```php
    Route::get('/', function () {
        return redirect(config('app.locale'));
    });
    ```
* Создаем роутер для переключателя языков
    ```php
    Route::get('/locale/{locale}', function ($locale) {
    
        if(url()->previous() != url()->current()){
            $segments = str_replace(url('/'), '', url()->previous());
            $segments = array_filter(explode('/', $segments));
            $segments[1] = $locale;
            $uri = implode("/", $segments);
            return redirect($uri);
        } else {
            return redirect()->route('home');
        }

    })->name('switcher');
    ```

* Создаем Midleware
    ```php
    php artisan make:middleware SetLocale
    ```
  Добавляем в него код.
    ```php
    // App\Http\Middleware\Setlocale.php
    public function handle($request, Closure $next, $locale)
    {
        app()->setLocale($locale);
        return $next($request);
    }
    ```
*   Регистрируем этот класс в app\Http\Kernel.php в массиве $ routeMiddleware
    ```php
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        // ...
        'setlocale' => \App\Http\Middleware\SetLocale::class,
    ];
    ```
*  Добавляем
    ```php
    Route::group([
        'prefix' => $locale,
        'where' => ['locale' => '[a-zA-Z]{2}'],
        'middleware' => ["setlocale:$locale"],
    ], function() {
        //...
    });
    ```
 * В файлвх LoginController, RegisterController,  ResetPasswordControllerи VerificationController
  добавляем переадресацию после аутентификации 
  ```php
    protected function redirectTo()
    {
        return app()->getLocale().'/';
    }
  ```   
* А в файл LoginController переадресацию после выхода из приложения
   ```php
    public function logout()
    {
        Auth::logout();
        return redirect(app()->getLocale().'/');
    }
   ```
* Добавляем переключатель в шапку
    ```php
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
         <ul class="navbar-nav mr-auto">
             @foreach(config('app.languages') as $locale)
 
                 <li class="nav-item @if(request()->segment(1) == $locale) active @endif">
                     <a class="nav-link capitalize" href="{{ route('switcher',['locale' => $locale]) }}">{{$locale}}</a>
                 </li>
             @endforeach
         </ul>
       ....
    ```
* готово
    
    