<?php
// app/Models/Livro.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'autor',
        'categoria',
        'codigo', // Único
        'tipo', // 'digital' ou 'fisico'
        'tamanho', // Peso ou tamanho do arquivo
    ];
}