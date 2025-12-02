// resources/js/store/index.js
import Vue from 'vue/dist/vue.esm.js';
import Vuex from 'vuex';
import axios from 'axios'; // Usaremos Axios para requisições AJAX

Vue.use(Vuex);

// Usa caminho relativo para bater no mesmo host/porta do Laravel (sem prefixo /api)
const API_URL = '/livros';

export default new Vuex.Store({
    // 1. STATE: Os dados reais da aplicação
    state: {
        livros: [],
        categorias: ['Fantasia Medieval', 'Ficção Social', 'Programação', 'Distopia'],
        // Adicionando o estado da paginação
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        },
        loading: false, // Indicador de carregamento
    },

    // 2. GETTERS: Como acessar os dados do estado
    getters: {
        allLivros: state => state.livros || [],
        allCategorias: state => state.categorias,
        paginationData: state => state.pagination || {total: 0, current_page: 1, last_page: 1},
        isLoading: state => state.loading,
    },

    // 3. MUTATIONS: A ÚNICA forma de MUDAR o STATE (de forma síncrona)
    mutations: {
        SET_LIVROS(state, data) {
            state.livros = data.data; // A lista de livros
            // Atualiza os dados de paginação
            state.pagination = {
                current_page: data.current_page,
                last_page: data.last_page,
                total: data.total
            };
        },
        SET_LOADING(state, status) {
            state.loading = status;
        },
        // Adicione outras mutations para criar, editar e deletar
    },

    // 4. ACTIONS: Funções assíncronas para CHAMAR a API e depois chamar as Mutations
    actions: {
    async fetchLivros({ commit }, page = 1) {
        commit('SET_LOADING', true);

        try {
            const response = await axios.get(`${API_URL}?page=${page}`);

            commit('SET_LIVROS', response.data);
            return response.data; // opcional, mas útil se quiser usar .then() no componente
        } catch (error) {
            console.error('Erro ao buscar livros:', error);
            throw error; // opcional, mas permite tratar erro no componente
        } finally {
            commit('SET_LOADING', false);
        }
    },
}
});