const validateRePassword = (rule, value, callback)=>{
    if(!value) callback(new Error('请再次输入密码'))
    else if(value !== state.regist.password) callback(new Error('两次输入的密码不一致'))
    else callback()
};

const validateEmail = (rule, value, callback)=>{
    if (!value) callback('邮箱地址不能为空')
    else if(/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/.test(value))
        callback()
    else callback(new Error("邮箱格式不正确"))
};

const validatePhone = (rule, value, callback)=>{
    if (!value) callback('手机号不能为空')
    else if(/^(13|14|15|17|18|19)\d{9}$/.test(value))
        callback()
    else callback(new Error("手机号不正确"))
};

const state = {
    user_rules: {
        username: [
            { required: true, message: '请输入用户名', trigger: 'blur' },
            { min: 3, max: 32, message: '用户名长度在 3 到 32 个字符', trigger: 'blur' }
        ],
        nickname: [
            { required: true, message: '请输入昵称', trigger: 'blur' },
            { min: 2, max: 32, message: '昵称长度在 2 到 32 个字符', trigger: 'blur' }
        ],
        password: [
            { required: true, message: '请输入密码', trigger: 'blur' },
            { min: 6, max: 32, message: '密码长度在 6 到 32 个字符', trigger: 'blur' }
        ],
        repassword: [
            { validator: validateRePassword, trigger: 'blur' }
        ],
        email: [
            { validator: validateEmail, trigger: 'blur' }
        ],
        phone: [
            { validator: validatePhone, trigger: 'blur' }
        ]
    },
    columns: {
        id: {title: "ID", show: true},
        name: {title: "用户名", show: true},
        nickname: {title: "昵称", show: true},
        email: {title: "邮箱", show: true},
        phone: {title: "电话", show: true},
        created_at: {title: "创建时间", show: false},
        updated_at: {title: "更新时间", show: false},
    },
    form: {
        id: null,
        name: null,
        nickname: null,
        email: '',
        phone: '',
        state: 1,
        created_at: '',
        updated_at: ''
    },
    pwd: '',
    role_rules: {
        role_name: [
            { required: true, message: '请输入角色', trigger: 'blur' },
            { min: 2, max: 32, message: '角色长度在 2 到 32 个字符', trigger: 'blur' }
        ]
    },
    formLabelWidth: '88px',
    regist: {
        id: null,
        name: '',
        nickname: '',
        password: '',
        repassword: '',
        email: '',
        phone: ''
    },
    role: {
        id: null,
        role_name: '',
        state: 1,
    },
    show_type: 3,
    loading: false,
    current_page: 1,
    total: 0,
    back_data: [],
    per_page: 10,
    dialog_visible: false,
    dialog_password_visible: false,
    roles: [],
    users: [],
    role_users: [],
    login_user: {
        user_id: null,
        nick_name: null
    },
    user_module_permission: {}
}

