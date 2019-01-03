// state
const state = {
  modal: {
    title: '',
    component: {},
    formData: {},
    action: '',
    show: false,
  }
}

// getters
const getters = {
  structure: (state) => {
    return state.modal;
  }
}

// mutations
const mutations = {
  update (state, modal) {
    state.modal = modal;
  }
}

// actions
const actions = {
  update ({commit}, modal) {
    modal.show = true;
    commit('update', modal);
    window.$('#modal').modal('show');
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
