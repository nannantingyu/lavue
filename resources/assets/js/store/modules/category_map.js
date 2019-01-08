import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        source_category: {title: "分类名称", show: true},
        source_site: {title: "原站点", show: true},
        target: {title: "目标名称", show: true},
        state: {title: "状态", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        source_category: null,
        target: null,
        source_site: null,
        state: 1,
        created_at: '',
        updated_at: ''
    },
    sites: [],
    to_strings: [],
    to_booleans: ['state'],
    category_map_lists: [],
    category_list: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        source_category: [
            { required: true, message: '请输入源分类名称', trigger: 'blur' },
            { min: 2, max: 20, message: '源分类名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        source_site: [
            { required: true, message: '请输入源网站', trigger: 'blur' },
            { min: 2, max: 20, message: '源网站长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        target: [
            { required: true, message: '请选择目标分类', trigger: 'change' },
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
    set_sites: (state, sites) => {
        state.sites = sites
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_category_map_list: (state, category_map_lists) => {
        state.category_map_lists = category_map_lists;
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
    set_category_list: (state, value) => {
        state.category_list = value;
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
            source_category: null,
            target: null,
            source_site: null,
            state: 1,
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type === 3) state.category_map_lists = state.back_data;
        else
            state.category_map_lists = state.back_data.filter(x=>{
                return x.state === state.show_type;
            });

        state.total = state.category_map_lists.length;
    },
    update_category_map_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.category_map_lists[prop['index']][key] = value
    },
    append_category_map_list: (state, row) => {
        state.category_map_lists.splice(0, 0, row)
    },
    sort_data: (state, {column, order})=> {
        state.category_map_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    },
    filter_data_by_site: (state, sites) =>{
        if(!sites || sites.length === 0) {
            state.category_map_lists = state.back_data;
        }
        else {
            state.category_map_lists = state.back_data.filter(x=>sites.includes(x.source_site));
        }

        state.total = state.category_map_lists.length;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
};
const actions = {
    get_category_map_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/categoryMapLists').then(result=> {
                if(result.data.success === 1) {
                    let category_map_lists = result.data.data;
                    for(let obj of category_map_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_category_map_list', category_map_lists);
                    commit('set_back_data', category_map_lists);
                    commit('set_current_page', 1);
                    commit('set_total', category_map_lists.length);
                    commit('set_loading', false);
                    resolve(category_map_lists)
                }
                else reject()
            })
        })
    },
    get_category_map: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/categoryMapInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    set_category_map_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setCategoryMapState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_category_map({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addCategoryMap", subform).then(function(result){
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