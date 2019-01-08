import Vue from "vue"
import Vuex from "vuex";
import user from "./modules/user"
import banner from "./modules/banner"
import banner_state from './modules/banner_state'
import module from './modules/module'
import article from './modules/article'
import config from './modules/config'
import hot_banner from './modules/hot_banner'
import company from './modules/company'
import category from './modules/category'
import live from './modules/live'
import category_company from './modules/category_company'
import template_generate from './modules/template'
import feedback from './modules/feedback'
import menu from './modules/menu'
import tools from './modules/tools'
import category_map from './modules/category_map'
import activity from './modules/activity'
import category_activity from './modules/category_activity'
import article_filter from './modules/article_filter'
import crawl_article from "./modules/crawl_article"
import article_need_look from "./modules/article_need_look"
import calendar from "./modules/calendar"
import event from "./modules/event"
import holiday from "./modules/holiday"
import block_coin from "./modules/block_coin"
import entry from "./modules/entry"
Vue.use(Vuex)
import actions from "./actions"
import mutations from "./mutations"
import getters from "./getters"


export default new Vuex.Store({
    modules: {
        user,
        banner,
        entry,
        banner_state,
        module,
        article,
        config,
        hot_banner,
        company,
        category,
        category_company,
        live,
        feedback,
        menu,
        tools,
        category_map,
        activity,
        category_activity,
        crawl_article,
        article_filter,
        article_need_look,
        calendar,
        event,
        block_coin,
        holiday,
        template: template_generate
    },
    state: {
        app_name: {},
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
        },
        formLabelWidth: '123px'
    },
    getters,
    mutations,
    actions
});