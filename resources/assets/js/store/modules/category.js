import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        name: {title: "分类名称", show: true},
        ename: {title: "英文名称", show: true},
        type: {title: "分类类型", show: true},
        sequence: {title: "顺序", show: true},
        pid: {title: "父分类", show: true},
        state: {title: "状态", show: true},
        target: {title: "Target", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        name: null,
        ename: null,
        sequence: '',
        state: 1,
        target: '_blank',
        pid: null,
        type: 'article_category',
        atype: [],
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    category_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    category_types: [],
    rules: {
        name: [
            { required: true, message: '请输入分类名称', trigger: 'blur' },
            { min: 2, max: 20, message: '分类名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        ename: [
            { required: true, message: '请输入分类英文名称', trigger: 'blur' },
            { min: 2, max: 20, message: '分类英文名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        type: [
            { required: true, message: '请输入分类类型', trigger: 'blur' },
            { min: 2, max: 20, message: '分类类型长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        link: [
            { required: true, message: '请输入链接', trigger: 'blur' },
            { min: 2, max: 256, message: '链接长度在 2 到 256 个字符', trigger: 'blur' }
        ],
        target: [
            { required: true, message: '请选择Target', trigger: 'blur' },
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
    set_category_list: (state, category_lists) => {
        state.category_lists = category_lists;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_category_types: (state, category_types) => {
        state.category_types = category_types;
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
            name: null,
            ename: null,
            link: '',
            sequence: '',
            state: 1,
            tag: '',
            type: 'article_category',
            logo: '',
            atype: [],
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.category_lists = state.back_data;
        else
            state.category_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.category_lists.length;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    update_category_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        state.category_lists[prop['index']][key] = value
    },
    append_category_list: (state, row) => {
        state.category_lists.splice(0, 0, row)
    },
    sort_data: (state, {column, order})=> {
        state.category_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};
const actions = {
    get_category_lists: ({commit, state}) => {
        commit('set_loading', true);
        return new Promise((resolve, reject) => {
            axios.get('/categoryTree').then(result=> {
                console.log(result);
                if(result.data.success === 1) {
                    let category_lists = result.data.data;
                    for(let obj of category_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    console.log(category_lists);
                    commit('set_category_list', category_lists);
                    commit('set_back_data', category_lists);
                    commit('set_current_page', 1);
                    commit('set_total', category_lists.length);
                    commit('set_loading', false);
                    resolve(category_lists);
                }
                else reject()
            })
        })
    },
    get_category: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/categoryInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    set_category_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setCategoryState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_category({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addCategory", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject(result.data.errors)
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