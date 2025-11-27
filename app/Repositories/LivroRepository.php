<?php
// app/Repositories/LivroRepository.php
namespace App\Repositories;

use App\Models\Livro;

class LivroRepository
{
    // READ: Lista paginada
    public function index($perPage = 15)
    {
        return Livro::paginate($perPage);
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