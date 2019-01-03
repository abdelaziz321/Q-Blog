const basicMessage = {
  title: 'hi there :)',
  class: '',
  body: '',
  confirm: false,
  itemsErrors: null,

  showMessage: false,
  waiting: false,
  timeout: null
}

// state
const state = {
  message: basicMessage
}

// getters
const getters = {
  structure: (state) => {
    return state.message;
  }
}

// mutations
const mutations = {
  UPDATE_MESSAGE(state, message) {
    state.message = message;
  },

  SHOW_MESSAGE(state) {
    state.message.showMessage = true;
  },

  STOP_WAITING(state) {
    state.message.waiting = false;
  },

  CLEAR_TIMEOUT(state) {
    clearTimeout(state.message.timeout);
  }
}

// actions
const actions = {

  update ({commit, dispatch}, message) {
    commit('STOP_WAITING');
    commit('CLEAR_TIMEOUT');

    if (!message.title) {
      message.title = basicMessage.title;
    }

    if (message.confirm == false) {
      message.timeout = setTimeout(() => {
        dispatch('close');
      }, 3000);
    }

    message.showMessage = true;
    commit('UPDATE_MESSAGE', message);
  },

  // ===============================================

  close ({commit}) {
    commit('UPDATE_MESSAGE', basicMessage);
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
