<template>
    <div class="card p-4 shadow">
        <h5>{{ modoEdicao ? 'Editar Livro' : 'Novo Livro' }}</h5>
        
        <form @submit.prevent="handleSubmit">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input v-model="form.nome" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Autor</label>
                    <input v-model="form.autor" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Categoria</label>
                    <select v-model="form.categoria" class="form-select" required>
                        <option value="" disabled>Selecione uma Categoria</option>
                        <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Código (Único)</label>
                    <input v-model="form.codigo" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tipo</label>
                    <select v-model="form.tipo" class="form-select" required>
                        <option value="" disabled>Selecione o Tipo</option>
                        
                        <option value="Digital">Arquivo Digital</option>
                        <option value="Físico">Físico</option>
                    
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>{{ tamanhoLabel }}</label> 
                    <input v-model="form.tamanho" class="form-control" required>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" @click="cancelar" class="btn btn-secondary me-2">Cancelar</button>
                <button type="submit" :disabled="isSubmitting" :class="modoEdicao ? 'btn btn-warning' : 'btn btn-primary'">
                    {{ isSubmitting ? 'Salvando...' : (modoEdicao ? 'Salvar Alterações' : 'Cadastrar Livro') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    data() {
        return {
            form: {
                id: null,
                nome: '',
                autor: '',
                categoria: '',
                codigo: '',
                tipo: 'Digital', // Valor padrão já correto (Maiúsculo)
                tamanho: '',
            },
            tamanhoLabel: 'Tamanho do Arquivo (MB/GB)',
            isSubmitting: false,
            modoEdicao: false
        };
    },
    computed: {
        ...mapGetters({
            categorias: 'allCategorias',
            livroParaEditar: 'livroEmEdicao' 
        })
    },
    watch: {
        'form.tipo': {
            immediate: true, 
            handler(newVal) {
                // Aceita tanto maiúsculo quanto minúsculo para mudar o texto visualmente
                if (newVal && newVal.toLowerCase().includes('fisico')) {
                    this.tamanhoLabel = 'Peso (g/kg)';
                } else {
                    this.tamanhoLabel = 'Tamanho do Arquivo (MB/GB)';
                }
            }
        }
    },
    mounted() {
        if (this.livroParaEditar) {
            // Clona os dados para editar
            this.form = { ...this.livroParaEditar };
            this.modoEdicao = true;
        }
    },
    methods: {
        ...mapActions(['createLivro', 'updateLivro', 'limparLivroEdicao']), 
        
        async handleSubmit() {
            this.isSubmitting = true;
            
            // GARANTIA EXTRA: Força o valor correto antes de enviar
            // Isso previne que um dado antigo (minúsculo) quebre a edição
            let tipoCorrigido = this.form.tipo;
            if (tipoCorrigido.toLowerCase() === 'fisico' || tipoCorrigido === 'Físico') {
                tipoCorrigido = 'Físico';
            } else {
                tipoCorrigido = 'Digital';
            }

            const data = { 
                ...this.form,
                tipo: tipoCorrigido 
            };
            
            const action = this.modoEdicao ? 'updateLivro' : 'createLivro';
            
            try {
                await this.$store.dispatch(action, data);
                alert(this.modoEdicao ? "Atualizado com sucesso!" : "Criado com sucesso!");
                this.limparERechar();
            } catch (error) {
                // Mostra o erro exato que o Laravel devolveu
                const msg = error.response?.data?.message || "Erro de validação.";
                alert("Erro ao salvar: " + msg);
                console.error("Detalhes do erro:", error.response?.data);
            } finally {
                this.isSubmitting = false;
            }
        },

        cancelar() {
            this.limparERechar();
        },

        limparERechar() {
            this.limparLivroEdicao();
            this.form = { id: null, nome: '', autor: '', categoria: '', codigo: '', tipo: 'Digital', tamanho: '' };
            this.modoEdicao = false;
            this.$emit('close'); 
        }
    },
};
</script>