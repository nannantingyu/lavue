import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        is_handling: {title: "是否处理", show: true},
        content: {title: "内容", show: true},
        phone: {title: "电话", show: true},
        ip: {title: "IP", show: true},
        created_at: {title: "创建时间", show: true},
        updated_at: {title: "更新时间", show: true}
    },
    feed_lists: [],
    pageSize:10,
    currentPage:1,
    total:0,
    state:-1//0未解决 1已解决 -1全部
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
    set_feedback_list: (state, list) => {
        state.feed_lists = list
    },
    set_feed_state:(state, r)=>{
        state.state = r
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
    get_feed_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/api/feedback/getList', {
                params: {page:(state.currentPage-1),pageSize:state.pageSize,state:state.state}}).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    for(let o of data.list) {
                            if(o.is_handling==0){
                                o.is_handling=false;
                            }
                            else if(o.is_handling==1){
                                o.is_handling=true;
                            }
                    }
                    commit('set_total', data.count);
                    commit('set_feedback_list', data.list);
                    resolve(data);
                }
                else reject(data)
            })
        })
    },
    updateMany:({commit, state},{idStr,s}) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/feedback/setStates',{id:idStr,is_handling:s}).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(data)
            })
        })
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
}