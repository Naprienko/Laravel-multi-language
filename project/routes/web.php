<?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    Route::get('/', function () {
        return redirect(config('app.locale'));
    });

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

    if (in_array(request()->segment(1),config('app.languages'))){
        $locale = request()->segment(1);
    } else {
        $locale = config('app.locale');
    };

    Route::group([
        'prefix' => $locale,
        'where' => ['locale' => '[a-zA-Z]{2}'],
        'middleware' => ["setlocale:$locale"],
    ], function() {

        /*
        Route::get('/', function () {
            return view('welcome');
        });*/
        Auth::routes();

        Route::get('/', 'HomeController@index')->name('home');
    });
