import {getCookie} from "./plugin/cookie";

require('./bootstrap');
window.Vue = require('vue');

import Vuex from 'vuex'
import router from './router'
import App from './components/container'

import {Container, Header, Aside, Main, Footer, Row, Col, Message, Button, Switch, Menu, MenuItemGroup, MenuItem, Submenu, Scrollbar} from 'element-ui'
Vue.use(Container)
Vue.use(Header)
Vue.use(Aside)
Vue.use(Main)
Vue.use(Footer)
Vue.use(Switch)
Vue.use(Menu)
Vue.use(MenuItemGroup)
Vue.use(MenuItem)
Vue.use(Submenu)
Vue.use(Button)
Vue.use(Row)
Vue.use(Col)
Vue.use(Vuex)
Vue.prototype.$message = Message;

import 'element-ui/lib/theme-chalk/index.css'
import store from './store'
import './plugin/tool'

//权限控制
import permissionDirective from './components/directives'
Vue.directive('hasReadPermissionHide', permissionDirective.hasReadPermissionHide)
Vue.directive('hasReadPermissionDisable', permissionDirective.hasReadPermissionDisable)
Vue.directive('hasUpdatePermissionHide', permissionDirective.hasUpdatePermissionHide)
Vue.directive('hasUpdatePermissionDisable', permissionDirective.hasUpdatePermissionDisable)
Vue.directive('hasDeletePermissionHide', permissionDirective.hasDeletePermissionHide)
Vue.directive('hasDeletePermissionDisable', permissionDirective.hasDeletePermissionDisable)

async function hasAccessPermission (path) {
    return new Promise(resolve=> {
        if(Object.keys(store.state.user.user_module_permission).length === 0) {
            store.commit('user/set_login_user', {
                user_id: getCookie('userid'),
                nick_name: getCookie('nickname')
            })

            store.dispatch('user/get_user_module_permission').then(()=>{
                resolve(store.state.user.user_module_permission[path+'-read'])
            }).catch(()=> {
                router.push({path: '/login'})
            })
        }
        else {
            resolve(store.state.user.user_module_permission[path+'-read'])
        }
    });
}

router.beforeEach((to, from, next) => {
    const userid = getCookie('userid'), to_path = to.fullPath.trim_char('/').split('/')[0];
    if(to_path === 'login' || to_path === 'regist') next()
    else {
        if(!userid) {
            next({path: "/login", replace: true})
        }
        else{
            hasAccessPermission(to_path).then(permission=> {
                if(to_path === 'index') next()
                else if(!permission) {
                    Message.error('没有权限进入' + to.fullPath + '页面')
                    next({path: '/index', replace: true})
                }
                else next()
            })
        }
    }
});

const app = new Vue({
    el: '#app',
    router,
    store,
    render: h=>h(App)
});
