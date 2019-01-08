
const state = {
    columns: {
        id: {title: "ID", show: true},
        time: {title: "更新时间", show: true},
        site: {title: "源站", show: true},
        state: {title: "当前状态", show: true}
    },
    lists: [],
    lists_all: [],
    pageSize:10,
    currentPage:1,
    total:0,
    state:-1,
    //config表中数据
    configData:{},
    configValue:"[]",
    allData:[]
};

const mutations = {
    set_allData: (state, arr) => {
        state.allData = arr;
    },
    set_configData: (state, obj) => {
        state.configData=obj;
    },
    set_config: (state, arr) => {
        state.configValue = arr;
    },
    set_list: (state, list) => {
        state.lists = list;
    },
    set_lists_all: (state, list) => {
        state.lists_all = list;
    },
    filter_data: (state, r) => {
        // 0:全部
        // 1:自动审核
        // 2:人工审核
        if(r==0){
            state.lists = state.lists_all;
        }
        else if(r==1){
            let arr=[];
            state.lists_all.map((item)=>{
                if(!item.state){
                    arr.push(item);
                }
            })
            state.lists = arr;
        }
        else if(r==2){
            let arr=[];
            state.lists_all.map((item)=>{
                if(item.state){
                    arr.push(item);
                }
            })
            state.lists = arr;
        }
    }
};
const actions = {
    filterData:({commit, state})=>{
        let arr = JSON.parse(JSON.stringify(state.configValue));
        let all = JSON.parse(JSON.stringify(state.allData))
        let r=[];

        arr.map((item)=>{
            all.map((a)=>{
                if(item.site==a.source_site){
                    a.state=true;
                    a.time=item.time;
                }
            })

        })
        for(let o of all) {
            if(!o.state){
                o.state=false;
            }
            if(o.source_site){
                r.push(o)
            }
        }
        // console.log(r);
        commit('set_list', r);
        commit('set_lists_all', r);
    },
    change_config:({commit, state,dispatch},val) => {
        let arr=JSON.stringify(val);
        return new Promise((resolve, reject) => {
            axios.post('/addConfig',{
                id:state.configData.id,
                key:'manualCheck',
                value:arr,
                group:'jujin8',
                sequence:state.configData.sequence,
                state:state.configData.state
            }).then(result=> {
                if(result.data.success === 1) {
                    let data = result.data.data;
                    commit('set_config', val);
                    dispatch('filterData');
                    resolve(data);
                }
                else {
                    reject(data)
                }
            })
        })
    },
    get_lists: ({commit, state,dispatch}) => {
        //获取 网站审核数据 并组装
        return new Promise((resolve, reject) => {
            axios.get('/articleSource').then(result=> {
                if(result.data.success === 1) {
                    let allData = result.data.data;
                    commit('set_allData', allData);
                    axios.get('/configInfo',{params: { 'key': 'manualCheck' }}).then(r=> {
                        if(r.data.success === 1) {
                            let configData = r.data.data;
                            let arr=[];
                            if(configData){
                                if(typeof configData.value == 'string'){
                                    arr=JSON.parse(configData.value);
                                }
                                else{
                                    arr=configData.value;
                                }
                                commit('set_config', arr);
                                commit('set_configData', configData);
                                dispatch('filterData');

                                resolve();
                            }
                            // console.log(r.data.data,"kkhhh")
                        }

                    })
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