<template>
  <div class="card shadow-sm mt-4">
    <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center py-3">
      <h5 class="mb-2 mb-md-0">
          Painel de Gerenciamento (Total: {{ totalLivros }})
      </h5>
      
      <div class="d-flex gap-2">
        <button 
          @click="mostrarRelatorio = !mostrarRelatorio" 
          class="btn btn-sm btn-info text-white"
        >
          {{ mostrarRelatorio ? 'Voltar para Lista' : 'Ver Relatório' }}
        </button>

        <button 
          v-if="!mostrarRelatorio" 
          @click="abrirParaCadastro" 
          class="btn btn-sm btn-success"
        >
          Adicionar Novo Livro
        </button>
      </div>
    </div>

    <div v-if="mostrarRelatorio" class="p-3">
        <LivroRelatorio @voltar="mostrarRelatorio = false" />
    </div>

    <template v-else>
      <div class="card-body border-bottom">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Buscar por Nome/Autor</label>
            <input type="text" class="form-control" v-model="filters.nome" @keyup.enter="applyFilters" placeholder="Digite e aperte Enter...">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Categoria</label>
            <select v-model="filters.categoria" class="form-select">
              <option value="">Todas</option>
              <option v-for="cat in allCategorias" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Tipo</label>
            <select v-model="filters.tipo" class="form-select">
              <option value="">Todos</option>
              <option value="Digital">Digital</option>
              <option value="Físico">Físico</option>
            </select>
          </div>
          
          <div class="col-md-2 d-grid gap-2 d-md-flex align-items-end mb-3">
            <button @click="applyFilters" class="btn btn-primary me-md-2">Filtrar</button>
            <button @click="resetFilters" class="btn btn-secondary">Limpar</button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div v-if="isLoading" class="text-center p-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="text-info mt-3 mb-0">Carregando livros...</p>
        </div>

        <div v-else-if="livros.length === 0" class="text-center p-5">
          <p class="text-danger mb-0">Nenhum livro encontrado.</p>
        </div>

        <div v-else class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Autor</th>
                <th>Categoria</th>
                <th>Tipo</th>
                <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="livro in livros" :key="livro.id">
                <td>#{{ livro.id }}</td>
                <td class="fw-bold">{{ livro.nome }}</td>
                <td>{{ livro.autor }}</td>
                <td><span class="badge bg-secondary">{{ livro.categoria }}</span></td>
                <td>{{ livro.tipo }}</td>
                
                <td>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <button @click="handleEdit(livro)" class="btn btn-sm btn-warning">
                            Editar
                        </button>
                        <button @click="handleDelete(livro.id)" class="btn btn-sm btn-danger">
                            Apagar
                        </button>
                    </div>
                </td>
                </tr>
            </tbody>
            </table>
        </div>
      </div>
    </template>

    <div v-if="showForm" class="modal-overlay">
      <LivroForm @close="fecharForm" />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import LivroForm from './LivroForm.vue';
import LivroRelatorio from './LivroRelatorio.vue';

export default {
  components: { LivroForm, LivroRelatorio },
  data() {
    return {
      mostrarRelatorio: false,
      showForm: false,
      filters: { nome: '', categoria: '', tipo: '' }
    };
  },
  computed: {
    ...mapGetters({
      livrosFromStore: 'allLivros',
      paginationData: 'paginationData',
      isLoading: 'isLoading',
      allCategorias: 'allCategorias',
    }),
    livros() { return this.livrosFromStore || []; },
    totalLivros() { return this.paginationData?.total || this.livros.length; }
  },
  methods: {
    ...mapActions(['fetchLivros', 'deleteLivro', 'setLivroParaEdicao', 'limparLivroEdicao']),
    abrirParaCadastro() { this.limparLivroEdicao(); this.showForm = true; },
    handleEdit(livro) { this.setLivroParaEdicao(livro); this.showForm = true; },
    fecharForm() { this.showForm = false; this.limparLivroEdicao(); },
    handleDelete(livroId) {
      if (confirm('Deseja realmente excluir este livro?')) {
        this.deleteLivro(livroId).then(() => alert('Excluído!'));
      }
    },
    applyFilters() { this.fetchLivros({ filters: this.filters }); },
    resetFilters() { this.filters = { nome: '', categoria: '', tipo: '' }; this.fetchLivros({}); }
  },
  mounted() { this.fetchLivros({}); }
};
</script>

<style scoped>
.card-body.p-0 .table-responsive {
    border-radius: 0 0 calc(0.375rem - 1px) calc(0.375rem - 1px);
}
.modal-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 1050;
}
</style>