const mutations = {
    set_current_page: (state, current_page) => {
        state.current_page = current_page
    },
    set_total: (state, total) => {
        state.total = total
    },
    set_roles: (state, roles)=>{
        state.roles = roles
    },
    set_users: (state, users)=>{
        state.users = users
    },
    set_role_users: (state, role_users)=>{
        state.role_users = role_users
    },
    set_pwd: (state, pwd) => {
        state.pwd = pwd;
    },
    set_login_user: (state, {user_id, nick_name})=> {
        state.login_user.user_id = user_id
        state.login_user.nick_name = nick_name
        if(user_id && nick_name) {
            setCookie('userid', user_id)
            setCookie('nickname', nick_name)
        }
    },
    set_form_value: (state, {key, value})=> {
        state.form[key] = value
    },
    set_show_type: (state, show_type) => {
        state.show_type = show_type;
    },
    set_dialog_visible: (state, value) => {
        state.dialog_visible = value;
    },
    set_back_data: (state, back_data) => {
        state.back_data = back_data;
    },
    set_dialog_password_visible: (state, value) => {
        state.dialog_password_visible = value;
    },
    set_row_index: (state, row_index) => {
        state.row_index = row_index;
    },
    set_form: (state, form) => {
        for (let o in form) {
            if(state.form.hasOwnProperty(o)) {
                state.form[o] = form[o]
            }
        }
    },
    update_user_list_by_index: (state, prop) => {
        let value = prop['value'], key = prop['key'];

        state.users[prop['index']][key] = value
    },
    add_user_module_permission: (state, {path, values}) => {
        state.user_module_permission[path+'-read'] = ((values['permission'] & 1) === 1);
        state.user_module_permission[path+'-update'] = ((values['permission'] & 2) === 2);
        state.user_module_permission[path+'-delete'] = ((values['permission'] & 4) === 4);
    },
    clear_user_module_permission: (state) => {
        state.user_module_permission = {}
    },
    filte_data: (state) => {
        if(state.show_type == 3) state.users = state.back_data;
        else
            state.users = state.back_data.filter(x=>{
                return x.state == state.show_type;
            });

        state.total = state.users.length;
    },
    reset_token: (state, token)=> {
        document.head.querySelector('meta[name="csrf-token"]').content = token;
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    },
    set_loading: (state, loading) => {
        state.loading = loading;
    },
    sort_data: (state, {column, order})=> {
        state.users = state.back_data.sort((x, y)=> {
            if(order === 'ascending') return !isNaN(x[column])?x[column] - y[column] : x[column] > y[column]?1:-1;
            else return !isNaN(x[column])?y[column] - x[column] : y[column] > x[column]?1:-1;
        })
    }
}
const actions = {
    login ({commit, state, dispatch}, login) {
        console.log(2131231);
        return new Promise((resolve, reject)=>{
            axios.post('/adlogin', login)
                .then(result=>{
                    if(result.data.success === 1) {
                        commit('set_login_user', {
                            user_id: result.data.userid,
                            nick_name: result.data.nickname
                        });

                        commit('reset_token', result.data._token);
                        dispatch('get_user_module_permission').then(res=> {
                            resolve()
                        })
                    }
                    else {
                        reject(data.errors)
                    }
                })
                .catch(data => {
                    if(data.response.data.hasOwnProperty('data')) {
                        commit('reset_token', data.response.data.data._token);
                    }

                    reject(data.response.data.errors)
                })
        })
    },
    update_password({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.post('/setPassword', {userid: state.form.id, password: state.pwd}).then(result=> {
                if(result.data.success === 1) resolve()
                else reject()
            })
        })
    },
    set_user_state({commit, state}, {id, st}) {
        return new Promise((resolve, reject)=> {
            axios.post('/setState', {id: id, state: st}).then(result=> {
                if(result.data.success === 1) resolve()
                else reject()
            })
        })
    },
    add_or_update_user ({commit, state}, form) {
        return new Promise((resolve, reject)=> {
            axios.post('/addOrUpdateUser', form).then(result=> {
                if(result.data.success == 1) resolve()
                else reject()
            })
        })
    },
    logout ({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.post('/adlogout').then(result=> {
                if(result.data.success === 1) {
                    delCookie('userid');
                    delCookie('nickname');
                    commit('set_login_user', {
                        user_id: null,
                        nick_name: null
                    });

                    commit('clear_user_module_permission');
                    location.href = '/';
                    resolve()
                }
                else reject()
            })
        })
    },
    regist ({commit, state, dispatch}) {
        return new Promise((resolve, reject)=> {
            axios.post("/regist", state.regist).then(function(result){
                if(result.data.success === 1) {
                    resolve()
                }
                else reject(result.data.errors)
            });
        })
    },
    add_role ({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.post("/addRole", state.role).then(function(result){
                if(result.data.success === 1) resolve()
                else {
                    reject(result.data.msg)
                }
            });
        })
    },
    get_roles ({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.get('/getRoles').then(result=> {
               if(result.data.success === 1) {
                   commit('set_roles', result.data.data)
                   resolve(result.data.data)
               }
               else reject(result.data.msg)
            });
        })
    },
    get_users ({commit, state}) {
        return new Promise((resolve, reject)=> {
            axios.get('/getUsers').then(result=> {
                commit('set_loading', true);
                if(result.data.success === 1) {
                    commit('set_users', result.data.data);
                    commit('set_back_data', result.data.data);
                    commit('set_current_page', 1);
                    commit('set_total', result.data.data.length);
                    commit('set_loading', false);
                    resolve()
                }
                else reject(result.data.msg)
            });
        })
    },
    get_role_users ({commit, state}, role_id) {
        return new Promise((resolve, reject)=> {
            axios.get('/getRoleUsers?role_id='+role_id).then(result=> {
                if(result.data.success === 1) {
                    const users = []
                    result.data.data.forEach(x=>{
                        users.push(x['id'])
                    })

                    commit('set_role_users', users)
                    resolve()
                }
                else reject(result.data.msg)
            });
        })
    },
    assign_role_for_users ({commit, state}, {role_id, users}) {
        return new Promise((resolve, reject) => {
            axios.post('/assignRoleForUser', {
                users: users,
                role_id: role_id
            }).then(result=> {
                if(result.data.success == 1) resolve()
                else reject();
            });
        })
    },
    retract_role_for_users ({commit, state}, {role_id, users}) {
        return new Promise((resolve, reject) => {
            axios.post('/retractRoleFromUser', {
                users: users,
                role_id: role_id
            }).then(result=> {
                if(result.data.success == 1) resolve()
                else reject();
            });
        })
    },
    get_user_module_permission({commit, state}) {
        return new Promise((resolve, reject)=> {
            if(!state.login_user.user_id) {
                reject('用户未登录')
            }
            else
                axios.get('/getUserModulePermission?user_id='+state.login_user.user_id)
                    .then(result=> {
                        if(result.data.success == 1) {
                            for(let path in result.data.data) {
                                commit('add_user_module_permission', {
                                    path: path,
                                    values: result.data.data[path]
                                })
                            }

                            resolve(result.data.data)
                        }
                        else
                            reject(result.data.msg)
                    })
        })
    },
}

export default {
    namespaced: true,
    state: state,
    mutations: mutations,
    actions: actions
}