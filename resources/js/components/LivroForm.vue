<template>
    <div class="card p-4 shadow">
        <h5>Novo Livro</h5>
        
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
                    <label>Categoria (SELECT)</label>
                    <select v-model="form.categoria" class="form-control" required>
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
                    <select v-model="form.tipo" class="form-control" required>
                        <option value="" disabled>Selecione o Tipo</option>
                        <option value="digital">Arquivo Digital</option>
                        <option value="fisico">Físico</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>{{ tamanhoLabel }}</label> 
                    <input v-model="form.tamanho" class="form-control" required>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" @click="$emit('close')" class="btn btn-secondary me-2">Cancelar</button>
                <button type="submit" :disabled="isSubmitting" class="btn btn-primary">
                    {{ isSubmitting ? 'Salvando...' : 'Salvar Livro' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    // Props vazias por enquanto, pois não estamos no modo edição
    data() {
        return {
            form: {
                id: null,
                nome: '',
                autor: '',
                categoria: '',
                codigo: '',
                tipo: 'digital',
                tamanho: '',
            },
            tamanhoLabel: 'Tamanho do Arquivo (MB/GB)',
            isSubmitting: false,
        };
    },
    computed: {
        ...mapGetters({
            categorias: 'allCategorias' // Puxa a lista do Vuex Store
        })
    },
    watch: {
        // Exigência do Teste: Mudar o rótulo de Tamanho/Peso
        'form.tipo': {
            immediate: true, 
            handler(newVal) {
                if (newVal === 'fisico') {
                    this.tamanhoLabel = 'Peso (g/kg)';
                } else {
                    this.tamanhoLabel = 'Tamanho do Arquivo (MB/GB)';
                }
            }
        }
    },
    methods: {
        ...mapActions(['createLivro']), 
        
        handleSubmit() {
            this.isSubmitting = true;
            const data = { ...this.form };
            
            // Chama a Action do Vuex
            this.createLivro(data)
                .then(() => {
                    // Limpa o formulário e fecha o modal
                    this.form = { nome: '', autor: '', categoria: '', codigo: '', tipo: 'digital', tamanho: '' };
                    this.$emit('close'); 
                    alert("Livro salvo com sucesso!");
                })
                .catch(error => {
                    alert("Erro ao salvar livro. Verifique a API no console (F12).");
                    console.error("Erro na API:", error);
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        }
    },
};
</script>