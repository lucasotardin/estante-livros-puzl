<?php 
// OBRIGATÓRIO NO INÍCIO DO ARQUIVO

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // 1. Configuração de Rotas
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Mantém o web.php funcionando
        api: __DIR__.'/../routes/api.php', // Mantém o api.php (embora vamos usar web)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    // 2. Configuração de Middlewares (A Solução do CSRF)
    ->withMiddleware(function (Middleware $middleware) {
        
        // Esta linha desativa a proteção CSRF para as suas rotas de API.
        $middleware->validateCsrfTokens(except: [
            'livros',         // Permite POST/PUT/DELETE em /livros
            'livros/*',       // Permite rotas PUT/DELETE com ID
            'relatorio/livros', // Permite GET no relatório
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Bloco de tratamento de exceções
    })->create();