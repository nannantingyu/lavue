import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        key: {title: "键", show: true},
        value: {title: "值", show: true},
        sequence: {title: "顺序", show: true},
        group: {title: "分组", show: true},
        state: {title: "状态", show: true},
        comment: {title: "注释", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        key: null,
        value: null,
        group: '',
        sequence: '',
        state: 1,
        comment: '',
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    config_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        key: [
            { required: true, message: '请输入键', trigger: 'blur' },
            { min: 2, max: 32, message: '键长度在 3 到 32 个字符', trigger: 'blur' }
        ],
        value: [
            { required: true, message: '请输入值', trigger: 'blur' },
        ],
        group: [
            { required: true, message: '请输入分组', trigger: 'blur' },
            { min: 2, max: 32, message: '分组长度在 3 到 32 个字符', trigger: 'blur' }
        ],
        sequence: [
            { validator: check_integer_factory('顺序为数字类型'), trigger: 'blur' }
        ],
    },
};

const mutations = {
    set_current_page: (state, current_page) => {
        state.current_page = current_page
    },
    set_total: (state, total) => {
        state.total = total
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_config_list: (state, config_lists) => {
        state.config_lists = config_lists;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_row_index: (state, row_index) => {
        state.row_index = row_index;
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    },
    set_dialog_visible: (state, value) => {
        state.dialog_visible = value;
    },
    set_form: (state, form) => {
        for (let o in form) {
            if(state.form.hasOwnProperty(o)) {
                state.form[o] = form[o]
            }
        }
    },
    set_default_form: (state) => {
        state.form = {
            id: null,
            key: null,
            value: null,
            group: '',
            sequence: '',
            state: 1,
            comment: '',
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.config_lists = state.back_data;
        else
            state.config_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.config_lists.length;
    },
    update_config_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.config_lists[prop['index']][key] = value
    },
    append_config_list: (state, row) => {
        state.config_lists.splice(0, 0, row)
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.config_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};

const actions = {
    get_config_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/configLists').then(result=> {
                commit('set_loading', true);
                if(result.data.success === 1) {
                    let config_lists = result.data.data;
                    for(let obj of config_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_config_list', config_lists);
                    commit('set_back_data', config_lists)
                    commit('set_current_page', 1)
                    commit('set_total', config_lists.length);
                    commit('set_loading', false);
                    resolve()
                }
                else reject()
            })
        })
    },
    get_config: ({commit, state}, {key})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/configInfo?key='+key).then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                    resolve(result)
                }
            })
        })
    },
    set_config_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setConfigState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_config({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addConfig", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject()
            });
        });
    }
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
}