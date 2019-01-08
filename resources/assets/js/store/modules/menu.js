import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        name: {title: "标题", show: true},
        url: {title: "链接", show: true},
        target: {title: "target", show: true},
        area: {title: "区域", show: true},
        sequence:{title: "顺序", show: true},
        state:{title: "状态", show: true},
        title:{title: "title(SEO)", show: true},
        keywords:{title: "keywords(SEO)", show: false},
        description:{title: "description(SEO)", show: false},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false}
    },
    menu_lists: [],
    menu_lists_all: [],
    pageSize:10,
    currentPage:1,
    total:0,
    state:-1//0未解决 1已解决 -1全部
};

const mutations = {
    set_current_page: (state, current_page) => {
        state.currentPage = current_page;
    },
    set_page_size: (state, pageSize) => {
        state.pageSize = pageSize
    },
    set_menu_lists_all: (state, list) => {
        state._menu_lists_all = list
    },
    set_menu_list: (state, list) => {
        state.menu_lists = list
    },
    set_feed_state:(state, r)=>{
        state.state = r
    },
    filter_data:(state, r)=>{
        // 0:全部
        // 1:顶部菜单
        // 2:底部链接
        if(r==0){
            state.menu_lists = state._menu_lists_all;
        }
        else if(r==1){
            let arr=[];
            state._menu_lists_all.map((item)=>{
                if(item.area=='main'){
                    arr.push(item);
                }
            })
            state.menu_lists = arr;
        }
        else if(r==2){
            let arr=[];
            state._menu_lists_all.map((item)=>{
                if(item.area=='bottom'){
                    arr.push(item);
                }
            })
            state.menu_lists = arr;
        }
    }
};

const actions = {
    set_feed_state({commit}, row)  {
        return new Promise((resolve, reject)=> {
            axios.post('/api/addFeedback', row)
                .then(function(result) {
                    if(result.data.success === 1){
                        resolve()
                    }
                    else reject()
                });
        })
    },
    add_update:({commit, state},form) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/menu/add',form).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(result.data.msg)
            })
        })
    },
    get_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/api/menu/list').then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    for(let o of data) {
                        if(o.state==0){
                            o.state=false;
                        }
                        else if(o.state==1){
                            o.state=true;
                        }
                        if(!o.target) {
                            o.target="_self"
                        }
                    }
                    commit('set_menu_list', data);
                    commit('set_menu_lists_all', data);
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