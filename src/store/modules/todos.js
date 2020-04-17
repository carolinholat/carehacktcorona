import axios from 'axios'

const state = {
    todos: [
        {
            id: 1,
            name: 'cooking',
            done: false
        },
        {
            id: 2,
            name: 'cleaning',
            done: false
        }
    ]
};

const getters = {
    allTodos: (state) => state.todos,
};

const actions = {
    async fetchTodos({commit}) {
        const response= await axios.get('https://jsonplaceholder.typicode.com/todos');
    }
};

const mutations = {};

export default {
    state,
    getters,
    actions,
    mutations
}