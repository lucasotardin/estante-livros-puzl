<?php 
// ... (início do arquivo)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id(); // ID
            $table->string('nome'); // Nome
            $table->string('autor'); // Autor
            $table->string('categoria'); // Categoria
            
            // Código (Precisa ser único para garantir que não haja duplicidade)
            $table->string('codigo')->unique(); 
            
            // Tipo (Usamos enum para limitar as opções a 'digital' ou 'fisico')
            $table->enum('tipo', ['digital', 'fisico']); 
            
            // Tamanho (Peso ou tamanho do arquivo)
            $table->string('tamanho'); 
            
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};