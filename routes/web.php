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

Route::get('/debug-storage', function () {
    return [
        'base_path' => base_path(),
        'cwd' => getcwd(),
        'storage_path' => storage_path(),
        'public_path' => public_path(),
        'files' => Storage::disk('public')->allFiles(),
    ];
});

Route::get('/', function () {
    return view('welcome');
});
