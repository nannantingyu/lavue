<template>
    <div>
        <el-container>
            <el-header>
                <h5>jujin8活动分类管理</h5>
                <el-button type="primary" icon="el-icon-plus"  @click="addData">添加分类</el-button>
                <el-radio-group v-model="radio" style="float: right;padding-bottom: 10px" @change="filterData">
                    <el-radio-button label="全部"></el-radio-button>
                    <el-radio-button label="已上线"></el-radio-button>
                    <el-radio-button label="已下线"></el-radio-button>
                </el-radio-group>
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
                            width="50">
                    </el-table-column>
                    <el-table-column
                            prop="pid"
                            :label="columns['pid']['title']"
                            v-if="columns['pid']['show']"
                            width="50">
                    </el-table-column>
                    <el-table-column
                            prop="name"
                            :label="columns['title']['title']"
                            v-if="columns['title']['show']"
                            width="100">
                    </el-table-column>
                    <el-table-column
                            prop="state"
                            :label="columns['state']['title']"
                            v-if="columns['state']['show']"
                            width="100">
                        <template slot-scope="scope">
                            <el-switch v-model="scope.row.state"
                                       :disabled="!user_module_permission['category-activity-delete']"
                                       @change="changeState(scope.row,'switch')"></el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="sequence"
                            :label="columns['sequence']['title']"
                            v-if="columns['sequence']['show']"
                            width="100">
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
                            label="操作"
                            fixed="right"
                            min-width="200">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    :type="scope.row.state?'success':'danger'"
                                    :disabled="!user_module_permission['category-activity-delete']"
                                    @click="changeState(scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                            <el-button
                                    size="mini"
                                    :type="scope.row.state?'success':'danger'"
                                    :disabled="!user_module_permission['category-activity-delete']"
                                    @click="edit_row(scope.row)">编辑</el-button>
                        </template>
                    </el-table-column>

                </el-table>
                <el-dialog title="编辑投资工具" :visible.sync="dialogFormVisible">
                    <el-form :model="form" :rules="rules" ref="form">
                        <el-form-item label="ID" :label-width="formLabelWidth">
                            <el-input v-model="form.id" disabled="disabled" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="PID" :label-width="formLabelWidth" prop="description">
                            <el-input v-model="form.pid" auto-complete="off" ></el-input>
                        </el-form-item>
                        <el-form-item label="标题" :label-width="formLabelWidth" prop="name">
                            <el-input v-model="form.name" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="顺序" :label-width="formLabelWidth" prop="sequence">
                            <el-input v-model="form.sequence" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="是否显示" :label-width="formLabelWidth">
                            <el-switch
                                    v-model="form.state">
                            </el-switch>
                            <!--<el-input v-model="form.state" auto-complete="off"></el-input>-->
                        </el-form-item>
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
    import {check_integer_factory,deepCopy} from "../plugin/tool";
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'
    import {Table, TableColumn, Pagination, Loading, Radio,RadioGroup,RadioButton,Button,Popover,  Dialog, FormItem, Input, Select, Option, Switch, DatePicker, Upload, Form} from 'element-ui'
    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Pagination);
    Vue.use(Loading);
    Vue.use(RadioGroup);
    Vue.use(Radio);
    Vue.use(RadioButton);
    Vue.use(Dialog);
    Vue.use(Form);
    Vue.use(Button);
    Vue.use(FormItem);
    Vue.use(Input);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(Switch);
    Vue.use(Upload);
    Vue.use(Popover);
    export default {
        name: "category-activity",
        data() {
            return {
                loading: false,
                radio:"全部",
                //编辑
                dialogFormVisible: false,
                form: {},
                rules: {
                    name: [
                        { required: true, message: '请输入标题', trigger: 'blur' },
                        { min: 2, max: 32, message: '标题长度在 3 到 64 个字符', trigger: 'blur' }
                    ],
                    pid:[
                        { required: true, message: '请输入PID', trigger: 'blur' },
                        { validator: check_integer_factory('PID为数字类型'), trigger: 'blur' }
                    ],
                    sequence: [
                        { required: true, message: '请输入顺序', trigger: 'blur' },
                        { validator: check_integer_factory('顺序为数字类型'), trigger: 'blur' }
                    ]
                }
            }
        },

        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'columns': state=>state.category_activity.columns,
                'lists': state=>state.category_activity.lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'current_page':state=>state.category_activity.currentPage,
                'total':state=>state.category_activity.total
            })
        },
        methods:{
            ...mapMutations({
                'set_state':'category_activity/set_state',
                'filter_data':"category_activity/filter_data"
            }),
            ...mapActions({
                'get_lists': 'category_activity/get_lists',
                'add_update':'category_activity/add_update'
            }),

            //添加
            addData:function(){
                this.form={id:"自动填充"};
                this.dialogFormVisible = true;
            },
            //编辑
            edit_row:function (row) {
                let obj=deepCopy(row);
                this.form=obj;
                console.log(this.form);
                this.dialogFormVisible = true;
            },
            submitFn:function(obj){
                this.add_update(obj).then(result=>{
                    this.$message.success('更新成功！');
                    this.get_lists().then(result=> {
                        this.filterData(this.radio);
                        this.$message.success('已更新列表！');
                    }).catch((ErrMsg)=>{
                        console.log(ErrMsg);
                        this.$message.error('刷新数据失败，请刷新此页！');
                        //获取数据失败时的处理逻辑
                    })

                });
            },
            //提交
            submitForm:function(){
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        let obj=deepCopy(_this.form);
                        _this.dialogFormVisible = false;
                        obj.state=obj.state?1:0;
                        obj.id=obj.id=='自动填充'?"":obj.id;
                        _this.submitFn(obj);

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            //开关、上线下线
            changeState: function (row,s) {
                let obj=deepCopy(row);
                if(s){
                    obj.state=row.state?1:0;
                }else{
                    obj.state=row.state?0:1;
                }
                this.submitFn(obj);
            },
            //筛选处理未处理
            filterData:function (state) {
                let _this=this;
                let param="";
                switch (state){
                    case "全部":param=0;break;
                    case "已上线":param=1;break;
                    case "已下线":param=2;break;
                }
                this.filter_data(param);

            }
        },
        mounted(){
            //获取列表
            var _this=this;
            this.get_lists().then(result=> {
                _this.$message.success('成功获取活动分类列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }

    }
</script>

<style scoped>

</style>