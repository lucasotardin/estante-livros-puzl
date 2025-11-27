<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
//essa classe vai servir para a regra "unique"
use Illuminate\Validation\Rule;

class LivroUpdateRequest extends FormRequest
{
    /**
     * Determinar se o usuario está autorizado a fazer esta requisição.
     * Por enquanto vai ficar True
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Definir as regras de validação que se aplicam á requisição UPDATE
    *
    * @return array<string, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
    */
    public function rules(): array
    {
        // 1. Precisamos pegar o ID do livro que está sendo atualizado.
        // O Laravel pega isso da própria rota, ex: /api/livros/5
        $livroId = $this->route('livro');

        return [
            //Regras normais
            'nome'      => 'required|string|max:255',
            'autor'     => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'tipo'      => 'required|in:digital,fisico', 
            'tamanho'   => 'required|string|max:100',
      // 2. A Regra de Ouro do Update:
            // O 'codigo' deve ser único, MAS deve ignorar o ID do livro
            // que já estamos editando (senão ele falha em si mesmo).
            'codigo'    => [
                'required',
                'string',
                'max:50',
                // Use a regra 'unique' na tabela 'livros', coluna 'codigo',
                // e IGNORE o $livroId atual.
                Rule::unique('livros', 'codigo')->ignore($livroId),
            ],
        ];
    }
}