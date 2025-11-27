<?php
// app/Http/Requests/LivroStoreRequest.php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class LivroStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nome'      => 'required|string|max:255',
            'autor'     => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'codigo'    => 'required|string|unique:livros|max:50', // Único
            'tipo'      => 'required|in:digital,fisico', 
            'tamanho'   => 'required|string|max:100',
        ];
    }
}