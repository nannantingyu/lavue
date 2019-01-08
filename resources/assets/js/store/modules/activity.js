import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        cid: {title: "CID(分类ID)", show: true},
        title: {title: "标题", show: true},
        image: {title: "图片", show: true},
        page: {title: "page", show: true},
        link: {title: "链接", show: true},
        sequence:{title: "顺序", show: true},
        state:{title: "状态", show: true},
        hits: {title: "点击量", show: false},
        expire_time: {title: "过期时间", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false}
    },
    lists: [],
    lists_all: [],
    pageSize:10,
    currentPage:1,
    total:0,
    state:-1
};

const mutations = {
    set_lists_all: (state, list) => {
        state.lists_all = list
    },
    set_list: (state, list) => {
        state.lists = list;
    },
    set_feed_state:(state, r)=>{
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
        console.log(form,"jjjjj")
        return new Promise((resolve, reject) => {
            axios.post('/api/banner/add',form).then(result=> {
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
            axios.get('/api/banner/list').then(result=> {
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