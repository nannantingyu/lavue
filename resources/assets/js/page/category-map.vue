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
            <el-col :span="5">
                <el-radio-group v-model="show_type">
                    <el-radio-button label="3">全部</el-radio-button>
                    <el-radio-button label="1">上线</el-radio-button>
                    <el-radio-button label="0">下线</el-radio-button>
                </el-radio-group>
            </el-col>
            <el-col :span="4">
                <el-select v-model="sites" multiple placeholder="请选择来源站点" @change="filter_data">
                    <el-option
                        v-for="item in source_sites"
                        :key="item.site"
                        :label="item.site"
                        :value="item.site">
                    </el-option>
                </el-select>
            </el-col>
            <el-col :span="2">
                <el-button @click="add_category_map">添加文章分类</el-button>
            </el-col>
        </el-row>
        <el-table :data="category_map_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                    prop="source_category"
                    sortable
                    :label="columns['source_category']['title']"
                    v-if="columns['source_category']['show']" width="200">
            </el-table-column>
            <el-table-column
                    :label="columns['target']['title']"
                    v-if="columns['target']['show']"
                    width="*">
                <template slot-scope="scope">
                    {{ get_catname(scope.row.target) }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="source_site"
                    sortable
                    :label="columns['source_site']['title']"
                    v-if="columns['source_site']['show']"
                    width="*">
            </el-table-column>
            <el-table-column
                    prop="state"
                    sortable
                    :label="columns['state']['title']"
                    v-if="columns['state']['show']"
                    width="80">
                <template slot-scope="scope">
                    <el-switch v-model="scope.row.state" @change="changeState(scope.row)"></el-switch>
                </template>
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    sortable
                    :label="columns['created_at']['title']"
                    v-if="columns['created_at']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="updated_at"
                    sortable
                    :label="columns['updated_at']['title']"
                    v-if="columns['updated_at']['show']"
                    width="120">
            </el-table-column>
            <el-table-column label="操作" fixed="right" width="160">
                <template slot-scope="scope">
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['category-map-delete']"
                            @click="setState(scope.$index, scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['category-map-delete']"
                            @click="editcategory_map(scope.$index, scope.row)">编辑</el-button>
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
        <el-dialog title="添加文章分类" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="分类名称" prop="source_category">
                    <el-input v-model="form.source_category"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="源网站" prop="source_site">
                    <!--<el-input v-model="form.source_site"></el-input>-->
                    <el-select v-model="form.source_site" placeholder="请选择来源站点">
                        <el-option
                            v-for="item in source_sites"
                            :key="item.site"
                            :label="item.site"
                            :value="item.site">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="目标分类" prop="target">
                    <el-select v-model="form.target" placeholder="请选择分类">
                        <el-option
                            v-for="item in category_list"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="状态" prop="state">
                    <el-switch v-model="form.state">启用</el-switch>
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
        name: "category_map",
        computed: {
            ...mapState({
                'category_map_lists': state=>state.category_map.category_map_lists,
                'category_list': state=>state.category_map.category_list,
                'columns': state=>state.category_map.columns,
                'loading': state=>state.category_map.loading,
                'source_sites': state=>state.article.source_sites,
                'current_page': state=>state.category_map.current_page,
                'per_page': state=>state.category_map.per_page,
                'total': state=>state.category_map.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.category_map.form,
                'rules': state=>state.category_map.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.category_map.row_index,
            }),

            sites: {
                get() {
                    return this.$store.state.category_map.sites
                },
                set(value) {
                    this.$store.commit('category_map/set_sites', value)
                }
            },
            dialog_visible: {
                get() {
                    return this.$store.state.category_map.dialog_visible
                },
                set(value) {
                    this.$store.commit('category_map/set_dialog_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.category_map.show_type
                },
                set(value) {
                    this.$store.commit('category_map/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "category_map/set_current_page",
                'set_per_page': "category_map/set_per_page",
                'filte_data': "category_map/filte_data",
                'set_dialog_visible': "category_map/set_dialog_visible",
                'set_form': "category_map/set_form",
                'set_row_index': "category_map/set_row_index",
                'set_source_sites': "article/set_source_sites",
                'set_form_value': "category_map/set_form_value",
                'update_category_map_list_by_index': "category_map/update_category_map_list_by_index",
                'append_category_map_list': "category_map/append_category_map_list",
                'set_default_form': "category_map/set_default_form",
                'set_category_list': "category_map/set_category_list"
            }),
            ...mapActions({
                'get_category_map_lists': 'category_map/get_category_map_lists',
                'get_category_map': "category_map/get_category_map",
                'add_or_update_category_map': "category_map/add_or_update_category_map",
                'set_category_map_state': "category_map/set_category_map_state"
            }),
            editcategory_map: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            changeTableSort: function(column) {
                this.$store.commit("category_map/sort_data", {column:column['prop'], order: column['order']})
            },
            changeState: function (row) {
                const _this = this;
                this.set_category_map_state({id:row.id, state:row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            filter_data: function() {
                console.log(this.sites)
                this.$store.commit("category_map/filter_data_by_site", this.sites);
            },
            add_category_map: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_category_map(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    _this.update_category_map_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_category_map_list(_this.form);
                                _this.$message.success("添加成功");
                            }

                            _this.filte_data();
                            _this.set_dialog_visible(false);
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            get_catname: function(id) {
                const cate = this.category_list.filter(x=> {
                    return x.id === id;
                })

                return cate[0].name;
            },
            get_table_data: function() {
                const _this = this;
                this.get_category_map_lists().then(result=> {
                    _this.$message.success('成功获取分类对应表！');
                })
            }
        },
        mounted() {
            const _this = this;
            if(this.category_list.length === 0) {
                this.$store.dispatch('category/get_category_lists').then(cates=> {
                    _this.set_category_list(cates);
                    _this.get_table_data();
                })
            }
            else {
                _this.get_table_data();
            }

            if(this.source_sites.length === 0) {
                this.$store.dispatch("config/get_config", {key: 'article_source_site'}).then(result=>{
                    if(result.data.success === 1) {
                        _this.set_source_sites({source_sites: result.data.data.value, is_bak: true});
                    }
                });
            }
        }
    }
</script>

<style scoped>
</style>