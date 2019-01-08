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
                <el-button @click="add_entry">添加词条</el-button>
            </el-col>
        </el-row>
        <el-table :data="entry_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                prop="entry_name"
                sortable
                :label="columns['entry_name']['title']"
                v-if="columns['entry_name']['show']" width="230">
            </el-table-column>
            <el-table-column
                prop="description"
                sortable
                :label="columns['description']['title']"
                v-if="columns['description']['show']" width="*">
            </el-table-column>
            <el-table-column
                prop="sequence"
                sortable
                :label="columns['sequence']['title']"
                v-if="columns['sequence']['show']"
                width="120">
            </el-table-column>
            <el-table-column
                prop="state"
                sortable
                :label="columns['state']['title']"
                v-if="columns['state']['show']"
                width="80">
                <template slot-scope="scope">
                    <el-switch v-model="scope.row.state" @change="changeState(scope.$index, !scope.row.state, scope.row.id)"></el-switch>
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
            <el-table-column label="操作" fixed="right" width="250">
                <template slot-scope="scope">
                    <el-button
                        size="mini"
                        :type="scope.row.state?'success':'danger'"
                        :disabled="!user_module_permission['entry-delete']"
                        @click="changeState(scope.$index, scope.row.state, scope.row.id)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                        size="mini"
                        :type="scope.row.state?'success':'danger'"
                        :disabled="!user_module_permission['entry-delete']"
                        @click="editentry(scope.$index, scope.row)">编辑</el-button>
                    <el-button
                        size="mini"
                        :type="scope.row.state?'success':'danger'"
                        :disabled="!user_module_permission['entry-update']"
                        @click="related_article(scope.$index, scope.row)">文章列表</el-button>
                </template>
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
        <el-dialog title="添加词条" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="词条名称" prop="entry_name">
                    <el-input v-model="form.entry_name"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="描述" prop="description">
                    <el-input v-model="form.description" type="textarea"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="状态" prop="state">
                    <el-switch v-model="form.state">启用</el-switch>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="顺序" prop="sequence">
                    <el-input v-model="form.sequence" type="number"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="submitForm()">{{ form.id?"更新":"添加" }}</el-button>
                    </el-col>
                </el-form-item>
            </el-form>
        </el-dialog>
        <el-dialog title="词条文章" :visible.sync="dialog_article_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="选择文章">
                    <el-select
                        v-model="search_article"
                        filterable
                        remote
                        size="medium"
                        @change="search_article_change"
                        reserve-keyword
                        placeholder="请输入关键词"
                        :remote-method="s_article"
                        :loading="loading">
                        <el-option
                            v-for="item in article_options"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="add_entry_article()">{{ form.id?"更新":"添加" }}</el-button>
                    </el-col>
                </el-form-item>
            </el-form>
            <div style="width: 100%;">
                <el-table :data="entry_articles.slice((current_page_article-1)*per_page_article, current_page_article*per_page_article)"
                          v-loading="loading"
                          ref="entry_article_table"
                          @sort-change="changeTableSort">
                    <el-table-column
                        prop="id"
                        sortable
                        label="ID"
                        width="80">
                    </el-table-column>
                    <el-table-column
                        prop="title"
                        sortable
                        label="标题"
                        width="*">
                    </el-table-column>
                    <el-table-column
                        prop="publish_time"
                        sortable
                        label="发布时间"
                        width="120">
                    </el-table-column>
                    <el-table-column
                        prop="updated_at"
                        label="更新时间"
                        width="120">
                    </el-table-column>
                    <el-table-column label="操作" fixed="right" width="80">
                        <template slot-scope="scope">
                            <el-button
                                size="mini"
                                :type="scope.row.state?'success':'danger'"
                                :disabled="!user_module_permission['entry-delete']"
                                @click="removeArticle(scope.$index, scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <el-pagination
                    background
                    @current-change="set_current_page_article"
                    @size-change="set_per_page_article"
                    :current-page="current_page_article"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="total_article">
                </el-pagination>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
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
        name: "entry",
        computed: {
            ...mapState({
                'entry_lists': state=>state.entry.entry_lists,
                'columns': state=>state.entry.columns,
                'loading': state=>state.entry.loading,
                'current_page': state=>state.entry.current_page,
                'current_page_article': state=>state.entry.current_page_article,
                'per_page': state=>state.entry.per_page,
                'total': state=>state.entry.total,
                'per_page_article': state=>state.entry.per_page_article,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.entry.form,
                'rules': state=>state.entry.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.entry.row_index,
                'current_entry_id': state=>state.entry.current_entry_id,
                'entry_types': state=>state.entry.entry_types,
                'article_options': state=>state.entry.article_options,
                'search_article_lists': state=>state.entry.search_article_lists,
                'entry_articles': state=>state.entry.entry_articles
            }),
            total_article: {
                get() {
                    return this.$store.state.entry.total_article
                },
                set(value) {
                    this.$store.commit('entry/set_total_article', value)
                }
            },
            search_article: {
                get() {
                    return this.$store.state.entry.search_article
                },
                set(value) {
                    this.$store.commit('entry/set_search_article', value)
                }
            },
            dialog_visible: {
                get() {
                    return this.$store.state.entry.dialog_visible
                },
                set(value) {
                    this.$store.commit('entry/set_dialog_visible', value)
                }
            },
            dialog_article_visible: {
                get() {
                    return this.$store.state.entry.dialog_article_visible
                },
                set(value) {
                    this.$store.commit('entry/set_dialog_article_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.entry.show_type
                },
                set(value) {
                    this.$store.commit('entry/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "entry/set_current_page",
                'set_current_page_article': "entry/set_current_page_article",
                'set_per_page': "entry/set_per_page",
                'set_per_page_article': "entry/set_per_page_article",
                'filte_data': "entry/filte_data",
                'set_dialog_visible': "entry/set_dialog_visible",
                'set_form': "entry/set_form",
                "set_current_entry_id": "entry/set_current_entry_id",
                'add_related_article': "entry/add_related_article",
                'set_row_index': "entry/set_row_index",
                'set_form_value': "entry/set_form_value",
                'update_entry_list_by_index': "entry/update_entry_list_by_index",
                'append_entry_list': "entry/append_entry_list",
                'set_default_form': "entry/set_default_form",
                'set_entry_types': "entry/set_entry_types",
                'set_loading': "entry/set_loading",
                'set_article_options': "entry/set_article_options",
                'set_search_article_lists': "entry/set_search_article_lists",
                'set_entry_articles': "entry/set_entry_articles"
            }),
            ...mapActions({
                'get_entry_lists': 'entry/get_entry_lists',
                'get_entry': "entry/get_entry",
                'add_or_update_entry': "entry/add_or_update_entry",
                'set_entry_state': "entry/set_entry_state",
                'sea_article': "article/search_article",
                'addEntryArticle': 'entry/addEntryArticle',
                'removeEntryArticle': 'entry/removeEntryArticle'
            }),
            editentry: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            removeArticle: function(index, row) {
                const _this = this;
                this.removeEntryArticle({article_id: row.id, entry_id: this.current_entry_id})
                    .then(result=>{
                        _this.entry_articles.splice(index, 1);
                        _this.$refs.entry_article_table.data = _this.entry_articles.slice(0, _this.per_page_article);
                        _this.total_article -= 1;
                        _this.$message.success("删除成功");
                    })
            },
            add_entry_article: function() {
                const _this = this;
                this.addEntryArticle({article_id: this.search_article, entry_id: this.current_entry_id})
                    .then(result=>{
                        _this.entry_articles.splice(0, 0, this.search_article_lists.find(x=>x.id==_this.search_article));
                        _this.total_article += 1;
                        _this.$refs.entry_article_table.data = _this.entry_articles.slice(0, _this.per_page_article);
                        _this.$message.success("添加成功");
                    });
            },
            search_article_change: function(item) {
                let article = this.search_article_lists.filter(x=> {
                    return x.id === item;
                });

                article = article.length > 0?article[0]:null;
                if(article) {
                    console.log(article);
                }
            },
            s_article: function(query) {
                const _this = this;
                if (query !== '') {
                    this.set_loading(true);
                    this.sea_article(query).then(result=>{
                        _this.set_loading(false);
                        const options = result.data.map(item => {
                            return {label: item['title'], value: item['id']};
                        });

                        _this.set_search_article_lists(result.data);
                        _this.set_article_options(options);
                    })
                } else {
                    this.options4 = [];
                }
            },
            related_article: function(index, row) {
                const _this = this;
                this.set_current_entry_id(row.id);
                this.dialog_article_visible = true;
                this.get_entry(row.id).then(result=> {
                    _this.set_entry_articles(result.articles);
                    _this.total_article = result.articles.length;
                });
            },
            changeState: function (index, s, id) {
                const _this = this, st = s?1:0;

                this.set_entry_state({id:id, state:st}).then(result=>{
                    let message = s?"下线成功":"上线成功";
                    _this.update_entry_list_by_index({
                        index: index,
                        key: 'state',
                        value: 0===st
                    });

                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            add_entry: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            get_entry_name_by_id: function(id) {
                if(id === 0) return "顶级";
                const cate = this.entry_lists.filter(x=>x.id === id);
                if(cate.length > 0) return cate[0]['name']
                return "";
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_entry(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    key == 'state' && (_this.form[key] = _this.form[key] == 1)
                                    _this.update_entry_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_entry_list(_this.form);
                                _this.$message.success("添加成功");
                            }

                            _this.filte_data();
                            _this.set_dialog_visible(false);
                        }).catch(errors=> {
                            for(let error_module in errors) {
                                for(let msg of errors[error_module])
                                    _this.$message.error(msg)
                            }
                        })
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            changeTableSort: function(column) {
                this.$store.commit("entry/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted() {
            const _this = this;
            this.get_entry_lists().then(result=> {
                _this.$message.success('成功获取词条！');
                console.log(_this.entry_lists);
            });
        }
    }
</script>

<style scoped>
    .el-select {
        width: 100%;
    }
</style>