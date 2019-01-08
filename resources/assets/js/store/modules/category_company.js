import {check_integer_factory} from "../../plugin/tool";
import $ from 'jquery'
const state = {
    columns: {
        id: {title: "ID", show: true},
        name: {title: "分类名称", show: true},
        ename: {title: "英文名称", show: true},
        sequence: {title: "顺序", show: true},
        pid: {title: "父分类", show: true},
        state: {title: "状态", show: true},
        target: {title: "Target", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        name: null,
        sequence: '',
        state: 1,
        created_at: '',
        updated_at: ''
    },
    to_strings: [],
    to_booleans: ['state'],
    category_company_lists: [],
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    per_page: 10,
    dialog_visible: false,
    row_index: 0,
    rules: {
        name: [
            { required: true, message: '请输入分类名称', trigger: 'blur' },
            { min: 2, max: 20, message: '分类名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        ename: [
            { required: true, message: '请输入分类英文名称', trigger: 'blur' },
            { min: 2, max: 20, message: '分类英文名称长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        link: [
            { required: true, message: '请输入链接', trigger: 'blur' },
            { min: 2, max: 256, message: '链接长度在 2 到 256 个字符', trigger: 'blur' }
        ],
        target: [
            { required: true, message: '请选择Target', trigger: 'blur' },
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
    set_category_company_list: (state, category_company_lists) => {
        state.category_company_lists = category_company_lists;
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
            sequence: '',
            state: 1,
            created_at: '',
            updated_at: ''
        }
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.category_company_lists = state.back_data;
        else
            state.category_company_lists = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.category_company_lists.length;
    },
    update_category_company_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        state.category_company_lists[prop['index']][key] = value
    },
    append_category_company_list: (state, row) => {
        state.category_company_lists.splice(0, 0, row)
    },
};

const actions = {
    get_category_company_lists: ({commit, state}) => {
        return new Promise((resolve, reject) => {
            axios.get('/categoryCompanyLists').then(result=> {
                if(result.data.success === 1) {
                    let category_company_lists = result.data.data;
                    for(let obj of category_company_lists) {
                        for(let o in obj) {
                            if(state.to_strings.includes(o)) obj[o] = obj[o].toString()
                            else if(state.to_booleans.includes(o)) obj[o] = (obj[o] === 1 || !!obj[o])
                        }
                    }

                    commit('set_category_company_list', category_company_lists);
                    commit('set_back_data', category_company_lists)
                    resolve(category_company_lists)
                }
                else reject()
            })
        })
    },
    get_category_company: ({commit, state})=> {
        return new Promise((resolve, reject)=> {
            axios.get('/categoryCompanyInfo').then(result=> {
                if(result.data.success === 1) {
                    commit('set_form', result.data.data)
                }
            })
        })
    },
    set_category_company_state ({commit}, {id, state})  {
        return new Promise((resolve, reject)=> {
            state = state?1:0;

            axios.post('/setCategoryCompanyState', {id: id, state: state})
                .then(function(result) {
                    if(result.data.success === 1) resolve()
                    else reject()
                });
        })
    },
    add_or_update_category_company({ commit, state }, form) {
        return new Promise((resolve, reject) => {
            let subform = {};
            $.extend(subform, form);

            for(let key of state.to_booleans) {
                if(subform.hasOwnProperty(key)) {
                    subform[key] = subform[key]?1:0;
                }
            }

            axios.post("/addCategoryCompany", subform).then(function(result){
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