<template>
    <div>
        <el-row>
            <el-col :span="2">
                <el-popover
                        placement="right"
                        width="400"
                        trigger="click">
                    <ul class="switch-panel">
                        <li v-for="(i, k) in columns">
                            <el-switch
                                    v-model="i.show"
                                    :active-text="i.title">
                            </el-switch>
                        </li>
                    </ul>
                    <el-button slot="reference">显示隐藏列</el-button>
                </el-popover>
            </el-col>
            <el-col :span="6">
                <el-radio-group v-model="show_type" @change="get_article_list">
                    <el-radio-button label="3">全部</el-radio-button>
                    <el-radio-button label="1">上线</el-radio-button>
                    <el-radio-button label="0">下线</el-radio-button>
                    <el-radio-button label="-1">待审核</el-radio-button>
                </el-radio-group>
            </el-col>
            <el-col :span="6">
                <el-popover
                    placement="right"
                    width="600"
                    trigger="click">
                    <el-row class="padding-row-15">
                        <el-input v-model="search_key" placeholder="请输入搜索关键词" @change="get_article_list"></el-input>
                    </el-row>
                    <el-row class="padding-row-15">
                        <el-select v-model="sites" multiple placeholder="请选择来源站点" @change="get_article_list">
                            <el-option
                                v-for="item in source_sites"
                                :key="item.site"
                                :label="item.site"
                                :value="item.site">
                            </el-option>
                        </el-select>
                    </el-row>
                    <el-row class="padding-row-15">
                        <el-select v-model="category" multiple placeholder="请选择分类" @change="get_article_list">
                            <el-option-group
                                v-for="group in article_categories_group"
                                :key="group.label"
                                :label="group.label">
                                <el-option
                                    v-for="item in group.data"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                </el-option>
                            </el-option-group>
                        </el-select>
                    </el-row>
                    <el-row class="padding-row-15">
                        <el-col :span="8">
                            <el-select v-model="time_key" placeholder="请选择分类" @change="changeDate">
                                <el-option key="publish_time" label="发布时间" value="publish_time"></el-option>
                                <el-option key="created_time" label="创建时间" value="created_time"></el-option>
                                <el-option key="updated_time" label="更新时间" value="updated_time"></el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="8">
                            <el-date-picker type="datetime" placeholder="选择开始时间"
                                            v-model="st"
                                            @change="changeDate"
                                            format="yyyy-MM-dd HH:mm:ss"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                            style="width: 100%;"></el-date-picker>
                        </el-col>
                        <el-col :span="8">
                            <el-date-picker type="datetime" placeholder="选择结束时间"
                                            v-model="et"
                                            @change="changeDate"
                                            format="yyyy-MM-dd HH:mm:ss"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                            style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-row>
                    <el-button type="success" icon="el-icon-search" circle slot="reference"></el-button>
                </el-popover>
                <el-button icon="el-icon-refresh" type="success" circle title="刷新当前筛选条件" @click="get_article_list"></el-button>
                <el-button type="success" @click="clear_filter" title="清空筛选条件">重置筛选</el-button>
                <el-popover
                    placement="right"
                    width="400"
                    trigger="click">
                    <el-row class="padding-row-15">
                        <el-switch
                            v-model="is_selected"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                            active-text="选中的"
                            inactive-text="全部的">
                        </el-switch>
                    </el-row>
                    <el-row class="padding-row-15">
                        <el-button @click="multiOffline">批量下线</el-button>
                        <el-button @click="multiOnline">批量上线</el-button>
                        <el-button @click="multiCheck">批量审核</el-button>
                        <!--<el-button @click="multiDelete">批量删除</el-button>-->
                    </el-row>
                    <el-button type="warning" slot="reference">批量操作</el-button>
                </el-popover>
            </el-col>
            <el-col :offset="6" :span="2">
                <el-button type="danger" @click="dialog_visible_list=true">查看来源</el-button>
            </el-col>
        </el-row>
        <el-table :data="article_lists"
                  v-loading="loading"
                  @selection-change="changeSelected"
                  @sort-change="changeTableSort"
                  style="width: 100%">
            <el-table-column
                type="selection"
                width="55">
            </el-table-column>
            <el-table-column
                    prop="id"
                    sortable
                    :label="columns['id']['title']"
                    v-if="columns['id']['show']"
                    width="80">
            </el-table-column>
            <el-table-column
                :label="columns['title']['title']"
                v-if="columns['title']['show']" width="*">
                <template slot-scope="scope">
                    <a target="_blank" v-bind:href="'http://www.jujin8.com/read/'+scope.row.id+'.html'">{{ scope.row.title }}</a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="description"
                    :label="columns['description']['title']"
                    v-if="columns['description']['show']"
                    width="250">
            </el-table-column>
            <el-table-column
                    prop="keywords"
                    :label="columns['keywords']['title']"
                    v-if="columns['keywords']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="image"
                    :label="columns['image']['title']"
                    v-if="columns['image']['show']"
                    width="160">
                <template slot-scope="scope">
                    <img width="140" :src="transfer(scope.row.image)"
                         @error="image_load_error(scope.$index)"
                         @click="redownload(scope.$index)" :alt="scope.row.image_alt">
                </template>
            </el-table-column>
            <el-table-column
                    prop="type"
                    :label="columns['type']['title']"
                    v-if="columns['type']['show']"
                    :filters="type_filter"
                    :filter-method="filterHandler"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="hits"
                    sortable
                    :label="columns['hits']['title']"
                    v-if="columns['hits']['show']"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="url"
                    :label="columns['url']['title']"
                    v-if="columns['url']['show']"
                    width="140">
                <template slot-scope="scope">
                    <a target="_blank" v-bind:href="scope.row.url">{{ scope.row.url }}</a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="state"
                    sortable
                    :label="columns['state']['title']"
                    v-if="columns['state']['show']"
                    width="80">
                <template slot-scope="scope">
                    <el-switch
                            :disabled="!user_module_permission['article-update']"
                            v-model="scope.row.state"
                            @change="changeState(scope.$index, scope.row)"></el-switch>
                </template>
            </el-table-column>
            <el-table-column
                    prop="favor"
                    sortable
                    :label="columns['favor']['title']"
                    v-if="columns['favor']['show']"
                    width="110">
            </el-table-column>
            <el-table-column
                    prop="source_type"
                    :label="columns['source_type']['title']"
                    v-if="columns['source_type']['show']"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="source_url"
                    :label="columns['source_url']['title']"
                    v-if="columns['source_url']['show']"
                    width="80">
                <template slot-scope="scope">
                    <a target="_blank" v-bind:href="scope.row.source_url">来源</a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="source_site"
                    :label="columns['source_site']['title']"
                    v-if="columns['source_site']['show']"
                    width="120">
            </el-table-column>
            <el-table-column prop="author"
                             :label="columns['author']['title']"
                             v-if="columns['author']['show']" width="180">
            </el-table-column>
            <el-table-column
                prop="publish_time"
                sortable
                label="发布时间"
                width="180">
            </el-table-column>
            <el-table-column
                prop="created_time"
                sortable
                :label="columns['created_time']['title']"
                v-if="columns['created_time']['show']"
                width="160">
            </el-table-column>
            <el-table-column
                prop="updated_time"
                sortable
                :label="columns['updated_time']['title']"
                v-if="columns['updated_time']['show']"
                width="160">
            </el-table-column>
            <el-table-column label="操作" fixed="right" width="250">
                <template slot-scope="scope">
                    <router-link class="router-button router-button-mini" :to="'/article-edit/'+scope.row.id">编辑</router-link>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['article-delete']"
                            @click="drop_article(scope.$index, scope.row)">删除</el-button>
                    <el-button
                        size="mini"
                        v-if="scope.row.need_check"
                        :type="scope.row.state?'success':'danger'"
                        :disabled="!user_module_permission['article-delete']"
                        @click="changeState(scope.$index, scope.row)">审核</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                background
                @current-change="page_change"
                @size-change="size_change"
                :current-page="current_page"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
        </el-pagination>
        <el-dialog title="来源审核" :visible.sync="dialog_visible_list">
            <el-table :data="source_sites"
                      v-loading="loading"
                      max-height="300"
                      @selection-change="changeSelected"
                      @sort-change="changeTableSort"
                      style="width: 100%">
                <el-table-column
                    prop="site"
                    label="来源网站"
                    width="*">
                </el-table-column>
                <el-table-column
                    prop="state"
                    sortable
                    label="状态"
                    width="120">
                    <template slot-scope="scope">
                        <el-switch
                            :disabled="!user_module_permission['config-delete']"
                            @change="change_source_site_state(scope.$index, scope.row)"
                            v-model="scope.row.state"></el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="操作" fixed="right" width="250">
                    <template slot-scope="scope">
                        <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['config-delete']"
                            @click="change_source_site(scope.$index, scope.row)">编辑</el-button>
                        <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['config-delete']"
                            @click="remove_source_site(scope.row.site)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <el-row class="padding-row-top-40">
                <el-col :span="8">
                    <el-input @change="search_source_site" @keyup.enter.native="search_source_site" v-model="search_source_site_key" placeholder="请输入搜索关键词"></el-input>
                </el-col>
                <el-col :offset="10" :span="4">
                    <el-button @click="dialog_visible_add=true">添加来源网站</el-button>
                </el-col>
            </el-row>
        </el-dialog>
        <el-dialog title="来源审核" :visible.sync="dialog_visible_add">
            <el-form ref="site_form" :model="site_form" :rules="site_rules">
                <el-form-item label="来源网站" prop="site">
                    <el-input v-model="site_form.site"></el-input>
                </el-form-item>
                <el-form-item label="状态" prop="image">
                    <el-switch v-model="site_form.state">启用</el-switch>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="submitSiteForm()">添加</el-button>
                    </el-col>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'
    import {Table, TableColumn, Pagination, MessageBox, Form, FormItem, Dialog, OptionGroup, Loading, DatePicker, Popover, Switch, RadioGroup, RadioButton, Input, Select, Option} from 'element-ui'
    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Pagination);
    Vue.use(Loading);
    Vue.use(Popover);
    Vue.use(Switch);
    Vue.use(RadioGroup);
    Vue.use(RadioButton);
    Vue.use(Input);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(DatePicker);
    Vue.use(Form);
    Vue.use(FormItem);
    Vue.use(Dialog);
    Vue.use(OptionGroup);

    export default {
        name: "article-list",
        created() {
            const _this = this;
            if(this.article_categories.length <= 0) {
                this.$store.dispatch("category/get_category_lists").then(result=> {
                    _this.set_article_categories(result);
                    _this.get_article_list();
                })
            }
            else {
                _this.get_article_list()
            }

            if(this.source_sites.length === 0) {
                this.$store.dispatch("config/get_config", {key: 'article_source_site'}).then(result=>{
                    if(result.data.success === 1) {
                        _this.set_source_sites({source_sites: result.data.data.value, is_bak: true});
                    }
                });
            }
        },
        computed: {
            ...mapState({
                article_lists: state=>state.article.article_lists,
                source_sites: state=>state.article.source_sites,
                source_sites_bak: state=>state.article.source_sites_bak,
                loading: state=>state.article.loading,
                total_page: state=>state.article.total_page,
                current_page: state=>state.article.current_page,
                per_page: state=>state.article.per_page,
                total: state=>state.article.total,
                columns: state=>state.article.columns,
                selected: state=>state.article.selected,
                user_module_permission: state=>state.user.user_module_permission,
                article_categories: state=>state.article.article_categories,
                article_categories_group: state=>state.article.article_categories_group,
                site_form: state=>state.article.site_form,
                site_rules: state=>state.article.site_rules,
                redownload_file_lists: state=>state.article.redownload_file_lists,
            }),
            ...mapGetters({
                'type_filter': 'article/type_filter'
            }),
            show_type: {
                get() {
                    return this.$store.state.article.show_type
                },
                set(value) {
                    this.$store.commit('article/set_show_type', value)
                }
            },
            dialog_visible_list: {
                get() {
                    return this.$store.state.article.dialog_visible_list
                },
                set(value) {
                    this.$store.commit('article/set_dialog_visible_list', value)
                }
            },
            dialog_visible_add: {
                get() {
                    return this.$store.state.article.dialog_visible_add
                },
                set(value) {
                    this.$store.commit('article/set_dialog_visible_add', value)
                }
            },
            is_selected: {
                get() {
                    return this.$store.state.article.is_selected
                },
                set(value) {
                    this.$store.commit('article/set_is_selected', value)
                    this.filte_data()
                }
            },
            sites: {
                get() {
                    return this.$store.state.article.sites
                },
                set(value) {
                    this.$store.commit('article/set_sites', value)
                    this.filte_data()
                }
            },
            category: {
                get() {
                    return this.$store.state.article.category
                },
                set(value) {
                    this.$store.commit('article/set_category', value)
                    this.filte_data()
                }
            },
            search_key: {
                get() {
                    return this.$store.state.article.search_key
                },
                set(value) {
                    this.$store.commit('article/set_search_key', value)
                    this.filte_data()
                }
            },
            time_key: {
                get() {
                    return this.$store.state.article.time_key
                },
                set(value) {
                    this.$store.commit('article/set_time_key', value)
                    this.filte_data()
                }
            },
            st: {
                get() {
                    return this.$store.state.article.st
                },
                set(value) {
                    this.$store.commit('article/set_st', value)
                    this.filte_data()
                }
            },
            et: {
                get() {
                    return this.$store.state.article.et
                },
                set(value) {
                    this.$store.commit('article/set_et', value)
                    this.filte_data()
                }
            },
            search_source_site_key: {
                get() {
                    return this.$store.state.article.search_source_site_key
                },
                set(value) {
                    this.$store.commit('article/set_search_source_site_key', value)
                }
            },
        },
        methods: {
            ...mapMutations({
                set_current_page: "article/set_current_page",
                set_source_sites: "article/set_source_sites",
                set_per_page: "article/set_per_page",
                filte_data: "article/filte_data",
                set_article_categories: "article/set_article_categories",
                set_search_key: "article/set_search_key",
                filte_search: "article/filte_search",
                set_order_by: "article/set_order_by",
                set_order: "article/set_order",
                set_time_key: "article/set_time_key",
                set_st: "article/set_st",
                set_et: "article/set_et",
                filter_by_category: "article/filter_by_category",
                clear_filter_options: "article/clear_filter_options"
            }),
            ...mapActions({
                set_article_state: "article/set_article_state",
                delete_article: "article/delete_article",
                add_or_update_source_site: "article/add_or_update_source_site"
            }),
            image_load_error: function(index) {
                this.$store.commit('article/update_article_list_by_index',  {index: index, key: 'image_alt', val: "图片加载失败，点击可重新下载"})
                this.$store.commit('article/update_article_list_by_index',  {index: index, key: 'image_exist', val: false});
            },
            redownload: function(index) {
                const this_file = this.article_lists[index]['image'], _this = this, exist = this.article_lists[index]['image_exist'];
                if(!this_file) {
                    this.$message.error("该图片为空，无法下载，可在编辑中自行添加！");
                    return;
                }

                if(exist) {
                    this.$message.error("图片已存在，请勿重复下载！");
                    return;
                }

                if(this.redownload_file_lists.includes(this_file)) {
                    this.$message.info("你已经添加该下载任务了，无需重新添加！请等待图片下载")
                }
                else this.$store.dispatch('article/redownload_img', this_file).then(x=>{
                    _this.$message.success("成功添加下载任务");
                }).catch(x=>{
                    _this.$message.error("未能添加下载任务，图片原地址不存在");
                });
            },
            search_source_site: function(value) {
                this.set_source_sites({source_sites: this.source_sites, is_bak: false});
            },
            get_article_list: function() {
                const _this = this;
                this.$store.dispatch('article/get_data').then(data => {
                    _this.$message.success("成功获取文章列表")
                })
            },
            change_source_site: function(index, row) {
                const site = row['site'];
                this.$store.commit('article/set_site_form_value', {key: 'site', value: site});
                this.$store.commit('article/set_site_form_value', {key: 'state', value: row['state']});
                this.$store.commit('article/set_site_form_value', {key: 'old_site', value: site});
                this.dialog_visible_add = true;
            },
            change_source_site_state: function(index, row) {
                const _this = this;
                const site = row['site'];
                this.$store.commit('article/set_site_form_value', {key: 'site', value: site});
                this.$store.commit('article/set_site_form_value', {key: 'state', value: row['state']});
                this.$store.commit('article/set_site_form_value', {key: 'old_site', value: site});

                this.$store.dispatch('article/add_or_update_source_site').then(x=>{
                    _this.$store.commit('article/set_default_site_form');
                    _this.$message.success("修改成功");
                });
            },
            remove_source_site: function(site) {
                const _this = this;
                this.$store.dispatch('article/remove_source_site', site).then(result=> {
                    _this.$message.success('删除成功');
                });
            },
            changeState: function(index, row) {
                const state = row.need_check?1:row.state?1:0;
                const new_state = state, _this = this, msg = row.need_check?'审核':new_state == 0?"下线":"上线";
                this.set_article_state({id: row.id, state: new_state, index: index})
                    .then((x)=>{
                        _this.$store.commit('article/update_article_list_by_index',  {index: index, key: 'need_check', val: false})
                        _this.$message.success(msg + "成功");
                    }).catch((x)=>{
                    _this.$message.error(msg + "失败");
                })
            },
            changeSelected: function(selected) {
                const st = selected.map(x=>{
                    return x.id
                });

                this.$store.commit("article/set_selected", st);
            },
            changeDate: function() {
                if(this.st || this.et) {
                    this.get_article_list()
                }
            },
            multiOffline: function() {
                const _this = this;
                if(this.is_selected && this.selected.length === 0) {
                    this.$message.error('没有选中任何文章');
                    return false;
                }

                _this.$store.dispatch('article/multiOffline', _this.get_params()).then(result=> {
                    _this.update_article_state(false);
                    _this.$message.success("成功全部下线")
                });
            },
            multiCheck: function() {
                const _this = this;
                if(this.is_selected && this.selected.length === 0) {
                    this.$message.error('没有选中任何文章');
                    return false;
                }

                _this.$store.dispatch('article/multiOnline', _this.get_params()).then(result=> {
                    _this.update_article_state(true);
                    _this.$message.success("成功全部通过审核")
                });
            },
            multiOnline: function() {
                const _this = this;
                if(this.is_selected && this.selected.length === 0) {
                    this.$message.error('没有选中任何文章');
                    return false;
                }

                _this.$store.dispatch('article/multiOnline', _this.get_params()).then(result=> {
                    _this.update_article_state(true);
                    _this.$message.success("成功全部上线")
                });
            },
            multiDelete: function() {
                const _this = this;
                _this.$store.dispatch('article/multiDelete', _this.get_params()).then(result=> {
                    _this.$message.success("成功全部删除")
                });
            },
            update_article_state: function(state) {
                if(this.is_selected) {
                    for(let id of this.selected) {
                        this.$store.commit('article/update_article_list_by_id',  {id: id, key: 'state', val: state})
                    }
                }
                else {
                    for(let index in this.article_lists) {
                        this.$store.commit('article/update_article_list_by_index',  {index: index, key: 'state', val: state})
                    }
                }
            },
            get_params: function() {
                let params = {
                    is_selected: this.is_selected,
                    selected: this.selected
                };

                if(this.search_key) params['keywords'] = this.search_key
                if(this.sites.length > 0) params['sites'] = this.sites.join(',')
                if(this.category.length > 0) params['category'] = this.category.join(',')
                if(this.st || this.et) {
                    params['st'] = this.st;
                    params['et'] = this.et;
                    params['time_key'] = this.time_key;
                }

                return params;
            },
            drop_article: function(index, row) {
                const _this = this;
                MessageBox.confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    _this.delete_article({id: row.id, index: index}).then(result=> {
                        _this.$message.success("删除成功");
                    })
                }).catch(() => {
                    _this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            filterHandler: function(value, row, column) {
                const property = column['property'];
                return row[property] == value;
            },
            page_change: function(page) {
                this.set_current_page(page)
                this.get_article_list()
            },
            size_change: function(size) {
                this.set_per_page(size);
                this.get_article_list()
            },
            transfer: function(img) {
                return img;
            },
            changeTableSort: function(column) {
                this.set_order_by(column['prop']);
                this.set_order(column['order'] === 'ascending'?'asc':'desc');
                this.get_article_list()
            },
            submitSiteForm: function() {
                const _this = this;
                this.$refs['site_form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_source_site(this.site_form).then(function(result){
                            _this.dialog_visible_add = false;
                            _this.$store.commit('article/set_default_site_form');
                            _this.$message.success("添加成功");
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            clear_filter: function () {
                this.clear_filter_options();
                this.get_article_list();
            }
        }
    }
</script>

<style scoped>
    .padding-row-15 {
        padding: 15px;
    }
    .padding-row-top-40 {
        padding-top: 40px;
    }
</style>