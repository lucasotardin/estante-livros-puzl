// resources/js/store/index.js
import Vue from 'vue/dist/vue.esm.js';
import Vuex from 'vuex';
import axios from 'axios'; 

Vue.use(Vuex);

// URL Base da API: Altere se necessário!
const API_URL = 'http://localhost/livros';

export default new Vuex.Store({
    
    // 1. STATE: Os dados reais da aplicação
    state: {
        livros: [], // Array de livros
        categorias: ['Fantasia Medieval', 'Ficção Social', 'Programação', 'Distopia', 'Romance'], // Lista de categorias (para o SELECT)
        // Usamos paginationData para consistência
        paginationData: {
            current_page: 1,
            last_page: 1,
            total: 0
        },
        isLoading: false, // Indicador de carregamento
    },

    // 2. GETTERS: Como acessar os dados do estado
    getters: {
        allLivros: state => state.livros || [],
        allCategorias: state => state.categorias,
        paginationData: state => state.paginationData,
        isLoading: state => state.isLoading,
    },

    // 3. MUTATIONS: A ÚNICA forma de MUDAR o STATE (de forma síncrona)
    mutations: {
        // Salva apenas o ARRAY de livros
        SET_LIVROS(state, livrosArray) { 
            state.livros = livrosArray; 
        },
        // Salva os dados de paginação (total, current_page, etc.)
        SET_PAGINATION(state, paginationPayload) {
            state.paginationData = paginationPayload; 
        },
        SET_LOADING(state, status) {
            state.isLoading = status;
        },
        // Mutações futuras de CREATE, UPDATE, DELETE virão aqui
    },

    // 4. ACTIONS: Funções assíncronas para CHAMAR a API
    actions: {
        
        // Ação para BUSCAR a lista de livros (READ) - CORRIGIDA PARA FILTROS
        async fetchLivros({ commit }, payload = { page: 1, filters: {} }) {
            commit('SET_LOADING', true);
            
            // 1. Desestrutura o payload (contém a página e os filtros)
            const page = payload.page || 1;
            const filters = payload.filters || {};
            
            // 2. Constrói o Query String
            let queryString = `?page=${page}`;
            
            for (const key in filters) {
                // Adiciona o parâmetro se o valor do filtro não for vazio
                if (filters[key]) {
                    queryString += `&${key}=${filters[key]}`;
                }
            }

            try {
                // 3. Usa o queryString completo para a requisição GET
                const response = await axios.get(`${API_URL}${queryString}`);
                
                // Salva a lista de livros
                commit('SET_LIVROS', response.data.data); 
                
                // Salva os dados de paginação
                commit('SET_PAGINATION', response.data.meta || response.data); 

                return response.data; 
            } catch (error) {
                console.error('Erro ao buscar livros:', error);
                commit('SET_LIVROS', []); 
                throw error;
            } finally {
                commit('SET_LOADING', false);
            }
        },
        
        // Ação para CRIAR um novo livro (CREATE)
        async createLivro({ dispatch }, livroData) {
            try {
                const response = await axios.post(API_URL, livroData);
                await dispatch('fetchLivros', {}); // Recarrega a lista sem filtros específicos
                return response.data;
            } catch (error) {
                console.error('Erro ao criar livro:', error.response.data);
                throw error; 
            }
        },
        
        // Ação para DELETAR um livro (DELETE)
        async deleteLivro({ dispatch }, id) {
            try {
                const response = await axios.delete(`${API_URL}/${id}`); 
                await dispatch('fetchLivros', {}); // Recarrega a lista sem filtros específicos
                return response.data;
            } catch (error) {
                console.error('Erro ao deletar livro:', error);
                throw error; 
            }
        },

        // FUTURAS ACTIONS DE UPDATE (EDIÇÃO) VIRÃO AQUI
    }
});