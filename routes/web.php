<?php

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
    return view('dashboard');
});

Route::get('/pantry/shared/{token}', function ($token) {
    return redirect('ladespensa://pantry/shared/' . $token);
});

Route::get('/join/{token}', function ($token) {
    $safeToken = e($token);
    $deepLink  = 'ladespensa://pantry/shared/' . $safeToken;

    return response(<<<HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Unirse a despensa - La Despensa</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
      <script>
        window.location.href = "{$deepLink}";
        setTimeout(function() {
          document.getElementById('fallback').style.display = 'block';
        }, 2000);
      </script>
      <div id="fallback" style="display:none; font-family:sans-serif; padding:24px; text-align:center">
        <h2>Únete a la despensa en La Despensa</h2>
        <p>Abre la app e introduce este código en <b>Despensa → Unirme con código</b>:</p>
        <p style="font-size:20px; font-weight:bold; letter-spacing:2px">{$safeToken}</p>
      </div>
    </body>
    </html>
    HTML, 200, ['Content-Type' => 'text/html']);
});
