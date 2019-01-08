export default {
    get_app_name ({ state, commit, rootState }) {
        return new Promise((resolve, reject) => {
            axios.get('/getAppName').then(function(result){
                if(result.data.success === 1){
                    commit("set_app_name", result.data.data)
                    resolve()
                }
            })
        })
    }
}