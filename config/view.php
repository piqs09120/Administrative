<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | View Debugger
    |--------------------------------------------------------------------------
    |
    | Enabling this option will display the current view name and data if an
    | exception occurs while rendering a view. This can be helpful when
    | you are getting an error and are unsure of which view file is being
    | rendered. This option defaults to false and should only be used in
    | development.
    |
    */

    'debug' => env('VIEW_DEBUG', false),

]; 