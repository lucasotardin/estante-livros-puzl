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
        };
    },
    computed: {
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
        // Mapear as Actions existentes e a nova Action 'deleteLivro'
        ...mapActions(['fetchLivros', 'deleteLivro']), 

        // Lógica de exclusão
        handleDelete(livroId) {
            if (!confirm('Tem certeza que deseja apagar este livro? Esta ação é irreversível.')) {
                return; 
            }

            // Chama a Action do Vuex para deletar
            this.deleteLivro(livroId)
                .then(() => {
                    alert('Livro apagado com sucesso!');
                })
                .catch(error => {
                    alert('Erro ao apagar o livro. Verifique a API.');
                    console.error("Erro na exclusão:", error);
                });
        }
    },

    mounted() {
        this.fetchLivros();
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
    align-items