<template>
    <div>
        <el-container>
            <el-header>
                <h5>jujin8财经事件</h5>
                <el-button type="primary" icon="el-icon-plus" :disabled="!user_module_permission['event-delete']" @click="addData">添加财经事件</el-button>
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


            </el-header>
            <el-main>
                <el-table
                        :data="lists"
                        border
                        style="width: 100%"
                        v-loading="loading">
                    <el-table-column
                            prop="id"
                            :label="columns['id']['title']"
                            v-if="columns['id']['show']"
                            width="80">
                    </el-table-column>
                    <el-table-column
                            prop="country"
                            :label="columns['country']['title']"
                            v-if="columns['country']['show']"
                            width="80">
                    </el-table-column>
                    <el-table-column
                            prop="city"
                            min-width="150"
                            :label="columns['city']['title']"
                            v-if="columns['city']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="event"
                            min-width="150"
                            :label="columns['event']['title']"
                            v-if="columns['event']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="date"
                            min-width="150"
                            :label="columns['date']['title']"
                            v-if="columns['date']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="time"
                            min-width="150"
                            :label="columns['time']['title']"
                            v-if="columns['time']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="source_id"
                            min-width="150"
                            :label="columns['source_id']['title']"
                            v-if="columns['source_id']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="created_at"
                            min-width="150"
                            :label="columns['created_at']['title']"
                            v-if="columns['created_at']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="updated_at"
                            min-width="150"
                            :label="columns['updated_at']['title']"
                            v-if="columns['updated_at']['show']">
                    </el-table-column>
                    <el-table-column
                            fixed="right"
                            label="操作"
                            min-width="80">
                        <template slot-scope="scope">
                            <!--<el-button-->
                            <!--size="mini"-->
                            <!--:type="scope.row.state?'success':'danger'"-->
                            <!--:disabled="!user_module_permission['live-delete']"-->
                            <!--@click="changeState(scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>-->
                            <el-button
                                    size="mini"
                                    type="success"
                                    :disabled="!user_module_permission['event-delete']"
                                    @click="edit_row(scope.row)">编辑</el-button>
                        </template>
                    </el-table-column>

                </el-table>
                <el-pagination
                        background
                        @current-change="page_change"
                        @size-change="size_change"
                        :current-page="current_page"
                        layout="total, sizes, prev, pager, next, jumper"
                        :total="total"
                        style="margin-top: 40px"
                >
                </el-pagination>
                <el-dialog title="编辑财经事件信息" :visible.sync="dialogFormVisible">
                    <el-form :model="form" :rules="rules" ref="form">
                        <el-form-item label="ID" :label-width="formLabelWidth">
                            <el-input v-model="form.id" disabled="disabled" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="国家" :label-width="formLabelWidth" prop="country">
                            <el-input v-model="form.country" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="城市" :label-width="formLabelWidth" prop="city">
                            <el-input v-model="form.city" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="发布日期 " :label-width="formLabelWidth" prop="date">
                            <el-date-picker
                                    value-format="yyyy-MM-dd"
                                    v-model="form.date"
                                    type="date"
                                    placeholder="选择日期时间"
                            >
                            </el-date-picker>
                        </el-form-item>
                        <el-form-item label="时间" :label-width="formLabelWidth" prop="time">
                            <el-time-picker
                                    v-model="form.time"
                                    value-format="HH:mm:ss"
                                    placeholder="任意时间点">
                            </el-time-picker>
                            <!--<el-date-picker-->
                                    <!--value-format="yyyy-MM-dd HH:mm:ss"-->
                                    <!--v-model="form.time"-->
                                    <!--type="datetime"-->
                                    <!--placeholder="选择日期时间"-->
                            <!--&gt;-->
                            <!--</el-date-picker>-->
                        </el-form-item>
                        <el-form-item label="事件" :label-width="formLabelWidth" prop="event">
                            <el-input v-model="form.event" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="重要性" :label-width="formLabelWidth" prop="importance">
                            <template>
                                <el-radio v-model="form.importance" label="1">1</el-radio>
                                <el-radio v-model="form.importance" label="2">2</el-radio>
                                <el-radio v-model="form.importance" label="3">3</el-radio>
                                <el-radio v-model="form.importance" label="4">4</el-radio>
                                <el-radio v-model="form.importance" label="5">5</el-radio>
                            </template>
                        </el-form-item>
                        <el-form-item label="source_id" :label-width="formLabelWidth" prop="source_id">
                            <el-input v-model="form.source_id" auto-complete="off"></el-input>
                        </el-form-item>
                        <!--<el-form-item label="是否显示" :label-width="formLabelWidth">-->
                        <!--<el-switch-->
                        <!--v-model="form.state">-->
                        <!--</el-switch>-->
                        <!--&lt;!&ndash;<el-input v-model="form.state" auto-complete="off"></el-input>&ndash;&gt;-->
                        <!--</el-form-item>-->
                    </el-form>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="dialogFormVisible = false">取 消</el-button>
                        <el-button type="primary" @click="submitForm">确 定</el-button>
                    </div>
                </el-dialog>
            </el-main>
        </el-container>
    </div>
