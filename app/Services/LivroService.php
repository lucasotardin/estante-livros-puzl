<?php
// app/Services/LivroService.php
namespace App\Services;

use App\Repositories\LivroRepository; // Mantém a injeção de dependência

class LivroService
{
    protected $livroRepository;

    public function __construct(LivroRepository $livroRepository)
    {
        $this->livroRepository = $livroRepository;
    }

    // --- MÉTODOS CRUD ---

    /**
     * Obtém livros paginados, aceitando filtros de busca.
     * @param array $filters Parâmetros de filtro (nome, categoria, tipo, page)
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getLivrosPaginados(array $filters = [])
    {
        // REPASSA O ARRAY DE FILTROS PARA O REPOSITORY
        return $this->livroRepository->index($filters);
    }
    
    // Os demais métodos CRUD permanecem inalterados, pois não precisam de filtros
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

    /**
     * Processa os dados do repositório para gerar o relatório/dashboard.
     * @param array $filters Filtros de busca e período para o relatório.
     * @return array
     */
    public function getRelatorio(array $filters)
    {
        // REPASSA OS FILTROS PARA O REPOSITORY
        $dados = $this->livroRepository->getRelatorioData($filters);
        
        // Formatação dos Dados para Dashboard (Obrigatório no Service Layer)
        $total_geral = $dados->count();
        $total_fisico = $dados->where('tipo', 'Físico')->count(); // Usar "Físico" e "Digital" conforme o Front-End
        $total_digital = $dados->where('tipo', 'Digital')->count();
        
        return [
            'total_geral' => $total_geral,
            'total_fisico' => $total_fisico,
            'total_digital' => $total_digital,
            // Detalhes agrupados por categoria
            'detalhes' => $dados->groupBy('categoria')->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'count' => $items->count(),
                    'fisico' => $items->where('tipo', 'Físico')->count(),
                    'digital' => $items->where('tipo', 'Digital')->count(),
                ];
            })->values(),
        ];
    }
}