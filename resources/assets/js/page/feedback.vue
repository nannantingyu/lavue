<template>
    <div>
        <el-container>
            <el-header>
                <h5>jujin8意见反馈列表</h5>
                <el-button type="danger" round @click="updateMany(0)" :disabled="!user_module_permission['feedback-delete']">批量 修改为未处理</el-button>
                <el-button type="success" round @click="updateMany(1)" :disabled="!user_module_permission['feedback-delete']">批量修改为已处理</el-button>
                <el-radio-group v-model="radio" style="float: right;padding-bottom: 10px" @change="filterData">
                    <el-radio-button label="全部"></el-radio-button>
                    <el-radio-button label="未处理"></el-radio-button>
                    <el-radio-button label="已处理"></el-radio-button>
                </el-radio-group>

            </el-header>
            <el-main>
                <el-table
                        :data="feed_lists"
                        border
                        @selection-change="handleSelectionChange"
                        style="width: 100%"
                        v-loading="loading">
                    <el-table-column
                            type="selection"
                            width="55">
                    </el-table-column>
                    <el-table-column
                            prop="id"
                            :label="columns['id']['title']"
                            v-if="columns['id']['show']"
                            width="50">
                    </el-table-column>
                    <el-table-column
                            prop="phone"
                            :label="columns['phone']['title']"
                            v-if="columns['phone']['show']"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            prop="content"
                            :label="columns['content']['title']"
                            v-if="columns['content']['show']">
                    </el-table-column>
                    <el-table-column
                            prop="is_handling"
                            :label="columns['is_handling']['title']"
                            v-if="columns['is_handling']['show']"
                            width="150">
                        <template slot-scope="scope">
                            <el-switch v-model="scope.row.is_handling"
                                       :disabled="!user_module_permission['feedback-delete']"
                                       @change="changeState(scope.row)"></el-switch>
                        </template>
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
                            prop="ip"
                            :label="columns['ip']['title']"
                            v-if="columns['ip']['show']">
                    </el-table-column>
                    <el-table-column
                            label="操作"
                            fixed="right"
                            min-width="100">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    :type="scope.row.is_handling?'danger':'success'"
                                    :disabled="!user_module_permission['feedback-delete']"
                                    @click="changeState(scope.row,'button')">{{scope.row.is_handling==1?"未处理":"已处理"}}</el-button>
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
            </el-main>
        </el-container>
    </div>
</template>

<script>
    import {deepCopy} from "../plugin/tool";
    import {mapState, mapActions, mapMutations} from 'vuex'
    import {Table, TableColumn, Pagination, Loading, Radio,RadioGroup,RadioButton} from 'element-ui'
    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Pagination);
    Vue.use(Loading);
    Vue.use(RadioGroup);
    Vue.use(Radio);
    Vue.use(RadioButton);
    export default {
        name: "feedback",
        data() {
            return {
                loading: false,
                radio:"全部",
                multipleSelection:[]
            }
        },

        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'columns': state=>state.feedback.columns,
                'feed_lists': state=>state.feedback.feed_lists,
                'user_module_permission': state=>state.user.user_module_permission,
                'current_page':state=>state.feedback.currentPage,
                'total':state=>state.feedback.total
            })
        },
        methods:{
            ...mapMutations({
                'page_changeEvent': "feedback/set_current_page",
                'size_changeEvent': "feedback/set_page_size",
                'set_state':'feedback/set_state',
                'set_feed_stateM': "feedback/set_feed_state"
            }),
            ...mapActions({
                'get_feed_lists': 'feedback/get_feed_lists',
                'set_feed_state': "feedback/set_feed_state",
                'update_many_state':'feedback/updateMany'
            }),
            updateMany(state){
                let idStr="";
                if(this.multipleSelection.length==0){
                    this.$message.warning("请选择处理条目");
                    return false;
                }
                this.multipleSelection.map(item=>{
                    idStr+=item.id+","
                })
                idStr=idStr.slice(0,idStr.length-1);
                this.update_many_state({idStr,s:state}).then(result=>{
                    this.$message.success("更新成功");
                    this.get_feed_lists().then(result => {
                        this.$message.success("意见反馈列表更新成功")
                    });
                });
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            //更改handle状态
            changeState: function (row,isButton) {
                const _this = this;
                const obj=deepCopy(row);
                if(isButton){
                    obj.is_handling=row.is_handling?0:1;
                }
                else{
                    obj.is_handling=row.is_handling?1:0;
                }
                this.set_feed_state(obj).then(result=>{
                    _this.$message.success("更新成功");
                    this.get_feed_lists().then(result=> {
                        _this.$message.success('成功更新意见反馈列表！');
                    })
                });
            },
            //下一页上一页
            page_change:function (state) {
                let _this=this;
                this.loading=true;
                this.page_changeEvent(state);
                this.get_feed_lists().then(result=> {
                    _this.loading=false;
                });
            },
            //分页size
            size_change:function (state) {
                let _this=this;
                this.loading=true;
                this.size_changeEvent(state);
                this.get_feed_lists().then(result=> {
                    _this.loading=false;
                });
            },
            //筛选处理未处理
            filterData:function (state) {
                console.log(state);
                let _this=this;
                let s=-1;
                   switch (state){
                       case '全部':s=-1;break;
                       case '未处理':s=0;break;
                       case '已处理':s=1;break;
                       default:s=-1;
                   }
                this.loading=true;
                this.set_feed_stateM(s);
                this.get_feed_lists().then(result=> {
                    _this.loading=false;
                });

            }
        },
        mounted(){
            //获取意见反馈列表
            var _this=this;
            this.get_feed_lists().then(result=> {
                _this.$message.success('成功获取意见反馈列表！');
            }).catch((ErrMsg)=>{
                _this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }
    }
</script>

<style scoped>

</style>