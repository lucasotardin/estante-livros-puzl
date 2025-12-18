<template>
  <div class="app-background">
    
    <div class="container content-wrapper">
      
      <div class="card shadow-lg border-0 card-library">
        
        <div class="card-header header-library text-white d-flex flex-wrap justify-content-between align-items-center py-3">
          <h5 class="mb-2 mb-md-0 fw-bold d-flex align-items-center font-serif">
            <i class="fas fa-book-open me-2 text-gold"></i> Gerenciador de Livros 
            <span class="badge bg-gold text-dark ms-2 rounded-pill shadow-sm">{{ totalLivros }}</span>
          </h5>
          
          <div class="d-flex gap-2">
            <button 
              @click="mostrarRelatorio = !mostrarRelatorio" 
              class="btn btn-sm btn-outline-light fw-bold"
            >
              {{ mostrarRelatorio ? 'Voltar para Lista' : 'Ver Relatório' }}
            </button>

            <button 
              v-if="!mostrarRelatorio" 
              @click="abrirParaCadastro" 
              class="btn btn-sm btn-library-create fw-bold shadow-sm"
            >
              + Novo Livro
            </button>
          </div>
        </div>

        <div v-if="mostrarRelatorio" class="p-4 bg-paper">
            <LivroRelatorio @voltar="mostrarRelatorio = false" />
        </div>

        <template v-else>
          <div class="card-body border-bottom bg-paper">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-coffee">Buscar</label>
                <input type="text" class="form-control input-library" v-model="filters.nome" @keyup.enter="applyFilters" placeholder="Nome ou Autor...">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-coffee">Categoria</label>
                <select v-model="filters.categoria" class="form-select input-library">
                  <option value="">Todas</option>
                  <option v-for="cat in allCategorias" :key="cat" :value="cat">{{ cat }}</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-coffee">Tipo</label>
                <select v-model="filters.tipo" class="form-select input-library">
                  <option value="">Todos</option>
                  <option value="Digital">Digital</option>
                  <option value="Físico">Físico</option>
                </select>
              </div>
              
              <div class="col-md-2 d-flex align-items-end gap-2">
                <button @click="applyFilters" class="btn btn-library-filter flex-grow-1 fw-bold">Filtrar</button>
                <button @click="resetFilters" class="btn btn-outline-coffee">Limpar</button>
              </div>
            </div>
          </div>

          <div class="card-body p-0">
            <div v-if="isLoading" class="text-center p-5">
              <div class="spinner-border text-coffee" role="status"></div>
              <p class="text-muted mt-3 mb-0">Consultando acervo...</p>
            </div>

            <div v-else-if="livros.length === 0" class="text-center p-5 text-muted">
              <h4>Nenhum livro encontrado.</h4>
              <p>O acervo está vazio para esta busca.</p>
            </div>

            <div v-else class="table-responsive">
                <table class="table table-hover table-library align-middle mb-0">
                <thead class="thead-library">
                    <tr>
                    <th class="ps-4">ID</th>
                    <th>Obra</th>
                    <th>Autor</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="livro in livros" :key="livro.id">
                    <td class="ps-4 text-muted fw-bold">#{{ livro.id }}</td>
                    <td>
                        <span class="fw-bold text-dark">{{ livro.nome }}</span>
                        <div class="small text-muted" style="font-size: 0.8rem;">Cód: {{ livro.codigo }}</div>
                    </td>
                    <td class="text-secondary font-italic">{{ livro.autor }}</td>
                    <td>
                        <span class="badge badge-category">{{ livro.categoria }}</span>
                    </td>
                    <td>
                        <span v-if="livro.tipo === 'Físico'" class="badge badge-physical">
                            <i class="fas fa-box me-1"></i> Físico
                        </span>
                        <span v-else class="badge badge-digital">
                            <i class="fas fa-tablet-alt me-1"></i> Digital
                        </span>
                    </td>
                    
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button @click="handleEdit(livro)" class="btn btn-sm btn-outline-edit fw-bold">
                                Editar
                            </button>
                            <button @click="handleDelete(livro.id)" class="btn btn-sm btn-outline-delete fw-bold">
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
    
    </div> 
  </div>
