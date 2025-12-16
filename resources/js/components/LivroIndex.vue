<template>
    <div class="card shadow-sm mt-4">
        
        <div
            class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
        >
            <h5 class="mb-0">
                Painel de Gerenciamento de Livros (Total: {{ totalLivros }})
            </h5>
            <button @click="showForm = true" class="btn btn-sm btn-success">
                Adicionar Novo Livro
            </button>
        </div>

        <div class="card-body border-bottom">
            <div class="row align-items-end">
                
                <div class="col-md-4 mb-2">
                    <label for="search-nome" class="form-label">Buscar por Nome/Autor</label>
                    <input 
                        type="text" 
                        class="form-control form-control-sm" 
                        id="search-nome"
                        v-model="filters.nome" 
                        placeholder="Nome, Autor ou Palavra-chave..."
                        @keyup.enter="applyFilters" 
                    />
                </div>

                <div class="col-md-3 mb-2">
                    <label for="search-categoria" class="form-label">Filtrar por Categoria</label>
                    <select v-model="filters.categoria" class="form-select form-select-sm" id="search-categoria">
                        <option value="">Todas as Categorias</option>
                        <option v-for="cat in allCategorias" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label for="search-tipo" class="form-label">Filtrar por Tipo</label>
                    <select v-model="filters.tipo" class="form-select form-select-sm" id="search-tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="Digital">Digital</option>
                        <option value="Físico">Físico</option>
                    </select>
                </div>

                <div class="col-md-2 mb-2 d-flex justify-content-end">
                    <button @click="applyFilters" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-filter"></i> Aplicar
                    </button>
                    <button @click="resetFilters" class="btn btn-sm btn-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p v-if="isLoading" class="text-info">
                Carregando livros... (Aguardando API)
            </p>

            <p v-else-if="livros.length === 0" class="text-danger">
                Nenhum livro encontrado. Tente adicionar um via
                Postman/livros.http!
            </p>

            <table v-else class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Autor</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(livro, index) in livros"
                        :key="livro && livro.id ? livro.id : 'livro-' + index"
                    >
                        <td>
                            {{ livro && livro.id ? livro.id : '—' }}
                        </td>
                        <td>
                            {{ livro && livro.nome ? livro.nome : 'Sem nome' }}
                        </td>
                        <td>
                            {{ livro && livro.autor ? livro.autor : 'Desconhecido' }}
                        </td>
                        <td>
                            {{ livro && livro.categoria ? livro.categoria : '-' }}
                        </td>
                        <td>
                            {{ livro && livro.tipo ? livro.tipo : '-' }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2">
                                Editar
                            </button>
                            <button @click="handleDelete(livro.id)" class="btn btn-sm btn-danger">
                                Apagar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="showForm" class="modal-overlay">
            <LivroForm @close="showForm = false" />
        </div>
        
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import LivroForm from './LivroForm.vue'; 

export default {
    components: {
        LivroForm, 
    },
    data() {
        return {
            showForm: false, 
            // NOVO ESTADO: Objeto para armazenar todos os filtros
            filters: {
                nome: '',
                categoria: '',
                tipo: '',
            }
        };
    },
    computed: {
        ...mapGetters({
            livrosFromStore: 'allLivros',
            paginationData: 'paginationData',
            isLoading: 'isLoading',
            allCategorias: 'allCategorias', // Mapear as categorias para o SELECT
        }),
        livros() {
            return this.livrosFromStore || [];
        },
        totalLivros() {
            if (
                this.paginationData &&
                typeof this.paginationData.total === 'number'
            ) {
                return this.paginationData.total;
            }
            return this.livros.length;
        },
    },

    methods: {
        ...mapActions(['fetchLivros', 'deleteLivro']), 

        // Lógica de exclusão
        handleDelete(livroId) {
            if (!confirm('Tem certeza que deseja apagar este livro? Esta ação é irreversível.')) {
                return; 
            }

            this.deleteLivro(livroId)
                .then(() => {
                    alert('Livro apagado com sucesso!');
                })
                .catch(error => {
                    alert('Erro ao apagar o livro. Verifique a API.');
                    console.error("Erro na exclusão:", error);
                });
        },
        
        // NOVO MÉTODO: Envia os filtros para a Action do Vuex
        applyFilters() {
            // Chama a Action, passando o objeto filters
            this.fetchLivros({ filters: this.filters }); 
        },
        
        // NOVO MÉTODO: Limpa os filtros e recarrega a lista
        resetFilters() {
            this.filters = {
                nome: '',
                categoria: '',
                tipo: '',
            };
            // Recarrega a lista sem filtros (passa o objeto vazio)
            this.fetchLivros({}); 
        },
    },

    mounted() {
        // Agora, mounted deve chamar fetchLivros com objeto vazio para garantir que o payload seja passado corretamente.
        this.fetchLivros({}); 
    },
};
</script>

<style scoped>
/* Estilos para o Modal Overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); 
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
</style>