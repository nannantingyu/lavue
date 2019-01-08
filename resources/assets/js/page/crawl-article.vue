<template>
    <div>
        <el-row>
            <el-col :span="6">
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
                <el-radio-group v-model="show_type">
                    <el-radio-button label="3">全部</el-radio-button>
                    <el-radio-button label="1">上线</el-radio-button>
                    <el-radio-button label="0">下线</el-radio-button>
                </el-radio-group>
            </el-col>
            <el-col :span="2">
                <el-button @click="add_crawl_article">添加爬取文章链接</el-button>
            </el-col>
        </el-row>
        <el-table :data="crawl_article_lists.slice((current_page-1)*per_page, current_page*per_page)"
                  v-loading="loading"
                  @sort-change="changeTableSort"
                  style="width: 100%">
            <el-table-column
                    prop="id"
                    sortable
                    :label="columns['id']['title']"
                    v-if="columns['id']['show']"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="url"
                    sortable
                    :label="columns['url']['title']"
                    v-if="columns['url']['show']" width="*">
                <template slot-scope="scope">
                    <a :href="scope.row.url" target="_blank">{{scope.row.url}}</a>
                </template>
            </el-table-column>
            <el-table-column
                prop="url"
                sortable
                :label="columns['url']['title']"
                v-if="columns['url']['show']" width="100">
                <template slot-scope="scope">
                    <a :href="'/toArticlePage?page='+scope.row.url" target="_blank">官网展示</a>
                </template>
            </el-table-column>
            <el-table-column
                    :label="columns['user_id']['title']"
                    v-if="columns['user_id']['show']"
                    width="120">
                <template slot-scope="scope">
                    {{ get_catname(scope.row.target) }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    :label="columns['created_at']['title']"
                    v-if="columns['created_at']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="updated_at"
                    :label="columns['updated_at']['title']"
                    v-if="columns['updated_at']['show']"
                    width="120">
            </el-table-column>
        </el-table>
        <el-pagination
                background
                @current-change="set_current_page"
                @size-change="set_per_page"
                :current-page="current_page"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
        </el-pagination>
        <el-dialog title="添加爬取文章链接" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="链接" prop="url">
                    <el-input v-model="form.url"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="分类" prop="categories">
                    <el-select v-model="form.categories" multiple placeholder="请选择分类">
                        <el-option
                            v-for="item in article_categories"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="submitForm()">{{ form.id?"更新":"添加" }}</el-button>
                    </el-col>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'
    import {Table, TableColumn, Pagination, Loading, Popover, RadioGroup, RadioButton, Dialog, FormItem, Input, Select, Option, Switch, DatePicker, Upload, Form} from 'element-ui'
    Vue.use(FormItem);
    Vue.use(Input);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(Switch);
    Vue.use(DatePicker);
    Vue.use(Upload);
    Vue.use(Form);
    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Pagination);
    Vue.use(Loading);
    Vue.use(Popover);
    Vue.use(RadioGroup);
    Vue.use(RadioButton);
    Vue.use(Dialog);

    export default {
        name: "crawl_article",
        computed: {
            ...mapState({
                'crawl_article_lists': state=>state.crawl_article.crawl_article_lists,
                'columns': state=>state.crawl_article.columns,
                'loading': state=>state.crawl_article.loading,
                'current_page': state=>state.crawl_article.current_page,
                'per_page': state=>state.crawl_article.per_page,
                'total': state=>state.crawl_article.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.crawl_article.form,
                'rules': state=>state.crawl_article.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.crawl_article.row_index,
                'article_categories': state=>state.crawl_article.category_list,
            }),
            dialog_visible: {
                get() {
                    return this.$store.state.crawl_article.dialog_visible
                },
                set(value) {
                    this.$store.commit('crawl_article/set_dialog_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.crawl_article.show_type
                },
                set(value) {
                    this.$store.commit('crawl_article/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "crawl_article/set_current_page",
                'set_per_page': "crawl_article/set_per_page",
                'filte_data': "crawl_article/filte_data",
                'set_dialog_visible': "crawl_article/set_dialog_visible",
                'set_form': "crawl_article/set_form",
                'set_row_index': "crawl_article/set_row_index",
                'set_form_value': "crawl_article/set_form_value",
                'update_crawl_article_list_by_index': "crawl_article/update_crawl_article_list_by_index",
                'append_crawl_article_list': "crawl_article/append_crawl_article_list",
                'set_default_form': "crawl_article/set_default_form",
                'set_category_list': "crawl_article/set_category_list",
                'set_article_categories': "article/set_article_categories"
            }),
            ...mapActions({
                'get_crawl_article_lists': 'crawl_article/get_crawl_article_lists',
                'get_crawl_article': "crawl_article/get_crawl_article",
                'add_or_update_crawl_article': "crawl_article/add_or_update_crawl_article",
                'set_crawl_article_state': "crawl_article/set_crawl_article_state"
            }),
            editcrawl_article: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            add_crawl_article: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_crawl_article(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    _this.update_crawl_article_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_crawl_article_list(_this.form);
                                _this.$message.success("添加成功");
                            }

                            _this.filte_data();
                            _this.set_dialog_visible(false);
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            get_catname: function(id) {
                return this.article_categories.filter(x=>x.id === id);
            },
            set_filter_article_categories: function(categories) {
                let article_categories = categories.filter(x=>x.type === 'article_category');
                this.set_category_list(article_categories);
            },
            changeTableSort: function(column) {
                this.$store.commit("crawl_article/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted() {
            const _this = this;
            this.get_crawl_article_lists().then(result=> {
                _this.$message.success('成功获取文章分类！');
            });

            if(this.article_categories.length === 0) {
                this.$store.dispatch('category/get_category_lists').then(result=> {
                    this.set_filter_article_categories(result)
                })
            }
            else this.set_filter_article_categories(this.category_lists);
        }
    }
</script>

<style scoped>
</style>