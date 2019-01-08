<template>
    <div>
        <el-row>
            <el-col :span="3">
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
            <el-col :span="8">
                <el-radio-group v-model="radio"  @change="filterData">
                    <el-radio-button label="全部"></el-radio-button>
                    <el-radio-button label="已上线"></el-radio-button>
                    <el-radio-button label="已下线"></el-radio-button>
                </el-radio-group>
            </el-col>
            <el-col :span="3" :offset="8">
                <el-button type="primary" icon="el-icon-plus" @click="addData">添加工具</el-button>
            </el-col>
        </el-row>
        <el-table
            :data="lists.slice((current_page-1)*per_page, current_page*per_page)"
            @sort-change="changeTableSort"
            v-loading="loading">
            <el-table-column
                prop="id"
                sortable
                :label="columns['id']['title']"
                v-if="columns['id']['show']"
                min-width="50">
            </el-table-column>
            <el-table-column
                prop="title"
                sortable
                :label="columns['title']['title']"
                v-if="columns['title']['show']"
                min-width="100">
            </el-table-column>
            <el-table-column
                prop="description"
                :label="columns['description']['title']"
                v-if="columns['description']['show']"
                min-width="150">
            </el-table-column>
            <el-table-column
                prop="image"
                :label="columns['image']['title']"
                v-if="columns['image']['show']"
                min-width="200">
                <template slot-scope="scope">
                    <img width="160" :src="transfer(scope.row.image)" :alt="scope.row.image">
                </template>
            </el-table-column>
            <el-table-column
                prop="tag"
                :label="columns['tag']['title']"
                v-if="columns['tag']['show']"
                min-width="100">
            </el-table-column>
            <el-table-column
                prop="url"
                :label="columns['url']['title']"
                v-if="columns['url']['show']"
                min-width="100">
            </el-table-column>
            <el-table-column
                prop="state"
                :label="columns['state']['title']"
                v-if="columns['state']['show']"
                min-width="100">
                <template slot-scope="scope">
                    <el-switch v-model="scope.row.state"
                               :disabled="!user_module_permission['tools-delete']"
                               @change="changeState(scope.row,'switch')"></el-switch>
                </template>
            </el-table-column>
            <el-table-column
                prop="sequence"
                sortable
                :label="columns['sequence']['title']"
                v-if="columns['sequence']['show']"
                min-width="100">
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
                        :disabled="!user_module_permission['tools-delete']"
                        @click="changeState(scope.row)">{{scope.row.state==1?"下线":"上线"}}</el-button>
                    <el-button
                        size="mini"
                        :type="scope.row.state?'success':'danger'"
                        :disabled="!user_module_permission['tools-delete']"
                        @click="edit_row(scope.row)">编辑</el-button>
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
        <el-dialog title="编辑投资工具" :visible.sync="dialogFormVisible">
            <el-form :model="form" :rules="rules" ref="form">
                <el-form-item label="ID" :label-width="formLabelWidth">
                    <el-input v-model="form.id" disabled="disabled" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="标题" :label-width="formLabelWidth" prop="title">
                    <el-input v-model="form.title" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="描述" :label-width="formLabelWidth" prop="description">
                    <el-input v-model="form.description" auto-complete="off" type="textarea"
                              :rows="3"></el-input>
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
                <el-form-item label="标签" :label-width="formLabelWidth" prop="tag">
                    <el-input v-model="form.tag" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="url" :label-width="formLabelWidth" prop="url">
                    <el-input v-model="form.url" auto-complete="off"></el-input>
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
        name: "tools",
        data() {
            return {
                form: {}
            }
        },
        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'columns': state=>state.tools.columns,
                'lists': state=>state.tools.lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'total':state=>state.tools.total,
                'current_page': state=>state.tools.current_page,
                'per_page': state=>state.tools.per_page,
                loading: state=>state.tools.loading,
                rules: state=>state.tools.rules,
            }),
            dialogFormVisible: {
                get() {
                    return this.$store.state.tools.dialogFormVisible
                },
                set(value) {
                    this.$store.commit('tools/set_dialogFormVisible', value)
                }
            },
            radio: {
                get() {
                    return this.$store.state.tools.radio
                },
                set(value) {
                    this.$store.commit('tools/set_radio', value)
                }
            },
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
                'set_current_page': "tools/set_current_page",
                'set_per_page': "tools/set_per_page",
                'set_state': 'tools/set_state',
                'filter_data': "tools/filter_data"
            }),
            ...mapActions({
                'get_lists': 'tools/get_lists',
                'add_update':'tools/add_update'
            }),
            addData:function(){
                this.form={id:"自动填充"};
                this.dialogFormVisible = true;
            },
            edit_row:function (row) {
                let obj=deepCopy(row);
                this.form=obj;
                this.dialogFormVisible = true;
            },
            submitFn:function(obj){
                this.add_update(obj).then(result=>{
                    this.$message.success('更新成功！');
                    this.get_lists().then(result=> {
                        this.filterData(this.radio);
                        this.$message.success('已更新列表！');
                    }).catch((ErrMsg)=>{
                        this.$message.error('刷新数据失败，请刷新此页！');
                        //获取数据失败时的处理逻辑
                    })
                });
            },
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
                        return false;
                    }
                });
            },
            changeState: function (row,s) {
                let obj=deepCopy(row);
                if(s){
                    obj.state=row.state?1:0;
                }else{
                    obj.state=row.state?0:1;
                }

                this.submitFn(obj);
            },
            filterData:function (state) {
                let _this=this;
                let param="";
                switch (state){
                    case "全部":param=0;break;
                    case "已上线":param=1;break;
                    case "已下线":param=2;break;
                }
                this.filter_data(param);

            },
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
            changeTableSort: function(column) {
                this.$store.commit("tools/sort_data", {column:column['prop'], order: column['order']})
            },
        },
        mounted(){
            var _this = this;
            this.get_lists().then(result=> {
                _this.$message.success('成功获取投资工具列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
            })
        }

    }
</script>

<style scoped>
</style>