</template>

<script>
    import {deepCopy} from "../plugin/tool";
    import {mapState, mapActions, mapMutations} from 'vuex'
    import {Table, TableColumn, Pagination, Loading, Radio,RadioGroup,RadioButton,Popover,  Dialog, FormItem, Input, Select, Option, Switch, DatePicker,TimePicker,Form} from 'element-ui'
    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Pagination);
    Vue.use(Loading);
    Vue.use(RadioGroup);
    Vue.use(Radio);
    Vue.use(RadioButton);
    Vue.use(Dialog);
    Vue.use(Form);
    Vue.use(FormItem);
    Vue.use(Input);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(Switch);
    Vue.use(Popover);
    Vue.use(DatePicker);
    Vue.use(TimePicker);
    export default {
        name: "event",
        data() {
            return {
                loading: false,
                radio:"全部",
                //编辑
                dialogFormVisible: false,
                form: {},
                rules: {
                    title: [
                        { required: true, message: '请输入标题', trigger: 'blur' },
                    ],
                    country: [
                        { required: true, message: '请输入国家', trigger: 'blur' },
                    ],
                    city: [
                        { required: true, message: '请输入城市', trigger: 'blur' },
                    ],
                    event: [
                        { required: true, message: '请输入事件', trigger: 'blur' },
                    ],
                    date: [
                        { required: true, message: '请输入日期', trigger: 'blur' },
                    ],
                    importance: [
                        { required: true, message: '重要性不能为空', trigger: 'blur' },
                    ],
                    source_id: [
                        { required: true, message: '请输入source_id', trigger: 'blur' },
                    ],
                }
            }
        },

        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'columns': state=>state.event.columns,
                'lists': state=>state.event.lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'current_page':state=>state.event.currentPage,
                'total':state=>state.event.total
            })
        },
        methods:{
            ...mapMutations({
                'page_changeEvent': "event/set_current_page",
                'size_changeEvent': "event/set_page_size",
                // 'set_state':'calendar/set_state',
            }),
            ...mapActions({
                'get_lists': 'event/get_lists',
                'add_update':'event/add_update',
                // 'set_feed_state': "feedback/set_feed_state",
            }),
            //添加
            addData:function(){
                this.form={id:"自动填充"};
                this.dialogFormVisible = true;
            },
            //编辑
            edit_row:function (row) {
                // console.log(this.$refs['form'],"LLLLL")
                // this.$refs['form'].resetFields();
                let obj=deepCopy(row);
                this.form=obj;
                this.dialogFormVisible = true;
            },
            submitFn:function(obj){
                this.add_update(obj).then(result=>{
                    this.$message.success('更新成功！');
                    // this.get_lists().then(result=> {
                    this.get_lists();
                    this.$message.success('已更新列表！');
                    // }).catch((ErrMsg)=>{
                    //     console.log(ErrMsg);
                    //     this.$message.error('刷新数据失败，请刷新此页！');
                    //     //获取数据失败时的处理逻辑
                    // })

                });
            },
            //提交
            submitForm:function(){
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        let obj=deepCopy(_this.form);
                        _this.dialogFormVisible = false;
                        obj.id=obj.id=='自动填充'?"":obj.id;
                        _this.submitFn(obj);

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            //下一页上一页
            page_change:function (state) {
                let _this=this;
                this.loading=true;
                this.page_changeEvent(state);
                this.get_lists().then(result=> {
                    _this.loading=false;
                });
            },
            //分页size
            size_change:function (state) {
                let _this=this;
                this.loading=true;
                this.size_changeEvent(state);
                this.get_lists().then(result=> {
                    _this.loading=false;
                });
            },
        },
        mounted(){
            //获取列表
            var _this=this;
            this.get_lists().then(result=> {
                _this.$message.success('成功获取财经事件列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }
    }
</script>

<style scoped>

</style>