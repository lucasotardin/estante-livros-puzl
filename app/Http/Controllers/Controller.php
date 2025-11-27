<?php
// app/Http/Controllers/Api/LivroController.php

namespace App\Http\Controllers\Api;

// ... (imports já existentes) ...
use App\Http\Requests\LivroUpdateRequest; // Importe a nova Validação para UPDATE
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LivroController extends Controller
{
    // ... (construtor, index, store já implementados) ...

    // GET /api/livros/{id}
    public function show(string $id)
    {
        $livro = $this->livroService->getLivroById($id);
        return response()->json($livro);
    }

    // PUT/PATCH /api/livros/{id}
    // Usa LivroUpdateRequest para validação
    public function update(LivroUpdateRequest $request, string $id)
    {
        try {
            $livro = $this->livroService->updateLivro($id, $request->validated());
            // Retorna o recurso atualizado com status 200 OK
            return response()->json($livro, Response::HTTP_OK); 
            
        } catch (\Exception $e) {
            // Se o ID não existir, a exception do findOrFail no Repository já cuida disso (404)
            return response()->json(['erro' => 'Erro interno ao atualizar o livro.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // DELETE /api/livros/{id}
    public function destroy(string $id)
    {
        $this->livroService->deleteLivro($id);
        // Retorna uma resposta vazia com status 204 No Content para DELETE bem-sucedido
        return response()->json(null, Response::HTTP_NO_CONTENT); 
    }
}