<?php
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    try {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => str_replace('App\Http\Controllers\\', '', $route->getActionName()),
                'middleware' => implode(', ', (array) $route->middleware()),
            ];
        })->filter(function ($route) {
            // Filter out internal Laravel/Sanctum routes
            return !str_contains($route['uri'], '_ignition') && 
                   !str_contains($route['uri'], 'sanctum');
        })->values();

        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>API Dashboard</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
            <style>
                body { font-family: "Inter", sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 40px; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
                h1 { font-size: 24px; font-weight: 600; margin-bottom: 24px; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
                th { background-color: #f1f5f9; font-weight: 600; color: #475569; text-transform: uppercase; font-size: 12px; letter-spacing: 0.05em; }
                tr:hover { background-color: #f8fafc; }
                .method { font-weight: 600; font-size: 12px; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-right: 4px; }
                .method-GET { background-color: #dcfce7; color: #166534; }
                .method-POST { background-color: #dbeafe; color: #1e40af; }
                .method-PUT, .method-PATCH { background-color: #fef9c3; color: #854d0e; }
                .method-DELETE { background-color: #fee2e2; color: #991b1b; }
                .uri { font-family: monospace; color: #2563eb; font-weight: 500; }
                .middleware { font-size: 12px; color: #64748b; }
                .action { font-size: 13px; color: #334155; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Backend API Dashboard</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>URI</th>
                            <th>Middleware</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $routes->map(function ($r) {
                            $method_class = 'method-' . explode('|', $r['method'])[0];
                            return "<tr>
                                <td><span class='method {$method_class}'>{$r['method']}</span></td>
                                <td class='uri'>/{$r['uri']}</td>
                                <td class='middleware'>{$r['middleware']}</td>
                                <td class='action'>{$r['action']}</td>
                            </tr>";
                        })->implode('') . '
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        ';
    } catch (Exception $e) {
        return "Error loading dashboard: " . $e->getMessage();
    }
});

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Resource Routes will go here
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    });
});
