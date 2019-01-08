import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        title: {title: "标题", show: false},
        body: {title: "内容", show: true},
        image: {title: "图片", show: false},
        importance: {title: "重要性", show: true},
        source_site:{title: "源站", show: true},
        source_id:{title: "源站ID", show: false},
        state:{title: "状态", show: true},
        description:{title: "description", show: true},
        publish_time: {title: "发布时间", show: false},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false}
    },
    lists: [],
    lists_all: [],
    pageSize:10,
    currentPage:1,
    total:0,
    state:"",
    startTime:"",
    endTime:""
};

const mutations = {
    set_date_range: (state, arr) => {
        state.startTime = arr[0];
        state.endTime = arr[1];
    },
    set_current_page: (state, current_page) => {
        state.currentPage = current_page;
    },
    set_page_size: (state, pageSize) => {
        state.pageSize = pageSize
    },
    set_total: (state, total) => {
        state.total = total;
    },
    set_lists_all: (state, list) => {
        state.lists_all = list
    },
    set_list: (state, list) => {
        state.lists = list
    },
    set_state:(state, r)=>{
        state.state = r
    },
    filter_data:(state, r)=>{
        // 0:全部
        // 1:上线
        // 2:下线
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
    }
};

const actions = {
    add_update:({commit, state},form) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/kuaixun/addKuaiXun',form).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(result.data)
            })
        })
    },
    get_lists: ({commit, state}) => {

        return new Promise((resolve, reject) => {
            axios.get('/api/kuaixun/pagelist', {
                params: {
                    type:"kuaixun",
                    order:"publish_time",
                    isDesc:true,
                    page:(state.currentPage-1),
                    pageSize:state.pageSize,
                    state:state.state
                }}).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    for(let o of data.value) {
                        if(o.state==0){
                            o.state=false;
                        }
                        else if(o.state==1){
                            o.state=true;
                        }
                    }
                    commit('set_list', data.value);
                    commit('set_lists_all', data.value);
                    commit('set_total', data.count);
                    resolve(data);
                }
                else reject(result)
            })
        })
    },
    get_lists_date: ({commit, state}) => {

        return new Promise((resolve, reject) => {
            axios.get('/api/kuaixun/getList', {
                params: {
                    type:"kuaixun",
                    startTime:state.startTime,
                    endTime:state.endTime
                }}).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    for(let o of data) {
                        if(o.state==0){
                            o.state=false;
                        }
                        else if(o.state==1){
                            o.state=true;
                        }
                    }
                    commit('set_list', data);
                    commit('set_lists_all', data);
                    console.log(data)
                    resolve(data);
                }
                else reject(result)
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