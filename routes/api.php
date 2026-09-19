<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TaskArts
|--------------------------------------------------------------------------
|
| Seluruh endpoint API didaftarkan dalam prefix `/api/v1`. Setiap modul
| memiliki file route tersendiri di `routes/api/` agar benar-benar modular
| dan mudah ditambah/diambil tanpa mengganggu modul lain.
|
*/

Route::prefix('v1')->group(function (): void {
    foreach (glob(__DIR__.'/api/*.php') as $routeFile) {
        require $routeFile;
    }
});
