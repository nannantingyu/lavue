import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        country: {title: "国家", show: true},
        city: {title: "城市", show: true},
        importance: {title: "重要性", show: true},
        event: {title: "事件", show: true},
        date: {title: "日期", show: true},
        time: {title: "时间", show: true},
        source_id: {title: "source_id", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false}
    },
    lists: [],
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
    set_list: (state, list) => {
        state.lists = list
    },
    // set_feed_state:(state, r)=>{
    //     state.state = r
    // }
};

const actions = {
    add_update:({commit, state},form) => {
        return new Promise((resolve, reject) => {
            axios.post('/api/event/add',form).then(result=> {
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
            axios.get('/api/event/list', {
                params: {page:(state.currentPage-1),pageSize:state.pageSize,state:state.state}}).then(result=> {
                    console.log(result);
                if(result.data.success === 1) {
                    let data = result.data.data;
                    commit('set_total', data.count);
                    commit('set_list', data.list);
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