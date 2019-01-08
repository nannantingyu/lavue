const moment = require('moment');
const state = {
    banner_state_list: [],
    back_data: [],
    current_page: 1,
    total: 0,
    edit_row: {},
    per_page: 10,
    columns: {
        id: {title: "ID", show: true},
        page: {title: "小程序URL", show: true},
        state: {title: "状态", show: true},
        app: {title: "小程序", show: true},
        updated_time: {title: "更新时间", show: true},
    },
    sort_col: 'updated_time',
    sort_order: 'descending',
    to_strings: ['app'],
    rules: {
        page: [
            {required: true, message: '请输入小程序地址', trigger: 'blur'}
        ],
        app: [
            {required: true, message: '请选择小程序', trigger: 'change'}
        ]
    },
    form: {
        id: "",
        app: "",
        state: 1,
        updated_time: ""
    },
    show_type: 3,
    dialog_visible: false,
    tb: null,
}

const mutations = {
    set_tb: (state, tb) => {
      state.tb = tb
    },
    set_sort_col: (state, sort_col) => {
        state.sort_col = sort_col
    },
    set_sort_order: (state, sort_order) => {
        state.sort_order = sort_order
    },
    set_edit_row: (state, edit_row) => {
        state.edit_row = edit_row
    },
    set_banner_state_list: (state, banner_state_list) => {
        state.banner_state_list = banner_state_list
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
    set_dialog_visible: (state, dialog_visible) => {
        state.dialog_visible = dialog_visible
    },
    update_banner_state_list_by_index: (state, prop) => {
        state.banner_state_list[prop['index']][prop['key']] = prop['value']
    },
    add_new_banner_state: (state, row)=> {
        state.banner_state_list.unshift(row);
    },
    filter_data: (state) => {
        if(state.show_type == 3) state.banner_state_list = state.back_data;
        else
            state.banner_state_list = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.banner_state_list.length
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_default_form: (state) => {
        state.form = {
            id: "",
            app: "",
            state: 1,
            updated_time: ""
        }
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    }
}

const actions = {
    set_form: ({commit, state}, data) => {
        const to_strings = ['app'];
        data = data || state.edit_row
        for(let o in data) {
            let value = data[o]
            if(to_strings.indexOf(o) >= 0) value = value.toString()
            commit('set_form_value', {key: o, value: value})
        }
    },
    get_banner_state_list: ({ commit, state })=> {
        axios.get("/bannerStateList").then(function(result) {
            if(result.data.success === 1)
            {
                commit('set_banner_state_list', result.data.data)
                commit('set_back_data', result.data.data)
                commit('filter_data')
                commit('set_current_page', 1)
                commit('set_total', result.data.data.length)
            }
        });
    },
    set_banner_state_state ({commit, state}, {row, index}) {
        return new Promise((resolve, reject)=> {
            axios.post('/setPageBannerState', {id: row.id, state: 1-row.state})
                .then(function(result) {
                    if(result.data.success === 1) {
                        resolve();
                        state.banner_state_list[index]['state'] = 1-row.state;
                    }
                    else reject()
                });
        });
    },
    add_or_update_banner_state ({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.post("/addBannerState", state.form).then(function(result){
                if(result.data.success === 1) {
                    commit('set_form_value', { key: 'updated_time', value: moment().format("YYYY-MM-DD HH:mm:ss") });
                    commit('set_form_value', { key: 'id', value: result.data.id });

                    let index = -1;
                    for (index in state.banner_state_list) {
                        if (result.data.id === state.banner_state_list[index]['id']) break
                    }

                    if (index !== state.banner_state_list.length-1) {
                        for(let key in state.form) {
                            commit('update_banner_state_list_by_index', {index: index, key: key, value: state.form[key]})
                        }
                    }
                    else {
                        commit('add_new_banner_state', state.form)
                    }

                    state.tb.sort(state.sort_col, state.sort_order);
                    resolve()
                }
                else {
                    reject()
                }
            });
        })
    },

}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}