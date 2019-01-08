<template>
    <div>
        <el-header>
            <h5>jujin8待审核文章管理</h5>
            <div style="overflow: hidden">
                <template>
                    <el-select v-model="optionValue" placeholder="请选择网站来源" value-key="site" filterable
                               style="float: right;" @change="changeSite">
                        <el-option
                                v-for="(item,index) in options"
                                :key="'option'+index"
                                :label="item.site+'（'+item.time+'）'"
                                :value="item">
                        </el-option>
                    </el-select>
                </template>
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
                <el-button type="success" round @click="updateMany()" :disabled="!user_module_permission['article-filter-delete']">批量审核通过</el-button>
            </div>
        </el-header>
        <el-main>
            <el-table
                    :data="lists"
                    v-loading="loading"
                    @selection-change="handleSelectionChange"
                    border
                    style="width: 100%">
                <el-table-column
                        type="selection"
                        width="*">
                </el-table-column>
                <el-table-column
                        prop="id"
                        sortable
                        :label="columns['id']['title']"
                        v-if="columns['id']['show']"
                        width="80">
                </el-table-column>
                <el-table-column
                        prop="title"
                        :label="columns['title']['title']"
                        v-if="columns['title']['show']" min-width="200">
                    <!--<template slot-scope="scope">-->
                        <!--<a target="_blank" v-bind:href="scope.row.url">{{ scope.row.title }}</a>-->
                    <!--</template>-->
                </el-table-column>
                <el-table-column
                        prop="image"
                        :label="columns['image']['title']"
                        v-if="columns['image']['show']"
                        min-width="140">
                    <template slot-scope="scope">
                        <img width="140" :src="transfer(scope.row.image)" :alt="scope.row.image">
                    </template>
                </el-table-column>
                <el-table-column
                        prop="state"
                        sortable
                        :label="columns['state']['title']"
                        v-if="columns['state']['show']"
                        min-width="80">
                    <template slot-scope="scope">
                        <el-switch
                                :disabled="!user_module_permission['article-need-look-delete']"
                                v-model="scope.row.state"
                                @change="changeState(scope.$index, scope.row,scope.row.state)"></el-switch>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="source_type"
                        :label="columns['source_type']['title']"
                        v-if="columns['source_type']['show']"
                        min-width="80">
                </el-table-column>
                <el-table-column
                        prop="source_url"
                        :label="columns['source_url']['title']"
                        v-if="columns['source_url']['show']"
                        min-width="80">
                    <template slot-scope="scope">
                        <a target="_blank" v-bind:href="scope.row.source_url">原文链接</a>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="source_site"
                        :label="columns['source_site']['title']"
                        v-if="columns['source_site']['show']"
                        min-width="80">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        sortable
                        :label="columns['created_at']['title']"
                        v-if="columns['created_at']['show']"
                        min-width="100">
                </el-table-column>
                <el-table-column
                        prop="updated_at"
                        sortable
                        :label="columns['updated_at']['title']"
                        v-if="columns['updated_at']['show']"
                        min-width="120">
                </el-table-column>
                <el-table-column prop="author"
                                 :label="columns['author']['title']"
                                 v-if="columns['author']['show']" min-width="80">
                </el-table-column>
                <el-table-column
                        prop="publish_time"
                        sortable
                        label="发布时间"
                        min-width="180">
                </el-table-column>

                <el-table-column label="操作" fixed="right" min-width="200">
                    <template slot-scope="scope">
                        <el-button
                                size="mini"
                                :type="scope.row.state==0?'success':'info'"
                                :disabled="!user_module_permission['article-need-look-delete']||scope.row.state==1"
                                @click="changeState(scope.$index, scope.row,1)">{{scope.row.state==0?"审核通过":"已通过"}}
                        </el-button>
                        <router-link :class="'router-button'" :to="'/article-edit/'+scope.row.id">
                            <el-button type="primary" size="mini">编辑</el-button></router-link>
                        <!--<el-button-->
                                <!--size="mini"-->
                                <!--type="danger"-->
                                <!--:disabled="!user_module_permission['article-need-look-delete']"-->
                                <!--@click="drop_article(scope.$index, scope.row)">删除-->
                        <!--</el-button>-->
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
                    style="padding-top:20px"
            >
            </el-pagination>
        </el-main>
    </div>
