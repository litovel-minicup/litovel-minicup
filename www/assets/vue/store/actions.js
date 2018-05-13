export default {
    subscribe({dispatch}, {match}) {
        dispatch('sendObj', {
            action: 'subscribe',
            match
        });
    },
    sendObj({state, commit}, obj) {
        console.log('Socket message', obj);
        if (state.socket.isConnected) {
            this.$socket.sendObj(obj)
        } else {
            commit('pushSocketQueue', obj)
        }
    }
}