import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        url: {title: "链接", show: true},
        user_id: {title: "用户", show: true},
        state: {title: "状态", show: true},
        created_at: {title: "创建时间", show: true},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        url: null,
        categories: [],
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    crawl_article_lists: [],
    category_list: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        url: [
            { required: true, message: '请输入源分类名称', trigger: 'blur' },
            { min: 2, max: 512, message: '源分类名称长度在 2 到 512 个字符', trigger: 'blur' }
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
    set_crawl_article_list: (state, crawl_article_lists) => {
        state.crawl_article_lists = crawl_article_lists;
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
            url: null,
            categories: [],
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type === 3) state.crawl_article_lists = state.back_data;
        else
            state.crawl_article_lists = state.back_data.filter(x=>{
                return x.state === state.show_type;
            });

        state.total = state.crawl_article_lists.length;
    },
    update_crawl_article_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.crawl_article_lists[prop['index']][key] = value
    },
    append_crawl_article_list: (state, row) => {
        state.crawl_article_lists.splice(0, 0, row)
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.crawl_article_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};
const actions = {
    get_crawl_article_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            commit('set_loading', true);
            axios.get('/crawlArticleLists').then(result=> {
                if(result.data.success === 1) {
                    let crawl_article_lists = result.data.data;
                    for(let obj of crawl_article_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_crawl_article_list', crawl_article_lists);
                    commit('set_back_data', crawl_article_lists);
                    commit('set_current_page', 1);
                    commit('set_total', crawl_article_lists.length);
                    commit('set_loading', false);
                    resolve(crawl_article_lists)
                }
                else reject()
            })
        })
    },
    get_crawl_article: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/crawlArticleInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    add_or_update_crawl_article({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addCrawlArticle", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject()
            });
        });
    }
};

const getters = {
    fileimgs: state=> {
        let imgs = [];
        if(state.form.logo)
            imgs.push({url: 'http://images.jujin8.com'+state.form.logo.replace('/uploads/crawler', '/uploads')});

        return imgs;
    },
    type_filter: state=> {
        let types = [];
        for(let ar of state.category_list) {
            types.push({text: ar['name'], value: ar['id']});
        }

        return types;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}