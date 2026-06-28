<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\File;

Route::get('/where-am-i', function () {
    return [
        'base_path' => base_path(),
        'storage_path' => storage_path(),
        'public_path' => public_path(),
        'cwd' => getcwd(),
        'files' => File::allFiles(storage_path('app/public')),
    ];
});

Route::get('/', function () {
    return view('welcome');
});
