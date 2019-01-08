export default {
    namespaced: true,
    state: {
        rules: {
            name: [
                { required: true, message: '请输入模块名', trigger: 'blur' },
                { min: 2, max: 32, message: '模块名长度在 3 到 32 个字符', trigger: 'blur' }
            ],
            path: [
                { required: true, message: '请输入模块路径', trigger: 'blur' },
                { min: 2, max: 32, message: '模块路径在 3 到 64 个字符', trigger: 'blur' }
            ],
            sequence: [
                { required: true, message: '请输入顺序', trigger: 'blur' },
            ]
        },
        modules: [],
        form: {
            id: null,
            name: '',
            path: '',
            state: 1,
            display: 1,
            sequence: 1,
            pid: ''
        },
        dialog_visible: false,
        active_module: {},
        module_info: {},
        parent_label: "",
        module_option: []
    },
    mutations: {
        set_modules: (state, modules) => {
            state.modules = modules;
        },
        set_parent_label: (state, parent_label) => {
            state.parent_label = parent_label;
        },
        set_dialog_visible: (state, dialog_visible) => {
            state.dialog_visible = dialog_visible
        },
        set_active_module_val: (state, {prop, val}) => {
            state.active_module[prop] = val
        },
        set_active_module: (state, module) => {
            state.active_module = module
        },
        add_new_node_to_active: (state, child) => {
            state.active_module.children.push(child)
        },
        set_form_val: (state, {prop, val}) => {
            if(state.form.hasOwnProperty(prop))
                state.form[prop] = val
        },
        set_module_info: (state, {id, val}) => {
            state.module_info[id] = val
        },
        set_module_option: (state, module_option) => {
            state.module_option = module_option
        },
        set_default_form: (state) => {
            state.form = {
                id: null,
                name: '',
                path: '',
                state: 1,
                display: 1,
                sequence: 1,
                pid: ''
            }
        },
    },
    actions:{
        get_modules({commit, state}) {
            return new Promise((resolve, reject)=>{
                axios.get('/getModule').then(result=> {
                    if(result.data.success === 1) {
                        commit('set_modules', result.data.data)
                        for(let o of result.data.list) {
                            commit('set_module_info', {id: o.id, val: o})
                        }

                        resolve(result.data.data)
                    }
                    else reject()
                })
            })
        },
        add_or_update_module({commit, state}, form=state.form) {
            return new Promise((resolve, reject)=> {
                axios.post('/addOrUpdateModule', form)
                    .then(result=> {
                        if(result.data.success === 1) {
                            resolve(result.data)
                        }
                        else reject()
                    })
            })
        },
        get_module_tree_select({commit, state}) {
            return new Promise((resolve, reject)=> {
                axios.get('/getModuleTreeSelect').then(result=> {
                    if(result.data.success === 1) {
                        resolve(result)
                        commit('set_module_option', result.data.data)
                    }
                    else reject()
                })
            });
        },
        get_module_permission({commit, state}, id) {
            return new Promise((resolve, reject)=> {
                axios.get('/getModulePermission?id='+id).then(result=>{
                    if(result.data.success === 1) {
                        resolve(result.data.data)
                    }
                    else reject(result.data.msg)
                });
            })
        },
        add_module_permission({commit, state}, form) {
            return new Promise((resolve, reject)=> {
                axios.post('/addModulePermission', form).then(result=> {
                    if(result.data.success === 1) {
                        resolve(result)
                    }
                    else reject(result.data.errors)
                })
            })
        }
    }
}