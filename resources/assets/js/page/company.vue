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
                <el-button @click="add_company">添加机构</el-button>
            </el-col>
        </el-row>
        <el-table :data="company_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                    v-if="columns['name']['show']" width="250">
            </el-table-column>
            <el-table-column
                    prop="shortname"
                    :label="columns['shortname']['title']"
                    v-if="columns['shortname']['show']"
                    width="250">
            </el-table-column>
            <el-table-column
                    prop="sequence"
                    :label="columns['sequence']['title']"
                    v-if="columns['sequence']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="tag"
                    :label="columns['tag']['title']"
                    v-if="columns['tag']['show']"
                    width="*">
            </el-table-column>
            <el-table-column
                    prop="link"
                    :label="columns['link']['title']"
                    v-if="columns['link']['show']"
                    width="300">
            </el-table-column>
            <el-table-column
                    :label="columns['logo']['title']"
                    v-if="columns['logo']['show']"
                    width="180">
                <template slot-scope="scope">
                    <img width="140" :src="transfer(scope.row.logo)" :alt="scope.row.logo">
                </template>
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
                            :disabled="!user_module_permission['company-delete']"
                            @click="setState(scope.$index, scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['company-delete']"
                            @click="editcompany(scope.$index, scope.row)">编辑</el-button>
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
        <el-dialog title="添加机构" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="机构名称" prop="name">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="缩略名称" prop="shortname">
                    <el-input v-model="form.shortname"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="链接" prop="link">
                    <el-input v-model="form.link"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="标签" prop="tag">
                    <el-input v-model="form.tag"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="分类" prop="categories">
                    <el-select v-model="form.categories" multiple placeholder="请选择分类">
                        <el-option
                                v-for="item in category_company_lists"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="Logo">
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
    import {mapState, mapActions, mapGetters, mapMutations} from 'vuex'
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
        name: "company",
        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'company_lists': state=>state.company.company_lists,
                'columns': state=>state.company.columns,
                'loading': state=>state.company.loading,
                'current_page': state=>state.company.current_page,
                'per_page': state=>state.company.per_page,
                'total': state=>state.company.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.company.form,
                'rules': state=>state.company.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.company.row_index,
                'category_company_lists': state=>state.category_company.category_company_lists
            }),
            ...mapGetters({
                'fileimgs': 'company/fileimgs',
            }),
            dialog_visible: {
                get() {
                    return this.$store.state.company.dialog_visible
                },
                set(value) {
                    this.$store.commit('company/set_dialog_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.company.show_type
                },
                set(value) {
                    this.$store.commit('company/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "company/set_current_page",
                'set_per_page': "company/set_per_page",
                'filte_data': "company/filte_data",
                'set_dialog_visible': "company/set_dialog_visible",
                'set_form': "company/set_form",
                'set_row_index': "company/set_row_index",
                'set_form_value': "company/set_form_value",
                'update_company_list_by_index': "company/update_company_list_by_index",
                'append_company_list': "company/append_company_list",
                'set_default_form': "company/set_default_form",
                'transfer': 'transfer'
            }),
            ...mapActions({
                'get_company_lists': 'company/get_company_lists',
                'get_company': "company/get_company",
                'add_or_update_company': "company/add_or_update_company",
                'set_company_state': "company/set_company_state"
            }),
            handleSuccess: function(response, file, file_list) {
                if(response.success) {
                    this.set_form_value({key: 'logo', value: response.file_path})
                }
            },
            handleRemove: function() {
                this.set_form_value({key: 'logo', value: null})
            },
            editcompany: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            changeState: function (row) {
                const _this = this;
                this.set_company_state({id:row.id, state:row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            add_company: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_company(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    _this.update_company_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_company_list(_this.form);
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
            },
            changeTableSort: function(column) {
                this.$store.commit("company/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted() {
            const _this = this;
            if(this.category_company_lists.length <= 0) {
                this.$store.dispatch('category_company/get_category_company_lists').then(result=> {
                });
            }
            this.get_company_lists().then(result=> {
                _this.$message.success('成功获取机构数据！');
            })
        }
    }
</script>

<style scoped>
</style>