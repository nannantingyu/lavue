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
                <el-button @click="add_block_coin">添加区块链币种</el-button>
            </el-col>
        </el-row>
        <el-table :data="block_coin_lists.slice((current_page-1)*per_page, current_page*per_page)"
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
                    prop="coin_id"
                    sortable
                    :label="columns['coin_id']['title']"
                    v-if="columns['coin_id']['show']" width="200">
            </el-table-column>
            <el-table-column
                    prop="coin_name"
                    :label="columns['coin_name']['title']"
                    v-if="columns['coin_name']['show']"
                    width="*">
            </el-table-column>
            <el-table-column
                    prop="sequence"
                    :label="columns['sequence']['title']"
                    v-if="columns['sequence']['show']"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="symble"
                    :label="columns['symble']['title']"
                    v-if="columns['symble']['show']"
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
                            :disabled="!user_module_permission['block-coin-delete']"
                            @click="setState(scope.$index, scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                            size="mini"
                            :type="scope.row.state?'success':'danger'"
                            :disabled="!user_module_permission['block-coin-delete']"
                            @click="editblock_coin(scope.$index, scope.row)">编辑</el-button>
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
        <el-dialog title="添加区块链币种" :visible.sync="dialog_visible">
            <el-form ref="form" :model="form" :rules="rules">
                <el-form-item :label-width="formLabelWidth" label="币代号" prop="coin_id">
                    <el-input v-model="form.coin_id" placeholder="bitcoin"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="币名称" prop="coin_name">
                    <el-input v-model="form.coin_name" placeholder="比特币"></el-input>
                </el-form-item>
                <el-form-item :label-width="formLabelWidth" label="缩写" prop="symble">
                    <el-input v-model="form.symble" placeholder="BTC"></el-input>
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
        name: "block_coin",
        computed: {
            ...mapState({
                'block_coin_lists': state=>state.block_coin.block_coin_lists,
                'columns': state=>state.block_coin.columns,
                'loading': state=>state.block_coin.loading,
                'current_page': state=>state.block_coin.current_page,
                'per_page': state=>state.block_coin.per_page,
                'total': state=>state.block_coin.total,
                'user_module_permission': state=>state.user.user_module_permission,
                'form': state=>state.block_coin.form,
                'rules': state=>state.block_coin.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'row_index': state=>state.block_coin.row_index,
            }),
            dialog_visible: {
                get() {
                    return this.$store.state.block_coin.dialog_visible
                },
                set(value) {
                    this.$store.commit('block_coin/set_dialog_visible', value)
                }
            },
            show_type: {
                get() {
                    return this.$store.state.block_coin.show_type
                },
                set(value) {
                    this.$store.commit('block_coin/set_show_type', value)
                    this.filte_data()
                }
            },
        },
        methods: {
            ...mapMutations({
                'set_current_page': "block_coin/set_current_page",
                'set_per_page': "block_coin/set_per_page",
                'filte_data': "block_coin/filte_data",
                'set_dialog_visible': "block_coin/set_dialog_visible",
                'set_form': "block_coin/set_form",
                'set_row_index': "block_coin/set_row_index",
                'set_form_value': "block_coin/set_form_value",
                'update_block_coin_list_by_index': "block_coin/update_block_coin_list_by_index",
                'append_block_coin_list': "block_coin/append_block_coin_list",
                'set_default_form': "block_coin/set_default_form"
            }),
            ...mapActions({
                'get_block_coin_lists': 'block_coin/get_block_coin_lists',
                'get_block_coin': "block_coin/get_block_coin",
                'add_or_update_block_coin': "block_coin/add_or_update_block_coin",
                'set_block_coin_state': "block_coin/set_block_coin_state"
            }),
            editblock_coin: function(index, row) {
                this.set_form(row);
                this.set_row_index(index);
                this.set_dialog_visible(true);
            },
            changeTableSort: function(column) {
                this.$store.commit("block_coin/sort_data", {column:column['prop'], order: column['order']})
            },
            changeState: function (row) {
                const _this = this;
                this.set_block_coin_state({id:row.id, state:row.state}).then(result=>{
                    let message = row.state?"上线成功":"下线成功";
                    _this.filte_data()
                    _this.$message.success(message);
                });
            },
            add_block_coin: function() {
                this.set_default_form();
                this.set_dialog_visible(true);
            },
            submitForm: function () {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_block_coin(this.form)
                            .then(function(result){
                                if(_this.form.id) {
                                    for(let key in _this.form) {
                                        _this.update_block_coin_list_by_index({
                                            index: _this.row_index,
                                            key: key,
                                            value: _this.form[key]
                                        })
                                    }
                                    _this.$message.success("更新成功");
                                }
                                else {
                                    _this.set_form_value({key: 'id', value: result.id});
                                    _this.append_block_coin_list(_this.form);
                                    _this.$message.success("添加成功");
                                }

                                _this.filte_data();
                                _this.set_dialog_visible(false);
                            }).catch(e=> {
                            _this.$message.error(e);
                            });
                    else _this.$message.error('请填写完整的信息！');
                });
            }
        },
        mounted() {
            const _this = this;
            this.get_block_coin_lists().then(result=> {
                _this.$message.success('成功获取区块链币种！');
            })
        }
    }
</script>

<style scoped>
</style>