<?php 
// Dentro de: routes/web.php

use Illuminate\Support\Facades\Route; 
// IMPORTAR: Adicione a linha do seu Controller aqui!
use App\Http\Controllers\Api\LivroController; 

Route::get('/', function () {
   dump('Acesse /livros para ver a lista de livros.');
    return view('welcome');
});

// ROTAS DA API (COLOCADAS AQUI POR EXIGÊNCIA DA INFRAESTRUTURA)
// Estas rotas agora são acessadas SEM o prefixo /api/
Route::apiResource('livros', LivroController::class); 
Route::get('relatorio/livros', [LivroController::class, 'relatorio']);