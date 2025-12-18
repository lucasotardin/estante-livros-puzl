<?php

namespace App\Services;

use App\Repositories\LivroRepository;

class LivroService
{
    protected $livroRepository;

    public function __construct(LivroRepository $livroRepository)
    {
        $this->livroRepository = $livroRepository;
    }

    // --- MÉTODOS CRUD (Essenciais para a lista funcionar) ---

    public function getLivrosPaginados(array $filters = [])
    {
        return $this->livroRepository->index($filters);
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

    // --- MÉTODO RELATÓRIO (A versão robusta que conta certo) ---

    public function getRelatorio(array $filters)
    {
        // 1. Busca os dados brutos do banco
        $dados = $this->livroRepository->getRelatorioData($filters);
        
        $total_geral = $dados->count();

        // 2. CONTAGEM ROBUSTA (Limpa espaços e ignora maiúsculas)
        
        // Conta Físicos
        $total_fisico = $dados->filter(function ($livro) {
            $tipo = mb_strtolower(trim($livro->tipo)); // Transforma em minúsculo e tira espaços
            return str_contains($tipo, 'fisic') || str_contains($tipo, 'físic');
        })->count();

        // Conta Digitais
        $total_digital = $dados->filter(function ($livro) {
            $tipo = mb_strtolower(trim($livro->tipo));
            return $tipo === 'digital' || $tipo === 'arquivo digital';
        })->count();
        
        return [
            'total_geral' => $total_geral,
            'total_fisico' => $total_fisico,
            'total_digital' => $total_digital,
            
            // 3. Detalhes (Aplicando a mesma lógica para garantir)
            'detalhes' => $dados->groupBy('categoria')->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'count' => $items->count(),
                    
                    'fisico' => $items->filter(function ($livro) {
                        $t = mb_strtolower(trim($livro->tipo));
                        return str_contains($t, 'fisic') || str_contains($t, 'físic');
                    })->count(),
                    
                    'digital' => $items->filter(function ($livro) {
                        $t = mb_strtolower(trim($livro->tipo));
                        return $t === 'digital' || $t === 'arquivo digital';
                    })->count(),
                ];
            })->values(),
        ];
    }
}