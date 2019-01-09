import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

export default new VueRouter({
    routes: [
        {
            name: 'index',
            path: '/index',
            component: resolve=>void(require(['./page/index.vue'], resolve))
        },
        {
            name: 'login',
            path: '/login',
            component: resolve=>void(require(['./page/login.vue'], resolve))
        },
        {
            name: 'user',
            path: '/user',
            component: resolve=>void(require(['./page/user.vue'], resolve))
        },
        {
            name: 'add-user',
            path: '/add-user',
            component: resolve=>void(require(['./page/add-user.vue'], resolve))
        },
        {
            name: 'module',
            path: '/module',
            component: resolve=>void(require(['./page/module.vue'], resolve))
        },
        {
            name: 'add-module',
            path: '/add-module',
            component: resolve=>void(require(['./page/add-module.vue'], resolve))
        },
        {
            name: 'add-role',
            path: '/add-role',
            component: resolve=>void(require(['./page/add-role.vue'], resolve))
        },
        {
            name: 'role-user',
            path: '/role-user',
            component: resolve=>void(require(['./page/role-user.vue'], resolve))
        },
        {
            name: 'permission',
            path: '/permission',
            component: resolve=>void(require(['./page/permission.vue'], resolve))
        },
        {
            name: 'article',
            path: '/article',
            component: resolve=>void(require(['./page/article.vue'], resolve))
        },
        {
            name: "article-edit",
            path: '/article-edit/:id?',
            component: resolve =>void(require(['./page/add-article.vue'], resolve))
        },
        {
            name: "article-edit-original",
            path: '/article-edit-original/:id?',
            component: resolve =>void(require(['./page/article-edit-original.vue'], resolve))
        },
        {
            name: "config",
            path: '/config',
            component: resolve =>void(require(['./page/config.vue'], resolve))
        },
        {
            name: "hot-banner",
            path: '/hot-banner',
            component: resolve =>void(require(['./page/hot-banner.vue'], resolve))
        },
        {
            name: "company",
            path: '/company',
            component: resolve =>void(require(['./page/company.vue'], resolve))
        },
        {
            name: "category",
            path: '/category',
            component: resolve =>void(require(['./page/category.vue'], resolve))
        },
        {
            name: "category-company",
            path: '/category-company',
            component: resolve =>void(require(['./page/category-company.vue'], resolve))
        },
        {
            name: "live",
            path: '/live',
            component: resolve =>void(require(['./page/live.vue'], resolve))
        },
        {
            name: "feedback",
            path: '/feedback',
            component: resolve =>void(require(['./page/feedback.vue'], resolve))
        },
        {
            name: "main-menu",
            path: '/main-menu',
            component: resolve =>void(require(['./page/main-menu.vue'], resolve))
        },
            {
            name: "template",
            path: '/template',
            component: resolve =>void(require(['./page/template.vue'], resolve))
        },
        {
            name: "tools",
            path: '/tools',
            component: resolve =>void(require(['./page/tools.vue'], resolve))
        },
        {
            name: "category-map",
            path: '/category-map',
            component: resolve =>void(require(['./page/category-map.vue'], resolve))
        },
        {
            name: "activity",
            path: '/activity',
            component: resolve =>void(require(['./page/activity.vue'], resolve))
        },
        {
            name: "calendar",
            path: '/calendar',
            component: resolve =>void(require(['./page/calendar.vue'], resolve))
        },
        {
            name: "event",
            path: '/event',
            component: resolve =>void(require(['./page/event.vue'], resolve))
        },
        {
            name: "holiday",
            path: '/holiday',
            component: resolve =>void(require(['./page/holiday.vue'], resolve))
        },
        {
            name: "category-activity",
            path: '/category-activity',
            component: resolve =>void(require(['./page/category-activity.vue'], resolve))
        },
        {
            name: "article-filter",
            path: '/article-filter',
            component: resolve =>void(require(['./page/article-filter.vue'], resolve))
        },
        {
            name: "article-need-look",
            path: '/article-need-look',
            component: resolve =>void(require(['./page/article-need-look.vue'], resolve))
        },
        {
            name: "crawl-article",
            path: '/crawl-article',
            component: resolve =>void(require(['./page/crawl-article.vue'], resolve))
        },
        {
            name: "block-coin",
            path: '/block-coin',
            component: resolve =>void(require(['./page/block-coin.vue'], resolve))
        },
        {
            name: "entry",
            path: '/entry',
            component: resolve =>void(require(['./page/entry.vue'], resolve))
        }
    ]
})