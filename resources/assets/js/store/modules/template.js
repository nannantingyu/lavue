const state = {
    form: {
        url: null,
        type: "update",
    },
    to_strings: [''],
    rules: {
        url: [
            {required: true, message: '请输入链接', trigger: 'blur'},
            {min: 2, max: 32, message: '链接在 2 到 32 个字符', trigger: 'blur'}
        ],
    },
}

const mutations = {
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_default_form: (state) => {
        state.form = {
            url: "",
            type: "update",
        }
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    }
}

const actions = {
    generate_template: ({ commit, state }) => {
        return new Promise((resolve, reject) => {
            axios.post("/genetateTemplate", state.form).then(function(result) {
                if(result.data.success === 1) resolve();
                else reject()
            });
        })
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}