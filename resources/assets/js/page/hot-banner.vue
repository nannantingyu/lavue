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
                <el-button @click="add_hot_banner">添加热门图片</el-button>
            </el-col>
        </el-row>
        <el-table :data="hot_banner_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                    prop="title"
                    sortable
                    :label="columns['title']['title']"
                    v-if="columns['title']['show']" width="*">
            </el-table-column>
            <el-table-column
                    :label="columns['image']['title']"
                    v-if="columns['image']['show']"
                    width="150">
                <template slot-scope="scope">
                    <img width="160" :src="transfer(scope.row.image)" :alt="scope.row.image">
                </template>
            </el-table-column>
            <el-table-column
                    prop="sequence"
                    :label="columns['sequence']['title']"
                    v-if="columns['sequence']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="link"
                    :label="columns['link']['title']"
                    v-if="columns['link']['show']"
                    width="160">
            </el-table-column>
            <el-table-column
                    prop="state"
                    sortable
                    :label="columns['state']['title']"
                    v-if="columns['state']['show']"
                    width="80">
                <template slot-scope="scope">
                    <el-switch v-model="scope.row.state"
                               :disabled="!user_module_permission['hot-banner-delete']"
                               @change="changeState(scope.row)"></el-switch>
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
            <el-table-column label="操作" fixed="right" width="160">
                <template slot-scope="scope">
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['hot-banner-delete']"
                            @click="setState(scope.$index, scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['hot-banner-delete']"
                            @click="edithot_banner(scope.$index, scope.row)">编辑</el-button>
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
        <el-dialog title="添加热门图片" :visible.sync="dialog_visible">
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
                <el-form-item :label-width="formLabelWidth" label="标题" prop="title">
                    <el-input v-model="form.title"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="图片" prop="image">
                    <el-upload
                            class="avatar-uploader"
                            :limit="1"
                            action="/uploads_image"
                            name="fileDataFileName"
                            list-type="picture-card"
                            :headers="headers"
                            :file-list="fileimgs"
                            :on-success="handleSuccess"
                            :on-remove="handleRemove">
                        <i class="el-icon-plus"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="链接" prop="link">
                    <el-input v-model="form.link"></el-input>
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
        name: "hot_banner",
        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'hot_banner_lists': state=>state.hot_banner.hot_banner_lists,
                'columns': state=>state.hot_banner.columns,
                'loading': state=>state.hot_banner.loading,
                'current_page': state=>state.hot_banner.current_page,
                'per_page': state=>state.hot_banner.per_page,
                'total': state=>state.hot_banner.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.hot_banner.form,
                'rules': state=>state.hot_banner.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.hot_banner.row_index,
                'article_options': state=>state.hot_banner.article_options,
                'search_article_lists': state=>state.hot_banner.search_article_lists
            }),
            ...mapGetters({
                'fileimgs': 'hot_banner/fileimgs',
            }),
            dialog_visible: {
                get() {
                    return this.$store.state.hot_banner.dialog_visible
                },
                set(value) {
                    this.$store.commit('hot_banner/set_dialog_visible', value)
                }
            },
            search_article: {
                get() {
                    return this.$store.state.hot_banner.search_article
                },
                set(value) {
                    this.$store.commit('hot_banner/set_search_article', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.hot_banner.show_type
                },
                set(value) {
                    this.$store.commit('hot_banner/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "hot_banner/set_current_page",
                'set_per_page': "hot_banner/set_per_page",
                'filte_data': "hot_banner/filte_data",
                'set_dialog_visible': "hot_banner/set_dialog_visible",
                'set_form': "hot_banner/set_form",
                'set_row_index': "hot_banner/set_row_index",
                'set_form_value': "hot_banner/set_form_value",
                'update_hot_banner_list_by_index': "hot_banner/update_hot_banner_list_by_index",
                'append_hot_banner_list': "hot_banner/append_hot_banner_list",
                'set_default_form': "hot_banner/set_default_form",
                'set_loading': "hot_banner/set_loading",
                'set_article_options': "hot_banner/set_article_options",
                'set_search_article_lists': "hot_banner/set_search_article_lists",
            }),
            ...mapActions({
                'get_hot_banner_lists': 'hot_banner/get_hot_banner_lists',
                'get_hot_banner': "hot_banner/get_hot_banner",
                'add_or_update_hot_banner': "hot_banner/add_or_update_hot_banner",
                'set_hot_banner_state': "hot_banner/set_hot_banner_state",
                'sea_article': "article/search_article"
            }),
            edithot_banner: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            changeTableSort: function(column) {
                this.$store.commit("hot_banner/sort_data", {column:column['prop'], order: column['order']})
            },
            search_article_change: function(item) {
                let article = this.search_article_lists.filter(x=> {
                    return x.id === item;
                });

                article = article.length > 0?article[0]:null;
                if(article) {
                    article.link = article.url;
                    article.state = true;
                    article.sequence = 1;
                    let form = {
                        link: article.url,
                        state: true,
                        sequence: 1,
                        title: article.title,
                        image: article.image
                    };

                    this.set_form(form)
                }
            },
            handleSuccess: function(response, file, file_list) {
                if(response.success) {
                    this.set_form_value({key: 'image', value: response.file_path})
                }
            },
            handleRemove: function() {
                this.set_form_value({key: 'image', value: null})
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
            changeState: function (row) {
                const _this = this;
                this.set_hot_banner_state({id:row.id, state:row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            add_hot_banner: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_hot_banner(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    _this.update_hot_banner_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_hot_banner_list(_this.form);
                                _this.$message.success("添加成功");
                            }

                            _this.filte_data();
                            _this.set_dialog_visible(false);
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            transfer: function(img) {
                return img?'http://images.jujin8.com'+img.replace('/uploads/crawler', '/uploads'):''
            }
        },
        mounted() {
            const _this = this;
            this.get_hot_banner_lists().then(result=> {
                _this.$message.success('成功获取系统配置！');
            })
        }
    }
</script>

<style scoped>
    .el-input--medium {
        width: 100% !important;
    }
</style>