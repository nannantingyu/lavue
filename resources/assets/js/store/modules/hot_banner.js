import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        image: {title: "图片", show: true},
        title: {title: "标题", show: true},
        sequence: {title: "顺序", show: true},
        link: {title: "链接", show: true},
        state: {title: "状态", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        image: null,
        title: null,
        link: '',
        sequence: '',
        state: 1,
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    hot_banner_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    search_article: null,
    search_article_lists: [],
    article_options: [],
    rules: {
        title: [
            { required: true, message: '请输入标题', trigger: 'blur' },
            { min: 2, max: 32, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
        ],
        image: [
            { required: true, message: '请选择图片', trigger: 'blur' },
        ],
        link: [
            { required: true, message: '请输入链接', trigger: 'blur' },
            { min: 2, max: 256, message: '链接长度在 3 到 256 个字符', trigger: 'blur' }
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
    set_search_article: (state, search_article) => {
        state.search_article = search_article;
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_hot_banner_list: (state, hot_banner_lists) => {
        state.hot_banner_lists = hot_banner_lists;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_article_options: (state, article_options) => {
        state.article_options = article_options;
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
            image: null,
            title: null,
            link: '',
            sequence: '',
            state: 1,
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.hot_banner_lists = state.back_data;
        else
            state.hot_banner_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.hot_banner_lists.length;
    },
    update_hot_banner_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.hot_banner_lists[prop['index']][key] = value
    },
    append_hot_banner_list: (state, row) => {
        state.hot_banner_lists.splice(0, 0, row)
    },
    set_search_article_lists: (state, search_article_lists) => {
        state.search_article_lists = search_article_lists;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.hot_banner_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};

const actions = {
    get_hot_banner_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            commit('set_loading', true);
            axios.get('/hotBannerLists').then(result=> {
                if(result.data.success === 1) {
                    let hot_banner_lists = result.data.data;
                    for(let obj of hot_banner_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_hot_banner_list', hot_banner_lists);
                    commit('set_back_data', hot_banner_lists);
                    commit('set_current_page', 1);
                    commit('set_total', hot_banner_lists.length);
                    commit('set_loading', false);
                    resolve()
                }
                else reject()
            })
        })
    },
    get_hot_banner: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/hotBannerInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    set_hotBanner_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setHotbannerState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_hot_banner({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addHotBanner", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject()
            });
        });
    }
};

const getters = {
    fileimgs: state=> {
        let imgs = [];
        if(state.form.image)
            imgs.push({url: state.form.image});

        return imgs;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}