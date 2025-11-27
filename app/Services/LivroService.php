<?php
// app/Services/LivroService.php
namespace App\Services;

use App\Repositories\LivroRepository;

class LivroService
{
    protected $livroRepository;

    public function __construct(LivroRepository $livroRepository)
    {
        $this->livroRepository = $livroRepository;
    }

    // --- MÉTODOS CRUD ---
    public function getLivrosPaginados()
    {
        return $this->livroRepository->index();
    }
    public function createLivro(array $data)
    {
        return $this->livroRepository->store($data);
    }
    public function getLivroById($id)
    {
        return $this->livroRepository->find($id);
    }
    public function updateLivro($id, array $data)
    {
        return $this->livroRepository->update($id, $data);
    }
    public function deleteLivro($id)
    {
        return $this->livroRepository->delete($id);
    }

    // --- MÉTODO RELATÓRIO ---
    public function getRelatorio(array $filters)
    {
        $dados = $this->livroRepository->getRelatorioData($filters);
        
        // Formatação dos Dados para Dashboard
        $total_geral = $dados->count();
        $total_fisico = $dados->where('tipo', 'fisico')->count();
        $total_digital = $dados->where('tipo', 'digital')->count();
        
        return [
            'total_geral' => $total_geral,
            'total_fisico' => $total_fisico,
            'total_digital' => $total_digital,
            // Detalhes agrupados por categoria
            'detalhes' => $dados->groupBy('categoria')->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'count' => $items->count(),
                    'fisico' => $items->where('tipo', 'fisico')->count(),
                    'digital' => $items->where('tipo', 'digital')->count(),
                ];
            })->values(),
        ];
    }
}