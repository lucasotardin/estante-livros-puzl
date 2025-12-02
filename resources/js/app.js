console.log('App.js carregado');
// resources/js/app.js
import './bootstrap';
import Vue from 'vue/dist/vue.esm.js';
import store from './store';
import LivroIndex from './components/LivroIndex.vue';

// Registra globalmente o componente raiz
Vue.component('livro-index', LivroIndex);

// Monta a instância com Vuex disponível
new Vue({
    el: '#app',
    store,
});