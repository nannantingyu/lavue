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
        </el-row>
        <el-table :data="users"
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
                    v-if="columns['name']['show']" width="200">
            </el-table-column>
            <el-table-column
                    prop="nickname"
                    :label="columns['nickname']['title']"
                    v-if="columns['nickname']['show']"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="email"
                    :label="columns['email']['title']"
                    v-if="columns['email']['show']"
                    width="*">
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    :label="columns['created_at']['title']"
                    v-if="columns['created_at']['show']"
                    width="160">
            </el-table-column>
            <el-table-column
                        prop="updated_at"
                        :label="columns['updated_at']['title']"
                        v-if="columns['updated_at']['show']"
                        width="160">
            </el-table-column>
            <el-table-column label="操作" fixed="right" width="300">
                <template slot-scope="scope">
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['user-delete']"
                            @click="changeState(scope.$index, scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['user-delete']"
                            @click="edituser(scope.$index, scope.row)">编辑</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['user-delete']"
                            @click="reset_password(scope.$index, scope.row)">密码重置</el-button>
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
        <el-dialog title="修改用户信息" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="用户名" prop="name">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="昵称" prop="nickname">
                    <el-input v-model="form.nickname"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="邮箱" prop="email">
                    <el-input v-model="form.email"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="电话" prop="phone">
                    <el-input v-model="form.phone"></el-input>
                </el-form-item>
                <!--<el-form-item :label-width="formLabelWidth" label="状态" prop="phone">-->
                    <!--<el-switch v-model="form.state">启用</el-switch>-->
                <!--</el-form-item>-->
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="submitForm()">{{ form.id?"更新":"添加" }}</el-button>
                    </el-col>
                </el-form-item>
            </el-form>
        </el-dialog>
        <el-dialog title="修改用户密码" :visible.sync="dialog_password_visible">
            <el-form ref="form-password">
                <el-form-item :label-width="formLabelWidth" label="用户名" prop="name">
                    <el-input disabled v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="密码" prop="password">
                    <el-input v-model="pwd"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="4" :span="8">
                        <el-button type="primary" @click="resetPassword()">更新</el-button>
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
        name: "user",
        computed: {
            ...mapState({
                'users': state=>state.user.users,
                'columns': state=>state.user.columns,
                'loading': state=>state.user.loading,
                'current_page': state=>state.user.current_page,
                'per_page': state=>state.user.per_page,
                'total': state=>state.user.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.user.form,
                'rules': state=>state.user.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.user.row_index,
            }),
            pwd: {
                get() {
                    return this.$store.state.user.pwd
                },
                set(value) {
                    this.$store.commit('user/set_pwd', value)
                }
            },
            dialog_visible: {
                get() {
                    return this.$store.state.user.dialog_visible
                },
                set(value) {
                    this.$store.commit('user/set_dialog_visible', value)
                }
            },
            dialog_password_visible: {
                get() {
                    return this.$store.state.user.dialog_password_visible
                },
                set(value) {
                    this.$store.commit('user/set_dialog_password_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.user.show_type
                },
                set(value) {
                    this.$store.commit('user/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "user/set_current_page",
                'set_per_page': "user/set_per_page",
                'filte_data': "user/filte_data",
                'set_dialog_visible': "user/set_dialog_visible",
                'set_dialog_password_visible': "user/set_dialog_password_visible",
                'set_form': "user/set_form",
                'set_row_index': "user/set_row_index",
                'set_form_value': "user/set_form_value",
                'update_user_list_by_index': "user/update_user_list_by_index",
                'append_user_list': "user/append_user_list"
            }),
            ...mapActions({
                'get_users': 'user/get_users',
                'get_user': "user/get_user",
                'add_or_update_user': "user/add_or_update_user",
                'set_user_state': "user/set_user_state",
                'update_password': "user/update_password"
            }),
            edituser: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            reset_password: function(index, row) {
                this.set_form(row);
                this.set_dialog_password_visible(true);
            },
            resetPassword: function() {
                if(!this.pwd) {
                    this.$message.error('请输入密码');
                    return
                }

                const _this = this;
                this.update_password().then(result=> {
                    _this.$message.success("密码重置成功");
                });

            },
            changeState: function (index, row) {
                const _this = this;
                this.set_user_state({id:row.id, st:1-row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    _this.update_user_list_by_index({
                        index: index,
                        key: 'state',
                        value: 1-row.state == 1
                    })
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_user(this.form).then(function(result){
                            if(_this.form.id) {
                                for(let key in _this.form) {
                                    _this.update_user_list_by_index({
                                        index: _this.row_index,
                                        key: key,
                                        value: _this.form[key]
                                    })
                                }
                                _this.$message.success("更新成功");
                            }
                            else {
                                _this.set_form_value({key: 'id', value: result.id});
                                _this.append_user_list(_this.form);
                                _this.$message.success("添加成功");
                            }

                            _this.filte_data();
                            _this.set_dialog_visible(false);
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
            changeTableSort: function(column) {
                this.$store.commit("user/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted() {
            const _this = this;
            if(this.users.length === 0) {
                this.get_users().then(result=> {
                    _this.$message.success('成功获取用户列表！');
                })
            }
            else {
                _this.$message.success('成功获取用户列表！');
            }
        }
    }
</script>

<style scoped>
</style>