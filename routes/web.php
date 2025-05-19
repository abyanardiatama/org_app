<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiController;

Route::get('/', function () {
    //go to /admin
    return redirect('/admin');
});
// Route::get('/avatars/{filename}', function ($filename) {
//     $path = storage_path('app/private/avatars/' . $filename);

//     if (!file_exists($path)) {
//         abort(404);
//     }

//     return response()->file($path);
// })->middleware('auth'); // Pastikan hanya user login yang bisa mengakses