</template>

<script>
// O SCRIPT PERMANECE EXATAMENTE O MESMO
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

<style>
/* Reset Global - Fundo escuro para evitar clarões ao carregar */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
    overflow-x: hidden;
    background-color: #1a1a1a; 
}
</style>

<style scoped>
/* --- FUNDO (RESTAURADO AO ORIGINAL) --- */
.app-background {
  /* Fundo preto transparente original */
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2000&auto=format&fit=crop');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  background-repeat: no-repeat;
  min-height: 100vh;
  width: 100vw;
  position: absolute;
  top: 0; left: 0;
}

.content-wrapper {
    position: relative;
    z-index: 10;
    padding-top: 5vh;
    padding-bottom: 5vh;
    max-width: 1200px;
}

/* --- CORES DO TEMA BIBLIOTECA --- */
/* Variáveis de cor simuladas */
.text-gold { color: #ffc107; }
.bg-gold { background-color: #ffc107; }
.text-coffee { color: #4e342e; } /* Marrom café escuro */

/* Card Principal */
.card-library {
    border-radius: 8px;
    background-color: #fff;
    border: none; /* Remove borda padrão */
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important; /* Sombra mais dramática */
}

/* Header (Marrom Café Escuro) */
.header-library {
    background-color: #2c1e1a; /* Cor de café bem escuro */
    color: #f8f9fa;
    border-bottom: 3px solid #ffc107; /* Linha dourada */
}

/* Área de Filtros (Tom de Creme/Papel) */
.bg-paper {
    background-color: #fff8e1; /* Creme suave */
}

/* Inputs */
.input-library {
    border: 1px solid #d7ccc8;
    background-color: #fff;
}
.input-library:focus {
    border-color: #4e342e;
    box-shadow: 0 0 0 0.2rem rgba(78, 52, 46, 0.25);
}

/* --- TABELA CUSTOMIZADA --- */
.table-library {
    border-color: #efebe9; /* Bordas internas suaves */
}

/* Cabeçalho da Tabela (Creme mais escuro) */
.thead-library th {
    background-color: #ece5dd; 
    color: #4e342e;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #d7ccc8;
}

/* Hover na linha */
.table-library tbody tr:hover {
    background-color: #fffde7; /* Brilho amarelo muito suave ao passar o mouse */
}


/* --- BOTÕES E BADGES --- */

/* Botão Novo (Verde Floresta) */
.btn-library-create {
    background-color: #2e7d32; 
    color: white; border: none;
}
.btn-library-create:hover { background-color: #1b5e20; color: white; }

/* Botão Filtrar (Marrom) */
.btn-library-filter {
    background-color: #4e342e;
    color: white; border: none;
}
.btn-library-filter:hover { background-color: #3e2723; color: white; }

/* Botão Limpar (Outline Marrom) */
.btn-outline-coffee {
    color: #4e342e; border-color: #4e342e;
}
.btn-outline-coffee:hover { background-color: #4e342e; color: white; }

/* Ações: Editar (Outline Dourado/Âmbar) */
.btn-outline-edit {
    color: #d89200; border-color: #d89200;
}
.btn-outline-edit:hover { background-color: #d89200; color: #2c1e1a; }

/* Ações: Apagar (Outline Vermelho Tijolo) */
.btn-outline-delete {
    color: #c62828; border-color: #c62828;
}
.btn-outline-delete:hover { background-color: #c62828; color: white; }

/* Badges */
.badge-category {
    background-color: #efebe9; color: #4e342e; border: 1px solid #d7ccc8;
}
.badge-physical {
    background-color: #388e3c; color: white; /* Verde clássico */
}
.badge-digital {
    background-color: #455a64; color: white; /* Azul ardósia */
}


/* Misc */
.card-body.p-0 .table-responsive {
    border-radius: 0 0 calc(0.375rem - 1px) calc(0.375rem - 1px);
}
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(3px);
  display: flex; justify-content: center; align-items: center; z-index: 2000;
}
.btn { transition: all 0.15s ease; }
.btn:active { transform: scale(0.98); }
</style>