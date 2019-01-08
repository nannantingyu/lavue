<template>
    <div>
        <el-container>
            <el-header>
                <h5>jujin8财经日历</h5>
                <el-button type="primary" icon="el-icon-plus"  @click="addData" :disabled="!user_module_permission['calendar-delete']">添加日历数据</el-button>
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
                <!--<el-radio-group v-model="radio" style="float: right;padding-bottom: 10px" @change="filterData">-->
                    <!--<el-radio-button label="全部"></el-radio-button>-->
                    <!--<el-radio-button label="未处理"></el-radio-button>-->
                    <!--<el-radio-button label="已处理"></el-radio-button>-->
                <!--</el-radio-group>-->

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
                            prop="quota_name"
                            min-width="150"
                            :label="columns['quota_name']['title']"
                            v-if="columns['quota_name']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="importance"
                            min-width="50"
                            :label="columns['importance']['title']"
                            v-if="columns['importance']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="former_value"
                            min-width="50"
                            :label="columns['former_value']['title']"
                            v-if="columns['former_value']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="predicted_value"
                            min-width="50"
                            :label="columns['predicted_value']['title']"
                            v-if="columns['predicted_value']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="published_value"
                            min-width="50"
                            :label="columns['published_value']['title']"
                            v-if="columns['published_value']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="influence"
                            width="100"
                            :label="columns['influence']['title']"
                            v-if="columns['influence']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="source_id"
                            min-width="50"
                            :label="columns['source_id']['title']"
                            v-if="columns['source_id']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="dataname"
                            min-width="150"
                            :label="columns['dataname']['title']"
                            v-if="columns['dataname']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="datename"
                            min-width="50"
                            :label="columns['datename']['title']"
                            v-if="columns['datename']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="dataname_id"
                            min-width="50"
                            :label="columns['dataname_id']['title']"
                            v-if="columns['dataname_id']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="unit"
                            min-width="50"
                            :label="columns['unit']['title']"
                            v-if="columns['unit']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="created_at"
                            :label="columns['created_at']['title']"
                            v-if="columns['created_at']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="updated_at"
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
                                    :disabled="!user_module_permission['calendar-delete']"
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
                <el-dialog title="编辑财经日历信息" :visible.sync="dialogFormVisible">
                    <el-form :model="form" :rules="rules" ref="form">
                        <el-form-item label="ID" :label-width="formLabelWidth">
                            <el-input v-model="form.id" disabled="disabled" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="国家" :label-width="formLabelWidth" prop="country">
                            <el-input v-model="form.country" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="指标名称" :label-width="formLabelWidth" prop="quota_name">
                            <el-input v-model="form.quota_name" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="前值" :label-width="formLabelWidth" prop="former_value">
                            <el-input v-model="form.former_value" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="预测值" :label-width="formLabelWidth" prop="predicted_value">
                            <el-input v-model="form.predicted_value" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="发布值" :label-width="formLabelWidth" prop="published_value">
                            <el-input v-model="form.published_value" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="影响" :label-width="formLabelWidth" prop="influence">
                            <el-input v-model="form.influence" auto-complete="off"></el-input>
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
                        <el-form-item label="datename" :label-width="formLabelWidth" prop="datename">
                            <el-input v-model="form.datename" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="数据名称" :label-width="formLabelWidth" prop="dataname">
                            <el-input v-model="form.dataname" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="dataname_id" :label-width="formLabelWidth" prop="dataname_id">
                            <el-input v-model="form.dataname_id" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="单位" :label-width="formLabelWidth" prop="unit">
                            <el-input v-model="form.unit" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="发布时间" :label-width="formLabelWidth" prop="publish_time">
                            <el-date-picker
                                    value-format="yyyy-MM-dd HH:mm:ss"
                                    v-model="form.publish_time"
                                    type="datetime"
                                    placeholder="选择日期时间"
                            >
                            </el-date-picker>
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
    import {Table, TableColumn, Pagination, Loading, Radio,RadioGroup,RadioButton,Popover,  Dialog, FormItem, Input, Select, Option, Switch, DatePicker,Form} from 'element-ui'
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
    export default {
        name: "calendar",
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
                    influence: [
                        { required: true, message: '请输入影响', trigger: 'blur' },
                    ],
                    quota_name: [
                        { required: true, message: '请输入指标名称', trigger: 'blur' },
                    ],
                    dataname: [
                        { required: true, message: '请输入指标名称', trigger: 'blur' },
                    ],
                    datename: [
                        { required: true, message: '请输入指标时间', trigger: 'blur' },
                    ],
                    dataname_id: [
                        { required: true, message: '请输入dataname_id', trigger: 'blur' },
                    ],
                    importance: [
                        { required: true, message: '重要性不能为空', trigger: 'blur' },
                    ],
                    source_id: [
                        { required: true, message: '请输入source_id', trigger: 'blur' },
                    ],
                    publish_time: [
                        { required: true, message: '请输入publish_time', trigger: 'blur' }
                    ],
                }
            }
        },

        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'columns': state=>state.calendar.columns,
                'lists': state=>state.calendar.lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'current_page':state=>state.calendar.currentPage,
                'total':state=>state.calendar.total
            })
        },
        methods:{
            ...mapMutations({
                'page_changeEvent': "calendar/set_current_page",
                'size_changeEvent': "calendar/set_page_size",
                // 'set_state':'calendar/set_state',
            }),
            ...mapActions({
                'get_lists': 'calendar/get_lists',
                'add_update':'calendar/add_update',
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
            //更改handle状态
            // changeState: function (row,isButton) {
            //     const _this = this;
            //     const obj=deepCopy(row);
            //     if(isButton){
            //         obj.is_handling=row.is_handling?0:1;
            //     }
            //     else{
            //         obj.is_handling=row.is_handling?1:0;
            //     }
            //     this.set_feed_state(obj).then(result=>{
            //         _this.$message.success("更新成功");
            //         this.get_feed_lists().then(result=> {
            //             _this.$message.success('成功更新意见反馈列表！');
            //         })
            //     });
            // },
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
            //筛选处理未处理
            // filterData:function (state) {
            //     let _this=this;
            //     let s=-1;
            //     switch (state){
            //         case '全部':s=-1;break;
            //         case '未处理':s=0;break;
            //         case '已处理':s=1;break;
            //         default:s=-1;
            //     }
            //     this.loading=true;
            //     this.set_feed_stateM(s);
            //     this.get_feed_lists().then(result=> {
            //         _this.loading=false;
            //     });
            //
            // }
        },
        mounted(){
            //获取意见反馈列表
            var _this=this;
            this.get_lists().then(result=> {
                _this.$message.success('成功获取财经日历列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }
    }
</script>

<style scoped>

</style>