</template>

<script>
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'
    import {
        Table,
        TableColumn,
        Radio,
        RadioGroup,
        RadioButton,
        Select,
        Option,
        Pagination, Loading, Popover, Switch, Input, MessageBox
    } from 'element-ui';

    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Radio);
    Vue.use(RadioGroup);
    Vue.use(RadioButton);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(Popover);
    Vue.use(Switch);
    Vue.use(Input);
    Vue.use(Loading);
    Vue.use(Pagination);
    export default {
        name: "article-need-look",
        data() {
            return {
                loading: false,
                radio: "全部",
                optionValue: "",
                multipleSelection:[]
            }
        },
        computed: {
            ...mapState(['formLabelWidth']),
            ...mapState({
                'options': state => state.article_need_look.options,
                'columns': state => state.article_need_look.columns,
                'lists': state => state.article_need_look.lists,
                'current_page': state => state.article_need_look.current_page,
                'total': state => state.article_need_look.total,
                'user_module_permission': state => state.user.user_module_permission,
            }),
            ...mapGetters({
                'type_filter': 'article/type_filter'
            }),
        },
        methods: {
            ...mapMutations({
                'page_changeEvent': "article_need_look/set_current_page",
                'size_changeEvent': "article_need_look/set_page_size",
                'set_site_info': "article_need_look/set_site_info",
                'set_temp_state':"article_need_look/set_temp_state",
            }),
            ...mapActions({
                'get_options': 'article_need_look/get_options',
                'get_lists': 'article_need_look/get_lists',
                'set_article_state': 'article_need_look/set_article_state',
                'delete_article': "article_need_look/delete_article",
                'update_many_state':'article_need_look/updateMany'
            }),
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            changeState: function(index, row ,state) {
                state==0?state=0:state=1;
                this.set_article_state({id: row.id, state:state, index: index})
                    .then(()=>{
                        this.$message.success("更新成功");
                        this.get_lists().then(result => {
                            this.$message.success("未审核文章列表获取成功")
                        });
                    }).catch(()=>{
                    this.$message.error("更新失败");
                })
            },
            updateMany(){
                let idStr="";
                if(this.multipleSelection.length==0){
                    this.$message.warning("请选择审核条目");
                    return false;
                }
                this.multipleSelection.map(item=>{
                    idStr+=item.id+","
                })
                idStr=idStr.slice(0,idStr.length-1);
                this.update_many_state(idStr).then(result=>{
                    this.$message.success("更新成功");
                    this.get_lists().then(result => {
                        this.$message.success("未审核文章列表获取成功")
                    });
                });
            },
            drop_article: function (index, row) {
                const _this = this;
                MessageBox.confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    _this.delete_article({id: row.id, index: index}).then(result => {
                        _this.$message.success("删除成功");
                    })
                }).catch(() => {
                    _this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            changeSite: function () {
                this.set_site_info(this.optionValue)
                this.get_lists().then(result => {
                    this.$message.success("未审核文章列表获取成功")
                });
            },
            //下一页上一页
            page_change: function (state) {
                let _this = this;
                this.loading = true;
                this.page_changeEvent(state);
                this.get_lists().then(result => {
                    _this.loading = false;
                });
            },
            //分页size
            size_change: function (state) {
                let _this = this;
                this.loading = true;
                this.size_changeEvent(state);
                this.get_lists().then(result => {
                    _this.loading = false;
                });
            },
            transfer: function (img) {
                return img ? 'http://images.jujin8.com' + img.replace('/uploads/crawler', '/uploads') : ''
            }
        },
        mounted() {
            this.get_options().then(result => {
                this.optionValue = result[0];
                this.set_site_info(result[0]);
                this.get_lists().then(result => {
                    this.$message.success("未审核文章列表获取成功")
                }).catch(msg=>{
                    this.$message.error(msg+"错误")
                });
            });

        }
    }
</script>

<style scoped>

</style>