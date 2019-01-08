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
                <el-button @click="add_category">添加文章分类</el-button>
            </el-col>
        </el-row>
        <el-table :data="category_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                    prop="name"
                    sortable
                    :label="columns['name']['title']"
                    v-if="columns['name']['show']" width="230">
            </el-table-column>
            <el-table-column
                    prop="ename"
                    sortable
                    :label="columns['ename']['title']"
                    v-if="columns['ename']['show']"
                    width="*">
            </el-table-column>
            <el-table-column
                    prop="sequence"
                    sortable
                    :label="columns['sequence']['title']"
                    v-if="columns['sequence']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="pid"
                    :label="columns['pid']['title']"
                    v-if="columns['pid']['show']"
                    width="160">
                <template slot-scope="scope">
                    {{ get_category_name_by_id(scope.row.pid) }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="type"
                    sortable
                    :label="columns['type']['title']"
                    v-if="columns['type']['show']"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="target"
                    :label="columns['target']['title']"
                    v-if="columns['target']['show']"
                    width="160">
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
                            :disabled="!user_module_permission['category-delete']"
                            @click="changeState(scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['category-delete']"
                            @click="editcategory(scope.$index, scope.row)">编辑</el-button>
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
                <el-form-item :label-width="formLabelWidth" label="分类名称" prop="name">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="分类英文名称" prop="ename">
                    <el-input v-model="form.ename"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="类型" prop="target">
                    <el-select v-model="form.type" placeholder="请选择分类类型">
                        <el-option
                            v-for="item in category_types"
                            :key="item"
                            :label="item"
                            :value="item">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="Target" prop="target">
                    <el-input v-model="form.target"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="父分类" prop="pid">
                    <el-select v-model="form.pid" placeholder="请选择分类类型">
                        <el-option
                            key="0"
                            label="顶级"
                            value="0">
                        </el-option>
                        <el-option
                            v-for="item in category_lists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
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
        name: "category",
        computed: {
            ...mapState({
                'category_lists': state=>state.category.category_lists,
                'columns': state=>state.category.columns,
                'loading': state=>state.category.loading,
                'current_page': state=>state.category.current_page,
                'per_page': state=>state.category.per_page,
                'total': state=>state.category.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.category.form,
                'rules': state=>state.category.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.category.row_index,
                'category_types': state=>state.category.category_types
            }),
            dialog_visible: {
                get() {
                    return this.$store.state.category.dialog_visible
                },
                set(value) {
                    this.$store.commit('category/set_dialog_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.category.show_type
                },
                set(value) {
                    this.$store.commit('category/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "category/set_current_page",
                'set_per_page': "category/set_per_page",
                'filte_data': "category/filte_data",
                'set_dialog_visible': "category/set_dialog_visible",
                'set_form': "category/set_form",
                'set_row_index': "category/set_row_index",
                'set_form_value': "category/set_form_value",
                'update_category_list_by_index': "category/update_category_list_by_index",
                'append_category_list': "category/append_category_list",
                'set_default_form': "category/set_default_form",
                'set_category_types': "category/set_category_types"
            }),
            ...mapActions({
                'get_category_lists': 'category/get_category_lists',
                'get_category': "category/get_category",
                'add_or_update_category': "category/add_or_update_category",
                'set_category_state': "category/set_category_state"
            }),
            editcategory: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            changeState: function (row) {
                const _this = this;
                this.set_category_state({id:row.id, state:row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    let index = 0;
                    for(; index < _this.category_lists.length; index ++) {
                        if(_this.category_lists[index].id = row.id) {
                            break;
                        }
                    }

                    _this.update_category_list_by_index({
                        index: index,
                        key: "state",
                        value: !row.state
                    });
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            add_category: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            get_category_name_by_id: function(id) {
                if(id === 0) return "顶级";
                const cate = this.category_lists.filter(x=>x.id === id);
                if(cate.length > 0) return cate[0]['name']
                return "";
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_category(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    key == 'state' && (_this.form[key] = _this.form[key] == 1)
                                    _this.update_category_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_category_list(_this.form);
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
                this.$store.commit("category/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted() {
            const _this = this;
            this.get_category_lists().then(result=> {
                _this.$message.success('成功获取文章分类！');
            });

            if(this.category_types.length === 0) {
                this.$store.dispatch("config/get_config", {key: 'category_types'}).then(result=>{
                    if(result.data.success === 1) {
                        const types = result.data.data.value.split(',');
                        _this.set_category_types(types);
                    }
                });
            }
        }
    }
</script>

<style scoped>
</style>