import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        country: {title: "国家", show: true},
        quota_name: {title: "名称", show: true},
        importance: {title: "重要性", show: true},
        former_value: {title: "前值", show: false},
        predicted_value: {title: "预测值", show: false},
        published_value: {title: "发布值", show: false},
        influence: {title: "影响", show: true},
        source_id: {title: "source_id", show: true},
        dataname: {title: "数据名称", show: true},
        datename: {title: "月份", show: true},
        dataname_id: {title: "dataname_id", show: true},
        unit: {title: "单位", show: false},
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
            axios.post('/api/calendar/add',form).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    resolve(data);
                }
                else reject(result.data)
            })
        })
    },
    // set_feed_state({commit}, row)  {
    //     return new Promise((resolve, reject)=> {
    //         axios.post('/api/addFeedback', row)
    //             .then(function(result) {
    //                 if(result.data.success === 1){
    //                     resolve()
    //                 }
    //                 else reject()
    //             });
    //     })
    // },
    get_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/api/calendar/list', {
                params: {page:(state.currentPage-1),pageSize:state.pageSize,state:state.state}}).then(result=> {
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