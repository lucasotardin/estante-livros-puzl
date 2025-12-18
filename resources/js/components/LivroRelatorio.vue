<template>
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Relatório de Livros</h5>
            <button @click="$emit('voltar')" class="btn btn-sm btn-light text-info">
                Voltar para Lista
            </button>
        </div>

        <div class="card-body">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-md-3">
                    <label>Data Início</label>
                    <input type="date" v-model="filtros.data_inicio" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Data Fim</label>
                    <input type="date" v-model="filtros.data_fim" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Categoria</label>
                    <select v-model="filtros.categoria" class="form-select">
                        <option value="">Todas</option>
                        <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button @click="gerarRelatorio" class="btn btn-primary w-100">
                        Gerar Dados
                    </button>
                </div>
            </div>

            <div v-if="dadosRelatorio" class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="card bg-light border-primary">
                        <div class="card-body">
                            <h3>{{ dadosRelatorio.total_geral }}</h3>
                            <p class="text-muted mb-0">Total de Livros</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light border-success">
                        <div class="card-body">
                            <h3>{{ dadosRelatorio.total_fisico }}</h3>
                            <p class="text-muted mb-0">Físicos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light border-info">
                        <div class="card-body">
                            <h3>{{ dadosRelatorio.total_digital }}</h3>
                            <p class="text-muted mb-0">Digitais</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dadosRelatorio && dadosRelatorio.detalhes.length > 0" class="mt-4">
                <h6>Detalhes por Categoria:</h6>
                <ul class="list-group">
                    <li v-for="item in dadosRelatorio.detalhes" :key="item.categoria" class="list-group-item d-flex justify-content-between align-items-center">
                        {{ item.categoria }}
                        <span class="badge bg-primary rounded-pill">{{ item.count }} livros</span>
                    </li>
                </ul>
            </div>
            
            <div v-else-if="carregando" class="text-center mt-5">
                <div class="spinner-border text-info" role="status"></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
    data() {
        return {
            filtros: {
                data_inicio: '',
                data_fim: '',
                categoria: ''
            },
            dadosRelatorio: null,
            carregando: false
        };
    },
    computed: {
        ...mapGetters({
            categorias: 'allCategorias'
        })
    },
    methods: {
        async gerarRelatorio() {
            this.carregando = true;
            this.dadosRelatorio = null;
            
            try {
                // Chama a rota que criamos no backend: /relatorio/livros
                const response = await axios.get('/relatorio/livros', {
                    params: this.filtros 
                });
                this.dadosRelatorio = response.data;
            } catch (error) {
                console.error("Erro ao gerar relatório", error);
                alert("Erro ao buscar dados do relatório.");
            } finally {
                this.carregando = false;
            }
        }
    },
    mounted() {
        // Gera o relatório inicial ao abrir
        this.gerarRelatorio();
    }
};
</script>