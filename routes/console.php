<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->alert(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:status', function () {
    $this->info('--- Application Status ---');
    $this->line('PHP Version: ' . PHP_VERSION);
    $this->line('Environment: ' . app()->environment());
    $this->line('Debug Mode:  ' . (config('app.debug') ? 'Enabled' : 'Disabled'));
    
    try {
        \DB::connection()->getPdo();
        $this->info('Database:    Connected');
    } catch (\Exception $e) {
        $this->error('Database:    Connection Failed');
    }
    $this->info('--------------------------');
})->purpose('Display the current application status');

Artisan::command('db:summary', function () {
    $this->info('--- Database Summary ---');
    
    $models = [
        'User' => \App\Models\User::class,
        'Product' => \App\Models\Product::class,
        'Category' => \App\Models\Category::class,
        'Order' => \App\Models\Orders::class,
        'Cart' => \App\Models\Carts::class,
    ];

    foreach ($models as $name => $class) {
        if (class_exists($class)) {
            $count = $class::count();
            $label = ($name === 'Category') ? 'Categories' : $name . 's';
            $this->line(str_pad($label . ':', 15) . $count);
        } else {
            $this->line(str_pad($name . 's:', 15) . 'Missing Model');
        }
    }
    
    $this->info('------------------------');
})->purpose('Display a summary of the database records');

