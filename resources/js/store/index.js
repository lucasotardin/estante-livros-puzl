// resources/js/store/index.js
import Vue from 'vue/dist/vue.esm.js';
import Vuex from 'vuex';
import axios from 'axios'; 

Vue.use(Vuex);

// URL Base da API: Altere se necessário!
const API_URL = '/livros'; // Note o /api/ comum no Laravel

export default new Vuex.Store({
    
    // 1. STATE: Os dados reais da aplicação
    state: {
        livros: [], // Array de livros
        categorias: ['Fantasia Medieval', 'Ficção Social', 'Programação', 'Distopia', 'Romance', 'Programacao'], 
        paginationData: {
            current_page: 1,
            last_page: 1,
            total: 0
        },
        isLoading: false, 
        
        // NOVO: Armazena o livro que está sendo editado no momento
        livroParaEdicao: null, 
    },

    // 2. GETTERS: Como acessar os dados do estado
    getters: {
        allLivros: state => state.livros || [],
        allCategorias: state => state.categorias,
        paginationData: state => state.paginationData,
        isLoading: state => state.isLoading,
        // Getter para o formulário saber se há alguém para editar
        livroEmEdicao: state => state.livroParaEdicao,
    },

    // 3. MUTATIONS: A ÚNICA forma de MUDAR o STATE
    mutations: {
        SET_LIVROS(state, livrosArray) { 
            state.livros = livrosArray; 
        },
        SET_PAGINATION(state, paginationPayload) {
            state.paginationData = paginationPayload; 
        },
        SET_LOADING(state, status) {
            state.isLoading = status;
        },
        
        // NOVAS MUTAÇÕES PARA EDIÇÃO
        SET_LIVRO_PARA_EDICAO(state, livro) {
            state.livroParaEdicao = livro;
        },
        CLEAR_LIVRO_EDICAO(state) {
            state.livroParaEdicao = null;
        }
    },

    // 4. ACTIONS: Funções assíncronas para CHAMAR a API
    actions: {
        
        // READ: Buscar lista de livros com filtros
        async fetchLivros({ commit }, payload = { page: 1, filters: {} }) {
            commit('SET_LOADING', true);
            
            const page = payload.page || 1;
            const filters = payload.filters || {};
            let queryString = `?page=${page}`;
            
            for (const key in filters) {
                if (filters[key]) {
                    queryString += `&${key}=${filters[key]}`;
                }
            }

            try {
                const response = await axios.get(`${API_URL}${queryString}`);
                commit('SET_LIVROS', response.data.data); 
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
        
        // CREATE: Criar um novo livro
        async createLivro({ dispatch }, livroData) {
            try {
                const response = await axios.post(API_URL, livroData);
                await dispatch('fetchLivros', {}); 
                return response.data;
            } catch (error) {
                console.error('Erro ao criar livro:', error.response.data);
                throw error; 
            }
        },

        // UPDATE: Atualizar livro existente (O que faltava!)
        async updateLivro({ dispatch, commit }, livroData) {
            try {
                // livroData deve conter o ID para a URL
                const response = await axios.put(`${API_URL}/${livroData.id}`, livroData);
                
                // Após sucesso, limpa o estado de edição e atualiza a lista
                commit('CLEAR_LIVRO_EDICAO');
                await dispatch('fetchLivros', {}); 
                return response.data;
            } catch (error) {
                console.error('Erro ao atualizar livro:', error.response?.data || error);
                throw error;
            }
        },
        
        // DELETE: Apagar um livro
        async deleteLivro({ dispatch }, id) {
            try {
                const response = await axios.delete(`${API_URL}/${id}`); 
                await dispatch('fetchLivros', {}); 
                return response.data;
            } catch (error) {
                console.error('Erro ao deletar livro:', error);
                throw error; 
            }
        },

        // AÇÕES DE INTERFACE (UI)
        setLivroParaEdicao({ commit }, livro) {
            commit('SET_LIVRO_PARA_EDICAO', livro);
        },
        
        limparLivroEdicao({ commit }) {
            commit('CLEAR_LIVRO_EDICAO');
        }
    }
});