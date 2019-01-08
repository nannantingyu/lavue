import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'

const state = {
    columns: {
        id: {title: "ID", show: true},
        name: {title: "机构名称", show: true},
        shortname: {title: "缩略名称", show: true},
        sequence: {title: "顺序", show: true},
        link: {title: "链接", show: true},
        state: {title: "状态", show: true},
        tag: {title: "标签", show: true},
        logo: {title: "Logo", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        name: null,
        shortname: null,
        link: '',
        sequence: '',
        state: 1,
        tag: '',
        logo: '',
        categories: [],
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    company_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        name: [
            { required: true, message: '请输入机构名称', trigger: 'blur' },
            { min: 2, max: 32, message: '机构名称长度在 2 到 32 个字符', trigger: 'blur' }
        ],
        shortname: [
            { required: true, message: '请输入缩略名称', trigger: 'blur' },
            { min: 2, max: 16, message: '机构名称长度在 2 到 16 个字符', trigger: 'blur' }
        ],
        link: [
            { required: true, message: '请输入链接', trigger: 'blur' },
            { min: 2, max: 256, message: '链接长度在 2 到 256 个字符', trigger: 'blur' }
        ],
        tag: [
            { required: true, message: '请输入标签', trigger: 'blur' },
            { min: 2, max: 64, message: '标签长度在 2 到 64 个字符', trigger: 'blur' }
        ],
        logo: [
            { required: true, message: '请输入Logo', trigger: 'blur' },
            { min: 2, max: 256, message: 'Logo长度在 2 到 256 个字符', trigger: 'blur' }
        ],
        sequence: [
            { validator: check_integer_factory('顺序为数字类型'), trigger: 'blur' }
        ],
    },
};
const mutations = {
    set_current_page: (state, current_page) => {
        state.current_page = current_page
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
    set_company_list: (state, company_lists) => {
        state.company_lists = company_lists;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_row_index: (state, row_index) => {
        state.row_index = row_index;
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    },
    set_dialog_visible: (state, value) => {
        state.dialog_visible = value;
    },
    set_form: (state, form) => {
        for (let o in form) {
            if(state.form.hasOwnProperty(o)) {
                state.form[o] = form[o]
            }
        }
    },
    set_default_form: (state) => {
        state.form = {
            id: null,
            name: null,
            shortname: null,
            link: '',
            sequence: '',
            state: 1,
            categories: [],
            tag: '',
            logo: '',
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.company_lists = state.back_data;
        else
            state.company_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.company_lists.length;
    },
    update_company_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        if(state.to_booleans.includes(key))
            value = value?1:0

        state.company_lists[prop['index']][key] = value
    },
    append_company_list: (state, row) => {
        state.company_lists.splice(0, 0, row)
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.company_lists = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
};
const actions = {
    get_company_lists: ({commit, state}) => {
        commit('set_loading', true);
        return new Promise((resolve, reject) => {
            axios.get('/companyLists').then(result=> {
                if(result.data.success === 1) {
                    let company_lists = result.data.data;
                    for(let obj of company_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])

                            if(o === 'categories') {
                                obj[o] = obj[o].map(x=>{
                                    return x.id;
                                })
                            }
                        }
                    }

                    console.log(company_lists)
                    commit('set_company_list', company_lists);
                    commit('set_back_data', company_lists);
                    commit('set_current_page', 1);
                    commit('set_total', company_lists.length);
                    commit('set_loading', false);
                    resolve()
                }
                else reject()
            })
        })
    },
    get_company: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/companyInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    set_company_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setCompanyState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_company({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addCompany", subform).then(function(result){
                if(result.data.success === 1) resolve(result.data.data)
                else reject()
            });
        });
    }
};
const getters = {
    fileimgs: state=> {
        let imgs = [];
        if(state.form.logo)
            imgs.push({url: 'http://images.jujin8.com'+state.form.logo.replace('/uploads/crawler', '/uploads')});

        return imgs;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}