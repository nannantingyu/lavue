import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        coin_id: {title: "币种代号", show: true},
        coin_name: {title: "币种名称", show: true},
        symble: {title: "缩写", show: true},
        sequence: {title: "顺序", show: true},
        state: {title: "状态", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        coin_id: null,
        coin_name: null,
        symbel: '',
        sequence: '',
        state: 1,
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    block_coin_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        coin_id: [
            { required: true, message: '请输入币种代号', trigger: 'blur' },
            { min: 2, max: 32, message: '币种代号长度在 3 到 32 个字符', trigger: 'blur' }
        ],
        coin_name: [
            { required: true, message: '请输入币种名称', trigger: 'blur' },
            { min: 2, max: 32, message: '币种名称长度在 3 到 32 个字符', trigger: 'blur' }
        ],
        symble: [
            { required: true, message: '请输入币种缩写', trigger: 'blur' },
            { min: 2, max: 32, message: '币种缩写长度在 3 到 32 个字符', trigger: 'blur' }
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
    set_block_coin_list: (state, block_coin_lists) => {
        state.block_coin_lists = block_coin_lists;
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
        if(state.show_type == 3) state.block_coin_lists = state.back_data;
        else
            state.block_coin_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.block_coin_lists.length;
    },
    update_block_coin_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.block_coin_lists[prop['index']][key] = value
    },
    append_block_coin_list: (state, row) => {
        state.block_coin_lists.splice(0, 0, row)
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.block_coin_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};

const actions = {
    get_block_coin_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/blockCoinLists').then(result=> {
                commit('set_loading', true);
                if(result.data.success === 1) {
                    let block_coin_lists = result.data.data;
                    for(let obj of block_coin_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_block_coin_list', block_coin_lists);
                    commit('set_back_data', block_coin_lists)
                    commit('set_current_page', 1)
                    commit('set_total', block_coin_lists.length);
                    commit('set_loading', false);
                    resolve()
                }
                else reject()
            })
        })
    },
    get_block_coin: ({commit, state}, {key})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/blockCoinInfo?key='+key).then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                    resolve(result)
                }
            })
        })
    },
    set_block_coin_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;
            axios.post('/setBlockCoinState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_block_coin({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addBlockCoin", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject(result.data.data)
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