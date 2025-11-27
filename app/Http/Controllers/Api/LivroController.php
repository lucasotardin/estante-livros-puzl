<?php
namespace App\Http\Controllers\Api;
// app/Http/Controllers/Api/LivroController.php

use App\Services\LivroService; 
use App\Http\Requests\LivroStoreRequest;
use App\Http\Requests\LivroUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LivroController extends Controller
{
    protected $livroService;

    public function __construct(LivroService $livroService)
    {
        $this->livroService = $livroService;
    }

    // 1. GET /api/livros (Listar com Paginação)
    public function index(Request $request)
    {
        $livros = $this->livroService->getLivrosPaginados();
        return response()->json($livros);
    }

    // 2. POST /api/livros (Criar)
    public function store(LivroStoreRequest $request) 
    {
        try {
            $livro = $this->livroService->createLivro($request->validated()); 
            return response()->json($livro, Response::HTTP_CREATED); 
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Erro interno ao salvar.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // 3. GET /api/livros/{id} (Visualizar)
    public function show(string $id)
    {
        $livro = $this->livroService->getLivroById($id);
        return response()->json($livro);
    }

    // 4. PUT/PATCH /api/livros/{id} (Atualizar)
    public function update(LivroUpdateRequest $request, string $id)
    {
        try {
            $livro = $this->livroService->updateLivro($id, $request->validated());
            return response()->json($livro, Response::HTTP_OK); 
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Erro interno ao atualizar.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // 5. DELETE /api/livros/{id} (Deletar)
    public function destroy(string $id)
    {
        $this->livroService->deleteLivro($id);
        return response()->json(null, Response::HTTP_NO_CONTENT); 
    }
    
    // 6. GET /api/relatorio/livros (Relatório/Dashboard)
    public function relatorio(Request $request)
    {
        $relatorio = $this->livroService->getRelatorio($request->all());
        return response()->json($relatorio);
    }
}