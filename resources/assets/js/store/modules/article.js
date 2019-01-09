import {check_time, check_integer_factory} from '../../plugin/tool'
import moment from 'moment'
const state = {
    current_page: 1,
    total: 0,
    per_page: 10,
    loading: true,
    article_lists: [],
    back_data: [],
    show_type: 3,
    types: [],
    article_now: {},
    editor: null,
    search_key: '',
    category: [],
    source_sites: [],
    source_sites_bak: [],
    order: 'desc',
    order_by: 'id',
    sites: [],
    selected: [],
    is_selected: true,
    time_key: 'publish_time',
    st: '',
    et: '',
    dialog_visible_list: false,
    dialog_visible_add: false,
    search_source_site_key: '',
    columns: {
        id: {title: "ID", show: true},
        title: {title: "标题", show: true},
        image: {title: "封面图", show: true},
        description: {title: "描述", show: false},
        keywords: {title: "关键词", show: false},
        author: {title: "作者", show: false},
        publish_time: {title: "发布时间", show: true},
        type: {title: "分类", show: true},
        hits: {title: "点击量", show: true},
        url: {title: "链接", show: false},
        state: {title: "状态", show: true},
        recommend: {title: "推荐", show: false},
        favor: {title: "点赞人数", show: false},
        source_type: {title: "原创类型", show: false},
        source_url: {title: "来源链接", show: true},
        source_site: {title: "来源网站", show: true},
        created_time: {title: "创建时间", show: true},
        updated_time: {title: "更新时间", show: false},
    },
    rules: {
        title: [
            { required: true, message: '请输入标题', trigger: 'blur' },
            { min: 2, max: 64, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
        ],
        description: [
            { required: true, message: '请输入描述', trigger: 'blur' },
            { min: 2, max: 512, message: '描述在 3 到 512 个字符', trigger: 'blur' }
        ],
        keywords: [
            { max: 64, message: '关键词最多 16 个字符', trigger: 'blur' }
        ],
        author: [
            { max: 16, message: '作者最多 16 个字符', trigger: 'blur' }
        ],
        publish_time: [
            { validator: check_time("发布时间不正确"), trigger: 'change' }
        ],
        image: [
            { required: true, message: '请上传图片' },
        ],
        type: [
            { required: true, message: '请输入分类', trigger: 'blur' },
            { min: 2, max: 32, message: '分类在 3 到 32 个字符', trigger: 'blur' }
        ],
        hits: [
            { validator: check_integer_factory('点击量为数字类型'), trigger: 'blur' }
        ],
        favor: [
            { validator: check_integer_factory('点赞数为数字类型'), trigger: 'blur' }
        ],
        source_type: [
            { required: true, message: '请选择原创类型', trigger: 'blur' },
        ]
    },
    modules: [],
    article_categories: [],
    article_categories_group: {},
    form: {
        id: null,
        title: '',
        image: '',
        description: '',
        keywords: '',
        author: '',
        publish_time: moment().format('YYYY-MM-DD HH:mm:ss'),
        type: '',
        hits: 0,
        state: true,
        categories: [],
        recommend: '',
        favor: 1,
        source_type: 'original',
        created_at: moment().format('YYYY-MM-DD HH:mm:ss'),
        updated_at: moment().format('YYYY-MM-DD HH:mm:ss')
    },
    site_form: {
        site: '',
        state: 1,
        old_site: ''
    },
    site_rules: {
        site: [
            { required: true, message: '请输入来源网站名称', trigger: 'blur' },
            { min: 2, max: 32, message: '来源网站名称长度在 3 到 32 个字符', trigger: 'blur' }
        ],
    },
    to_strings: [],
    redownload_file_lists: [],
    to_booleans: ['state', 'recommend']
};
const mutations = {
    set_article_list: (state, article_lists) => {
        state.article_lists = article_lists;
    },
    set_search_key: (state, search_key) => {
        state.search_key = search_key;
    },
    set_category: (state, category) => {
        state.category = category;
    },
    set_time_key: (state, time_key) => {
        state.time_key = time_key;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    set_dialog_visible_list: (state, dialog_visible_list) => {
        state.dialog_visible_list = dialog_visible_list;
    },
    set_dialog_visible_add: (state, dialog_visible_add) => {
        state.dialog_visible_add = dialog_visible_add;
    },
    set_st: (state, st) => {
        state.st = st;
    },
    set_et: (state, et) => {
        state.et = et;
    },
    append_redownload_file_lists: (state, file) => {
        state.redownload_file_lists.push(file);
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_is_selected: (state, is_selected) => {
        state.is_selected = is_selected;
    },
    set_editor: (state, editor) => {
        state.editor = editor;
    },
    set_selected: (state, selected) => {
        state.selected = selected;
    },
    set_current_page: (state, current_page) => {
        state.current_page = current_page
    },
    set_source_sites: (state, {source_sites, is_bak}) => {
        let sites = [];
        for(let site of source_sites) {
            sites.push({
                'site': site['site'],
                'state': site['state'] == 1
            });
        }

        is_bak && (state.source_sites_bak = sites)
        const search_source_sites = state.source_sites_bak.filter(x=>{
            return x.site.indexOf(state.search_source_site_key) > -1;
        });

        state.source_sites = search_source_sites
    },
    set_search_source_site_key: (state, search_source_site_key) => {
        state.search_source_site_key = search_source_site_key
    },
    set_order: (state, order) => {
        state.order = order
    },
    set_order_by: (state, order_by) => {
        state.order_by = order_by
    },
    set_sites: (state, sites) => {
        state.sites = sites
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
    set_article_now: (state, article_now) => {
        state.article_now = article_now;
    },
    set_article_categories: (state, article_categories) => {
        state.article_categories = article_categories;
        state.article_categories_group = {};
        for(let cat of article_categories) {
            if(!state.article_categories_group.hasOwnProperty(cat['type'])) {
                state.article_categories_group[cat['type']] = {
                    'data': [cat],
                    'label': cat['type']
                };
            }
            else state.article_categories_group[cat['type']]['data'].push(cat);
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.article_lists = state.back_data;
        else
            state.article_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.article_lists.length;
    },
    filte_search: (state) => {
        if(!state.search_key) state.article_lists = state.back_data;
        else
            state.article_lists = state.back_data.filter(x=>{
                return x.title.indexOf(state.search_key) >= 0 || x.description.indexOf(state.search_key) >= 0;
            });

        state.total = state.article_lists.length;
    },
    update_article_list_by_index: (state, prop) => {
        state.article_lists[prop['index']][prop['key']] = prop['val']
    },
    update_article_list_by_id: (state, prop) => {
        for(let index in state.article_lists) {
            if(state.article_lists[index]['id'] === prop['id']) {
                state.article_lists[index][prop['key']] = prop['val'];
                break;
            }
        }
    },
    delete_article: (state, index) => {
        state.article_lists.splice(index, 1)
    },
    set_default_form: (state) => {
        state.form = {
            id: null,
            title: '',
            image: '',
            description: '',
            keywords: '',
            author: '',
            publish_time: moment().format('YYYY-MM-DD HH:mm:ss'),
            type: '',
            hits: 1,
            state: true,
            categories: [],
            recommend: '',
            favor: 1,
            source_type: 'original',
            created_at: moment().format('YYYY-MM-DD HH:mm:ss'),
            updated_at: moment().format('YYYY-MM-DD HH:mm:ss')
        }
    },
    set_default_site_form: (state) => {
        state.site_form = {
            name: '',
            old_name: '',
            state: false,
        }
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    },
    set_site_form_value: (state, {key, value})=> {
        state.site_form[key] = value
    },
    clear_filter_options: (state) => {
        state.search_key = '';
        state.sites = [];
        state.category = [];
        state.st = null;
        state.et = null;
        state.time_key = 'id';
        state.order_by = 'id';
        state.order = 'desc';
        state.show_type = 3;
    }
}
const getters = {
    type_filter: state=> {
        let types = [];
        for(let ar of state.article_categories) {
            types.push({text: ar['name'], value: ar['name']});
        }

        return types;
    },
    fileimgs: state=> {
        let imgs = [];
        if(state.form.image)
            imgs.push({url: 'http://images.jujin8.com'+state.form.image.replace('/uploads/crawler', '/uploads')});

        return imgs;
    }
};
const actions = {
    get_data({ commit, state }) {
        return new Promise((resolve, reject)=> {
            commit('set_loading', true);
            let params = {
                page: state.current_page,
                num: state.per_page,
                order: state.order,
                order_by: state.order_by,
                state: state.show_type
            };

            if(state.search_key) params['keywords'] = state.search_key
            if(state.sites.length > 0) params['sites'] = state.sites.join(',')
            if(state.category.length > 0) params['category'] = state.category.join(',')
            if(state.st || state.et) {
                params['time_key'] = state.time_key;
                state.st && (params['st'] = state.st);
                state.et && (params['et'] = state.et);
            }

            axios.get("/articleLists", {params: params}).then(function(result){
                if(result.data.success === 1)
                {
                    let article_lists = result.data.data.data;
                    for(let obj of article_lists) {
                        obj['need_check'] = (obj['state'] === -1);
                        const imgs = JSON.parse(obj['image']);
                        obj['image'] = imgs.length > 0?imgs[0]:"http://image.yjshare.cn/images/default.jpg";
                        obj['image_alt'] = obj['image'];
                        obj['image_exist'] = true;
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) {
                                if (obj[o] === 1) {
                                    obj[o] = true;
                                }
                                else obj[o] = false;
                            }
                        }
                    }

                    commit('set_article_list', article_lists);
                    commit('set_back_data', article_lists);
                    commit('set_current_page', result.data.data.current_page);
                    commit('set_total', result.data.data.total);
                    commit('set_loading', false);
                    resolve()
                }
                else reject()
            })
        })
    },
    redownload_img ({commit, state}, url) {
        return new Promise((resolve, reject)=> {
            axios.post('/redownloadImage', {url: url}).then(result=> {
                if(result.data.success === 1) resolve();
                else reject();
            })
        })
    },
    set_article_state ({commit}, {id, state, index})  {
        return new Promise((resolve, reject)=> {
            axios.post('/setArticleState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) {
                        commit("update_article_list_by_index", {
                            index: index,
                            key: "state",
                            val: state===1
                        });

                        resolve()
                    }
                    else {
                        reject()
                    }
                });
        })
    },
    get_data_by_category: ({state, commit}) => {
        return new Promise((resolve, reject)=> {
            axios.get("/articleListsByCategory?categories="+state.category.join(',')).then(function(result){
                if(result.data.success === 1)
                {
                    commit('set_article_list', result.data.data)
                    commit('set_back_data', result.data.data)
                    commit('set_current_page', 1)
                    commit('set_total', result.data.data.length)
                    resolve()
                }
                else reject()
            })
        })
    },
    multiOffline: function({state, commit}, params) {
        return new Promise((resolve, reject)=> {
            axios.post("/multiOffline", params).then(result=> {
                if(result.data.success === 1) resolve()
                else reject()
            })
        })
    },
    multiOnline: function({state, commit}, params) {
        return new Promise((resolve, reject)=> {
            axios.post("/multiOnline", params).then(result=> {
                if(result.data.success === 1) resolve()
                else reject()
            })
        })
    },
    multiDelete: function({state, commit}, params) {
        return new Promise((resolve, reject)=> {
            axios.post("/multiDelete", params).then(result=> {
                if(result.data.success === 1) resolve()
                else reject()
            })
        })
    },
    getArticle: function({state, commit}, id) {
        return new Promise((resolve, reject)=> {
            axios.get("/articleInfo?id="+id)
                .then(function(result){
                    if(result.data.success === 1) {
                        let value = ""
                        for (let o in result.data.data) {
                            if(state.form.hasOwnProperty(o)) {
                                value = result.data.data[o]
                                if(state.to_strings.includes(o))
                                    value = value?value.toString():value

                                else if(state.to_booleans.includes(o))
                                    value = value == 1 || !!value
                                else if(o == 'categories') {
                                    value = value.map(x=>{
                                        return x.id
                                    });

                                    value = Array.from(new Set(value));
                                }

                                commit('set_form_value', {key: o, value: value})
                            }
                        }

                        commit('set_article_now', result.data.data)
                        let pattern = /\/uploads\/crawler/g;
                        let body_add_strip = result.data.data.body.body.replace(pattern, 'http://images.jujin8.com/uploads');

                        console.log(body_add_strip);
                        state.editor.setContent(body_add_strip);
                        resolve()
                    }
                    else {
                        reject();
                    }
            });
        })
    },
    add_or_update_source_site({ commit, state }) {
        return new Promise((resolve, reject) => {
            let form = {
                site: state.site_form.site,
                old_site: state.site_form.old_site,
                state: state.site_form.state?1:0
            };

            axios.post("/addOrUpdateSourceSite", form).then(function(result){
                if(result.data.success === 1) {
                    commit('set_source_sites', {source_sites: result.data.data, is_bak: true});
                    resolve()
                }
                else reject()
            });
        })
    },
    remove_source_site({ commit, state }, site) {
        return new Promise((resolve, reject) => {
            axios.post("/removeSourceSite", {site: site}).then(function(result){
                if(result.data.success === 1) {
                    commit('set_source_sites', {source_sites: result.data.data, is_bak: true});
                    resolve()
                }
                else reject()
            });
        })
    },
    add_or_update_article({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let pattern = /http:\/\/images\.jujin8\.com\/uploads/g, body = form.body || state.editor.getContent();
            form.body = body.replace(pattern, '/uploads');
            form.state = form.state?1:0;
            form.recommend = form.recommend?1:0;

            axios.post("/addArticle", form).then(function(result){
                if(result.data.success === 1) {
                    resolve()
                }
                else {
                    reject(result.data.errors)
                }
            });
        });
    },
    delete_article({ commit, state }, {id, index}) {
        return new Promise((resolve, reject) => {
            axios.post("/deleteArticle", {id: id}).then(function(result){
                if(result.data.success === 1) {
                    commit("delete_article", index);
                    resolve()
                }
                else {
                    reject()
                }
            });
        });
    },
    search_article({ commit, state }, keywords) {
        return new Promise((resolve, reject) => {
            axios.post("/searchArticle", {keywords: keywords}).then(function(result){
                if(result.data.success === 1) {
                    for(let obj of result.data.data) {
                        const images = JSON.parse(obj['image']);
                        obj['image'] = images.length > 0?images[0]:"https://image.yjshare.cn/default.jpg";
                    }
                    resolve(result.data)
                }
                else {
                    reject()
                }
            });
        });
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    getters,
    actions
}