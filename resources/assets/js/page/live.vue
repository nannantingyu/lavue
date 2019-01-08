<template>
    <div>
        <el-container>
            <el-header>
                <h5>jujin8快讯管理</h5>
                <el-button type="primary" icon="el-icon-plus"  @click="addData">添加快讯</el-button>
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
                <el-date-picker
                        v-model="dateRange"
                        type="daterange"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        value-format="yyyy-MM-dd"
                        @change="filterByDate"
                >
                </el-date-picker>
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
                            prop="title"
                            :label="columns['title']['title']"
                            v-if="columns['title']['show']"
                            width="150"
                    >
                    </el-table-column>
                    <el-table-column
                            prop="body"
                            :label="columns['body']['title']"
                            v-if="columns['body']['show']"
                            min-width="250"
                    >
                    </el-table-column>
                    <el-table-column
                            prop="image"
                            :label="columns['image']['title']"
                            v-if="columns['image']['show']"
                            width="200">
                        <template slot-scope="scope">
                            <img width="160" :src="transfer(scope.row.image)" :alt="scope.row.image">
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="importance"
                            :label="columns['importance']['title']"
                            v-if="columns['importance']['show']"
                            width="100"
                            sortable
                    >
                    </el-table-column>
                    <el-table-column
                            prop="source_site"
                            :label="columns['source_site']['title']"
                            v-if="columns['source_site']['show']"
                            width="100">
                    </el-table-column>
                    <el-table-column
                            prop="source_id"
                            :label="columns['source_id']['title']"
                            v-if="columns['source_id']['show']"
                            width="100">
                    </el-table-column>
                    <!--<el-table-column-->
                            <!--prop="type"-->
                            <!--:label="columns['type']['title']"-->
                            <!--v-if="columns['type']['show']"-->
                            <!--width="100">-->
                    <!--</el-table-column>-->
                    <el-table-column
                            prop="state"
                            :label="columns['state']['title']"
                            v-if="columns['state']['show']"
                            width="100">
                        <template slot-scope="scope">
                            <el-switch v-model="scope.row.state"
                                       :disabled="!user_module_permission['live-delete']"
                                       @change="changeState(scope.row,'switch')"></el-switch>
                        </template>
                    </el-table-column>

                    <el-table-column
                            prop="description"
                            :label="columns['description']['title']"
                            v-if="columns['description']['show']"
                            width="100">
                    </el-table-column>
                    <el-table-column
                            prop="publish_time"
                            :label="columns['publish_time']['title']"
                            v-if="columns['publish_time']['show']">
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
                            min-width="200">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    :type="scope.row.state?'success':'danger'"
                                    :disabled="!user_module_permission['live-delete']"
                                    @click="changeState(scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                            <el-button
                                    size="mini"
                                    :type="scope.row.state?'success':'danger'"
                                    :disabled="!user_module_permission['live-delete']"
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
                        v-if="pageshow"

                >
                </el-pagination>
                <el-dialog title="编辑快讯信息" :visible.sync="dialogFormVisible">
                    <el-form :model="form" :rules="rules" ref="form">
                        <el-form-item label="ID" :label-width="formLabelWidth">
                            <el-input v-model="form.id" disabled="disabled" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="标题" :label-width="formLabelWidth" prop="title">
                            <el-input v-model="form.title" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="描述" :label-width="formLabelWidth" prop="description">
                            <el-input v-model="form.description" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="内容" :label-width="formLabelWidth" prop="body">
                            <el-input v-model="form.body" auto-complete="off" type="textarea"
                                      :rows="4"></el-input>
                        </el-form-item>
                        <el-form-item label="图片" :label-width="formLabelWidth" prop="image">
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
                        <el-form-item label="重要性" :label-width="formLabelWidth" prop="importance">
                            <template>
                                <el-radio v-model="form.importance" label="0">0</el-radio>
                                <el-radio v-model="form.importance" label="1">1</el-radio>
                            </template>
                        </el-form-item>
                        <el-form-item label="源站" :label-width="formLabelWidth" prop="source_site">
                            <el-input v-model="form.source_site" auto-complete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="源站ID" :label-width="formLabelWidth" prop="source_id">
                            <el-input v-model="form.source_id" auto-complete="off"></el-input>
                        </el-form-item>
                        <!--<el-form-item label="分类" :label-width="formLabelWidth" prop="type">-->
                            <!--<el-input v-model="form.type" auto-complete="off"></el-input>-->
                        <!--</el-form-item>-->
                        <el-form-item label="发布时间" :label-width="formLabelWidth" prop="publish_time">
                            <el-date-picker
                                    value-format="yyyy-MM-dd HH:mm:ss"
                                    v-model="form.publish_time"
                                    type="datetime"
                                    placeholder="选择日期时间"
                            >
                            </el-date-picker>
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
    Vue.use(DatePicker);
    export default {
        name: "live",
        data() {
            return {
                pageshow:true,
                dateRange:"",
                loading: false,
                radio:"全部",
                //编辑
                dialogFormVisible: false,
                form: {},
                rules: {
                    title: [
                        { required: true, message: '请输入标题', trigger: 'blur' },
                    ],
                    body: [
                        { required: true, message: '请输入内容', trigger: 'blur' },
                    ],
                    importance: [
                        { required: true, message: '重要性不能为空', trigger: 'blur' },
                    ],
                    source_site: [
                        { required: true, message: '请输入源网站', trigger: 'blur' },
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
                'columns': state=>state.live.columns,
                'lists': state=>state.live.lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'current_page':state=>state.live.currentPage,
                'total':state=>state.live.total
            }),
            fileimgs:function() {
                let imgs = [];
                if(this.form.image){
                    imgs.push({url: 'http://images.jujin8.com'+this.form.image.replace('/uploads/crawler', '/uploads')});
                }
                return imgs;
            }
        },
        methods:{
            ...mapMutations({
                'page_changeEvent': "live/set_current_page",
                'size_changeEvent': "live/set_page_size",
                'set_state':'live/set_state',
                'filter_data':"live/filter_data",
                'set_date_range':"live/set_date_range"
            }),
            ...mapActions({
                'get_lists': 'live/get_lists',
                'add_update':'live/add_update',
                'get_lists_date':"live/get_lists_date"
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
                        this.filterData(this.radio);
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
                let param="";
                switch (state){
                    case "全部":param=-1;break;
                    case "已上线":param=1;break;
                    case "已下线":param=0;break;
                }
                //清空选择状态
                this.dateRange="";
                this.page_changeEvent(1);
                this.pageshow=true;

                this.set_state(param);
                this.get_lists();

            },
            //图片
            transfer: function(img) {
                return img?'http://images.jujin8.com'+img.replace('/uploads/crawler', '/uploads'):''
            },
            handleSuccess: function(response, file, fileList) {
                if(response.success) {
                    this.form.image=response.file_path
                }
            },
            handleRemove: function() {
                this.form.image=null
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
            //日期筛选
            filterByDate:function() {
                let _this=this;
                if(this.dateRange&&this.dateRange.length>0){
                    this.loading=true;
                    this.pageshow=false;
                    this.set_date_range(this.dateRange);
                    this.get_lists_date().then(result=>{
                        _this.loading=false;
                    });
                }
                else{
                    this.get_lists();
                    this.pageshow=true;
                }
             }
        },
        mounted(){
            //获取列表
            var _this=this;
            this.get_lists().then(result=> {
                _this.$message.success('成功获取快讯列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }

    }
</script>

<style scoped>

</style>