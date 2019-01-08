import {check_time, check_integer_factory} from '../../plugin/tool'
const state = {
    options:[],
    lists: [],
    pageSize:10,
    currentPage:1,
    total:0,
    siteInfo:{},
    state:-1,
    loading: false,
    article_now: {},
    editor: null,
    columns: {
        id: {title: "ID", show: true},
        title: {title: "标题", show: true},
        image: {title: "封面图", show: true},
        author: {title: "作者", show: false},
        publish_time: {title: "发布时间", show: true},
        state: {title: "状态", show: true},
        source_type: {title: "原创类型", show: false},
        source_url: {title: "来源链接", show: true},
        source_site: {title: "来源网站", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
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
    form: {
        id: null,
        title: '',
        image: '',
        description: '',
        keywords: '',
        author: '',
        publish_time: '',
        type: '',
        hits: '',
        state: '',
        categories: [],
        recommend: '',
        favor: 1,
        source_type: 'crawl',
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state', 'recommend']
};

const mutations = {
    set_current_page: (state, current_page) => {
        state.currentPage = current_page;
    },
    set_total: (state, total) => {
        state.total = total;
    },
    set_page_size: (state, pageSize) => {
        state.pageSize = pageSize
    },
    set_options:(state, options) => {
        state.options = options;
    },
    set_site_info:(state, obj) => {
        state.siteInfo = obj;
    },
    set_list: (state, list) => {
        state.lists = list;
    },
    set_lists_all: (state, list) => {
        state.lists_all = list;
    },
    delete_article: (state, index) => {
        state.article_lists.splice(index, 1)
    },
};

const actions = {
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
    set_article_state ({commit}, {id, state, index})  {
        return new Promise((resolve, reject)=> {
            axios.post('/setArticleState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) {
                        resolve(result)
                    }
                    else {
                        reject(result)
                    }
                });
        })
    },
    updateMany:({commit, state},idStr) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/article/setStates',{id:idStr}).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(data)
            })
        })
    },
    get_options: ({commit, state}) => {
        //获取 网站审核数据
        return new Promise((resolve, reject) => {
                    axios.get('/configInfo',{params: { 'key': 'manualCheck' }}).then(r=> {
                        if(r.data.success === 1) {
                            let configData = r.data.data;
                            if(configData){
                                let arr=[];
                                if(typeof configData.value == 'string'){
                                    arr=JSON.parse(configData.value);
                                }
                                else{
                                    arr=configData.value;
                                }
                                commit('set_options', arr);
                                resolve(arr);
                            }
                        }

                    })

        })
    },
    get_lists: ({commit, state}) => {
        //获取 未审核文章列表
        return new Promise((resolve, reject) => {
            axios.get('/api/article/listBySite',{params: {
                    size:state.pageSize,
                    page:state.currentPage,
                    site:state.siteInfo.site,
                    time:state.siteInfo.time }}).then(r=> {
                if(r.data.success === 1) {
                    let data = r.data.value;
                    if(data){
                        commit('set_list', data.list);
                        commit('set_total', data.count);
                        resolve(data);
                    }
                }

            })

        })
    }
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
}