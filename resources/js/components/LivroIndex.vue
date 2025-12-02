<template>
    <div class="card shadow-sm mt-4">
        <div
            class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
        >
            <h5 class="mb-0">
                Painel de Gerenciamento de Livros (Total: {{ totalLivros }})
            </h5>
            <button class="btn btn-sm btn-success">Adicionar Novo Livro</button>
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
                            <button class="btn btn-sm btn-danger">
                                Apagar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    computed: {
        // mapeia exatamente os getters que você definiu na store
        ...mapGetters({
            livrosFromStore: 'allLivros',
            paginationData: 'paginationData',
            isLoading: 'isLoading',
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
        ...mapActions(['fetchLivros']),
    },

    mounted() {
        console.log('Antes de buscar, livros:', this.livros);
        console.log('Livros', this.livrosFromStore);
        // se você ajustar a action pra dar "return", isso aqui funciona:
        this.fetchLivros().then(() => {
            console.log('Depois de buscar, livros:', this.livros);
        });

        // se não quiser mexer na action, pode deixar só:
        // this.fetchLivros();
    },
};
</script>

<style scoped>
/* Estilos aqui */
</style>