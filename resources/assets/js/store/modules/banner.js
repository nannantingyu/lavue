const state = {
    banner_list: [],
    back_data: [],
    current_page: 1,
    total: 0,
    per_page: 10,
    columns: {
        id: {title: "ID", show: false},
        title: {title: "描述", show: true},
        sequence: {title: "排序", show: true},
        page: {title: "小程序URL", show: false},
        state: {title: "状态", show: true},
        image_url: {title: "广告图", show: true},
        link: {title: "链接", show: true},
        hits: {title: "点击量", show: true},
        type: {title: "类型", show: true},
        app: {title: "小程序", show: true},
        created_time: {title: "创建时间", show: false},
        updated_time: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        title: "",
        page: "",
        image_url: "",
        link: "",
        type: "",
        hits: 0,
        state: true,
        sequence: 1,
        app: ""
    },
    to_strings: ['app'],
    rules: {
        title: [
            {required: true, message: '请输入广告标题', trigger: 'blur'},
            {min: 2, max: 32, message: '长度在 2 到 32 个字符', trigger: 'blur'}
        ],
        sequence: [
            {required: true, message: '请输入广告在当前页面顺序', trigger: 'blur'}
        ],
        page: [
            {required: true, message: '请输入小程序地址', trigger: 'blur'}
        ],
        image_url: [
            {required: true, message: '请上传广告图片'}
        ],
        link: [
            {required: true, message: '请输入广告链接地址', trigger: 'blur'}
        ],
        type: [
            {required: true, message: '请选择广告类型', trigger: 'change'}
        ],
        app: [
            {required: true, message: '请选择小程序', trigger: 'change'}
        ]
    },
    show_type: 3,
}

const mutations = {
    set_banner_list: (state, banner_list) => {
        state.banner_list = banner_list
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data
    },
    set_current_page: (state, current_page) => {
        state.current_page = current_page
    },
    set_total: (state, total) => {
        state.total = total
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    update_banner_list_by_index: (state, prop) => {
        state.banner_list[prop['index']][prop['key']] = prop['val']
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.banner_list = state.back_data;
        else
            state.banner_list = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.banner_list.length;
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_default_form: (state) => {
        state.form = {
            id: null,
            title: "",
            page: "",
            image_url: "",
            link: "",
            type: "",
            hits: 0,
            state: true,
            sequence: 1,
            app: ""
        }
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    }
}

const getters = {
    fileimgs: state=> {
        if(state.form.image_url)
            return [{url: state.form.image_url}];

        return [];
    },
}

const actions = {
    get_all_banners: ({ commit, state }) => {
        axios.get("/bannerList").then(function(result) {
            if(result.data.success === 1)
            {
                commit('set_banner_list', result.data.data)
                commit('set_back_data', result.data.data)
                commit('filte_data')
                commit('set_current_page', 1)
                commit('set_total', result.data.data.length)
            }
        });
    },
    set_banner_state ({commit}, {id, state, index})  {
        return new Promise((resolve, reject)=> {
            axios.post('/setBannerState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) {
                        commit("update_banner_list_by_index", {
                            index: index,
                            key: "state",
                            val: state
                        });

                        resolve()
                    }
                    else {
                        reject()
                    }
                });
        })
    },
    get_banner_info({ commit, state }, id) {
        return new Promise((resolve, reject)=> {
            axios.get('/getBanner?id=' + id).then(function(result) {
                if(result.data.success === 1) {
                    let value = ""
                    for (let o in result.data.data) {
                        if(state.form.hasOwnProperty(o)) {
                            value = result.data.data[o]
                            if(state.to_strings.indexOf(o) >= 0)
                                value = value.toString()

                            commit('set_form_value', {key: o, value: value})
                        }
                    }

                    resolve()
                }
                else reject()
            });
        })
    },
    add_or_update_banner({ commit }, form) {
        return new Promise((resolve, reject) => {
            axios.post("/addBanner", form).then(function(result){
                if(result.data.success === 1) {
                    resolve()
                }
                else {
                    reject()
                }
            });
        });
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}