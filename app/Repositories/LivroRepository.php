<?php
// app/Repositories/LivroRepository.php
namespace App\Repositories;

use App\Models\Livro;

class LivroRepository
{
    // READ: Lista paginada COM FILTROS
    public function index(array $filters = [], $perPage = 10)
    {
        // 1. Inicia a query
        $query = Livro::query();

        // 2. Aplica Filtro por Nome/Autor (Busca Geral)
        if (!empty($filters['nome'])) {
            $searchTerm = $filters['nome'];
            // Busca livros onde o nome ou o autor contenham o termo de busca (like)
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'like', '%' . $searchTerm . '%')
                  ->orWhere('autor', 'like', '%' . $searchTerm . '%');
            });
        }

        // 3. Aplica Filtro por Categoria
        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        // 4. Aplica Filtro por Tipo
        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        // 5. Executa a query e aplica a paginação
        return $query->paginate($perPage);
    }

    // CREATE: Cria livro
    public function store(array $data)
    {
        return Livro::create($data);
    }

    // READ: Encontra livro pelo ID
    public function find($id)
    {
        return Livro::findOrFail($id); 
    }

    // UPDATE: Atualiza livro existente
    public function update($id, array $data)
    {
        $livro = Livro::findOrFail($id);
        $livro->update($data); 
        return $livro; 		
    }

    // DELETE: Apaga livro
    public function delete($id)
    {
        $livro = Livro::findOrFail($id);
        $livro->delete();
        return true; 
    }
    
    // RELATÓRIO: Busca dados com filtros
    public function getRelatorioData(array $filters)
    {
        $query = Livro::query();

        // 1. Filtragem por Categoria
        if (isset($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        // 2. Filtragem por Período (created_at)
        if (isset($filters['data_inicio']) && isset($filters['data_fim'])) {
            $query->whereBetween('created_at', [
                $filters['data_inicio'],
                $filters['data_fim'] . ' 23:59:59' // Inclui o dia todo
            ]);
        }
        // Ordena por data de criação para melhor visualização
        return $query->orderBy('created_at', 'desc')->get(); 
    }
}