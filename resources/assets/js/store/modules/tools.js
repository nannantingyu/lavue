import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        title: {title: "标题", show: true},
        description: {title: "描述", show: true},
        image: {title: "二维码", show: true},
        tag: {title: "标签", show: true},
        url: {title: "url", show: true},
        sequence:{title: "顺序", show: true},
        state:{title: "状态", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false}
    },
    lists: [],
    lists_all: [],
    back_data: [],
    per_page: 10,
    current_page: 1,
    total: 0,
    state: -1,
    loading: false,
    radio:"全部",
    dialogFormVisible: false,
    rules: {
        image: [
            { required: true, message: '请上传图片', trigger: 'blur' }
        ],
        title: [
            { required: true, message: '请输入标题', trigger: 'blur' },
            { min: 2, max: 32, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
        ],
        description: [
            { required: true, message: '请输入描述', trigger: 'blur' },
        ],
        title: [
            { required: true, message: '请输入标题', trigger: 'blur' },
            { min: 2, max: 32, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
        ],
        tag: [
            { required: true, message: '请输入标签', trigger: 'blur' },
            { min: 2, max: 32, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
        ],
        sequence: [
            { required: true, message: '请输入顺序', trigger: 'blur' },
            { validator: check_integer_factory('顺序为数字类型'), trigger: 'blur' }
        ]
    }
};

const mutations = {
    set_lists_all: (state, list) => {
        state.lists_all = list
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data
    },
    set_list: (state, list) => {
        state.lists = list
    },
    set_dialogFormVisible: (state, value) => {
        state.dialogFormVisible = value;
    },
    set_current_page: (state, current_page) => {
        state.current_page = current_page;
    },
    set_total: (state, total) => {
        state.total = total
    },
    set_per_page: (state, per_page) => {
        state.per_page = per_page
    },
    set_feed_state:(state, r)=>{
        state.state = r
    },
    filter_data:(state, r)=>{
        if(r==0){
            state.lists = state.lists_all;
        }
        else if(r==1){
            let arr=[];
            state.lists_all.map((item)=>{
                if(item.state==1){
                    arr.push(item);
                }
            })
            state.lists = arr;
        }
        else if(r==2){
            let arr=[];
            state.lists_all.map((item)=>{
                if(item.state==0){
                    arr.push(item);
                }
            })
            state.lists = arr;
        }
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    set_radio: (state, radio) => {
        state.radio = radio;
    },
    sort_data: (state, {column, order})=> {
        state.lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};

const actions = {
    add_update:({commit, state},form) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/tool/add', form).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(data)
            })
        })
    },
    get_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            commit('set_loading', true);
            axios.get('/api/tool/list').then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    for(let o of data) o.state = o.state === 1;

                    commit('set_list', data);
                    commit('set_lists_all', data);
                    commit('set_back_data', data);
                    commit('set_loading', false);
                    commit('set_current_page', 1);
                    commit('set_total', data.length);
                    resolve(data);
                }
                else reject(data)
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