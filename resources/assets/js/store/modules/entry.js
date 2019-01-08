import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        entry_name: {title: "词条名称", show: true},
        description: {title: "描述", show: true},
        state: {title: "状态", show: true},
        sequence: {title: "顺序", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        entry_name: '',
        description: '',
        sequence: 1,
        state: 1,
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    entry_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    current_page_article: 1,
    total: 0,
    per_page: 10,
    total_article: 0,
    per_page_article: 10,
    dialog_visible: false,
    dialog_article_visible: false,
    row_index: 0,
    entry_types: [],
    search_article: null,
    search_article_lists: [],
    article_options: [],
    current_entry_id: null,
    entry_articles: [],
    rules: {
        entry_name: [
            { required: true, message: '请输入词条名称', trigger: 'blur' },
            { min: 2, max: 20, message: '词条名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        description: [
            { required: true, message: '请输入分类类型', trigger: 'blur' },
            { min: 2, max: 20, message: '分类类型长度在 2 到 20 个字符', trigger: 'blur' }
        ],
    },
};
const mutations = {
    set_current_page: (state, current_page) => {
        state.current_page = current_page
    },
    set_current_page_article: (state, current_page_article) => {
        state.current_page_article = current_page_article
    },
    set_entry_articles: (state, entry_articles) => {
        state.entry_articles = entry_articles;
    },
    set_total: (state, total) => {
        state.total = total
    },
    set_total_article: (state, total_article) => {
        state.total_article = total_article
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    set_per_page_article: (state, per_page_article) => {
        state.per_page_article = per_page_article
    },
    set_current_entry_id: (state, entry_id) => {
        state.current_entry_id = entry_id;
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_entry_list: (state, entry_lists) => {
        state.entry_lists = entry_lists;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_entry_types: (state, entry_types) => {
        state.entry_types = entry_types;
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
    set_dialog_article_visible: (state, value) => {
        state.dialog_article_visible = value;
    },
    set_search_article: (state, search_article) => {
        state.search_article = search_article;
    },
    set_search_article_lists: (state, search_article_lists) => {
        state.search_article_lists = search_article_lists;
    },
    set_article_options: (state, article_options) => {
        state.article_options = article_options;
    },
    set_form: (state, form) => {
        for (let o in form) {
            if(state.form.hasOwnProperty(o)) {
                state.form[o] = form[o]
            }
        }
    },
    add_related_article: (state, entry_id, article_id) => {
        console.log("add related article", entry_id, article_id);
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
            type: 'article_entry',
            logo: '',
            atype: [],
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.entry_lists = state.back_data;
        else
            state.entry_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.entry_lists.length;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    update_entry_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        state.entry_lists[prop['index']][key] = value
    },
    append_entry_list: (state, row) => {
        state.entry_lists.splice(0, 0, row)
    },
    sort_data: (state, {column, order})=> {
        state.entry_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};
const actions = {
    get_entry_lists: ({commit, state}) => {
        commit('set_loading', true);
        return new Promise((resolve, reject) => {
            axios.get('/entryLists').then(result=> {
                if(result.data.success === 1) {
                    let entry_lists = result.data.data;
                    for(let obj of entry_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_entry_list', entry_lists);
                    commit('set_back_data', entry_lists);
                    commit('set_current_page', 1);
                    commit('set_total', entry_lists.length);
                    commit('set_loading', false);
                    resolve(entry_lists);
                }
                else reject()
            })
        })
    },
    addEntryArticle: ({commit, state}, {entry_id, article_id}) => {
        return new Promise((resolve, reject)=> {
            axios.post("/addEntryArticle", {entry_id:entry_id, article_id:article_id})
                .then(result=>{
                if(result.data.success === 1) resolve();
                else reject();
            })
        })
    },
    removeEntryArticle: ({commit, state}, {entry_id, article_id}) => {
        return new Promise((resolve, reject)=> {
            axios.post("/removeEntryArticle", {entry_id:entry_id, article_id:article_id})
                .then(result=>{
                    if(result.data.success === 1) resolve();
                    else reject();
                })
        })
    },
    get_entry: ({commit, state}, id)=> {
        return new Promise((resolve, reject)=> {
            axios.get('/entryInfo?id='+id).then(result=> {
                if(result.data.success === 1) {
                    resolve(result.data.data)
                }
            })
        })
    },
    set_entry_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setEntryState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_entry({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addEntry", subform).then(function(result){
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