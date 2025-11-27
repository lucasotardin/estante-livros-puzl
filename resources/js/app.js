console.log('App.js carregado');
// resources/js/app.js
require('./bootstrap');

import Vue from 'vue';
import Store from './store/index'; // Importa a lógica do Vuex (Seu código)

// --- Importa e Registra o Componente ---
import LivroIndex from './components/LivroIndex.vue'; 
Vue.component('livro-index', LivroIndex); // Registrando globalmente

// --- Inicialização ---
const app = new Vue({
    el: '#app',       // Liga o Vue ao elemento HTML <div id="app">
    store: Store,     // Conecta o Store
});