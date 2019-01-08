<template>
    <div>
        <el-header>
            <h5>jujin8文章来源审核开关</h5>
            <div style="overflow: hidden">
                <el-radio-group v-model="radio" style="float: right;" @change="filterData">
                    <el-radio-button label="全部"></el-radio-button>
                    <el-radio-button label="自动审核"></el-radio-button>
                    <el-radio-button label="人工审核"></el-radio-button>
                </el-radio-group>
            </div>

        </el-header>
        <el-main>
            <template>
                <el-table
                        v-loading="loading"
                        ref="multipleTable"
                        :data="lists"
                        tooltip-effect="dark"
                        style="width: 100%"
                        border
                        stripe
                        height="480"
                        :default-sort = "{prop: 'time'}"
                        @selection-change="handleSelectionChange">
                    <el-table-column
                            type="selection"
                            width="*">
                    </el-table-column>
                    <el-table-column
                            sortable
                            prop="time"
                            :label="columns['time']['title']"
                            v-if="columns['time']['show']"
                            min-width="150">
                    </el-table-column>
                    <el-table-column
                            prop="source_site"
                            :label="columns['site']['title']"
                            v-if="columns['site']['show']"
                            min-width="200">
                    </el-table-column>
                    <el-table-column
                            :label="columns['state']['title']"
                            v-if="columns['state']['show']"
                            min-width="120">
                        <template slot-scope="scope">
                            <div :class="scope.row.state?'red':'green'">
                                {{scope.row.state?"人工审核":"自动审核"}}
                            </div>

                        </template>
                    </el-table-column>
                    <el-table-column
                            label="操作" min-width="200" fixed="right">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    :type="scope.row.state?'success':'danger'"
                                    :disabled="!user_module_permission['article-filter-delete']"
                                    @click="changeState(scope.row,scope.$index)">{{scope.row.state==1?"修改为自动审核":"修改为人工审核"}}</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </template>
            <el-row style="margin-top: 20px">
            <el-button type="danger" round @click="updateMany(0)" :disabled="!user_module_permission['article-filter-delete']">批量修改为人工审核</el-button>
            <el-button type="success" round @click="updateMany(1)" :disabled="!user_module_permission['article-filter-delete']">批量修改为自动审核</el-button>
             </el-row>
        </el-main>
    </div>
</template>

<script>
    // import {check_integer_factory,deepCopy} from "../plugin/tool";
    import {dateFtt,deepCopy} from "../plugin/tool";
    import {
        Table,
        TableColumn,
        Radio,
        RadioGroup,
        RadioButton,
        Loading
    } from 'element-ui';

    Vue.use(Table);
    Vue.use(TableColumn);
    Vue.use(Radio);
    Vue.use(RadioGroup);
    Vue.use(RadioButton);
    Vue.use(Loading);
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'

    export default {
        name: "article-filter",
        data() {
            return {
                radio:"全部",
                multipleSelection: [],
                loading:false
            }
        },
        computed: {
            ...mapState(['formLabelWidth']),
            ...mapState({
                'columns': state => state.article_filter.columns,
                'lists': state => state.article_filter.lists,
                'configValue':state => state.article_filter.configValue,
                'user_module_permission': state => state.user.user_module_permission
            })
        },
        methods: {
            ...mapMutations({
                // 'set_state':'menu/set_state',
                // 'set_feed_state': "menu/set_feed_state",
                'filter_data':"article_filter/filter_data"
            }),
            ...mapActions({
                'get_lists': 'article_filter/get_lists',
                'change_config':"article_filter/change_config",
                'filterData':"article_filter/filterData",
                // 'add_update':'menu/add_update'
            }),
            handleSelectionChange(val) {
                this.multipleSelection = val;
                // console.log(val);
            },
            changeState(row){
                //人工审核=》自动审核（修改config表中的数组）
                if(row.state==true){
                    //删除configValue项
                    let [...configValue]=this.configValue;
                    configValue.map((item,index)=>{
                       if(row.source_site==item.site){
                           configValue.splice(index,1);
                           this.change_config(configValue).then((result)=>{
                               this.$message.success('更新成功！');
                               this.filterData(this.radio);
                           }).catch((ErrMsg)=>{
                               this.$message.error('更新失败！');
                               this.get_lists();
                               this.filterData(this.radio);
                               //获取数据失败时的处理逻辑
                           });
                           return false;
                       }
                    })
                }
                else{
                    //自动审核=》人工审核
                    //添加
                    let obj={
                        site:row.source_site,
                        time:dateFtt("yyyy-MM-dd hh:mm:ss",new Date())
                    }
                    let [...configValue]=this.configValue;
                    configValue.push(obj);
                    this.change_config(configValue).then((result)=>{
                        this.$message.success('更新为人工审核成功！');
                        this.filterData(this.radio);
                    }).catch((ErrMsg)=>{
                        this.$message.error('更新失败！');
                        this.get_lists();
                        this.filterData(this.radio);
                        //获取数据失败时的处理逻辑
                    });
                }
            },
            filterData(state){
                let param="";
                switch (state){
                    case "全部":param=0;break;
                    case "自动审核":param=1;break;
                    case "人工审核":param=2;break;
                }

                this.filter_data(param);
            },
            updateMany(state){
                //state0 =》改为人工审核
                //state1 =》改为自动审核
                if(this.multipleSelection.length==0){
                    this.$message.warning('请选择更新的数据！');
                    return false;
                }
                let [...configValue]=this.configValue;
                this.multipleSelection.map(item=>{
                    if(state==0){
                       //添加配置项
                        let flag=true;
                        this.configValue.map(i=>{
                            if(item.source_site==i.site){
                                flag=false;
                                return false;
                            }
                        })
                        let obj={
                            site:item.source_site,
                            time:dateFtt("yyyy-MM-dd hh:mm:ss",new Date())
                        }
                        configValue.push(obj);
                    }else{
                        //删除配置项
                        configValue.map((i,index)=> {
                            if (item.source_site == i.site) {
                                configValue.splice(index, 1);
                            }
                        })

                    }
                })
                this.change_config(configValue).then((result)=>{
                    this.$message.success('批量更新成功！');
                    this.filterData(this.radio);
                }).catch((ErrMsg)=>{
                    this.$message.error('批量更新失败！');
                    this.get_lists();
                    this.filterData(this.radio);
                    //获取数据失败时的处理逻辑
                });

            }
        },
        mounted(){
            this.loading=true;
            this.get_lists().then(result=> {
                this.loading=false;
                this.$message.success('成功获取文章所有来源！');
            }).catch((ErrMsg)=>{
                this.$message.error('获取数据失败，请刷新此页！');
                //获取数据失败时的处理逻辑
            })
        }
    }

</script>

<style scoped>
.red{
    color: #f56c6c;
}
.green{
    color: #67c23a;
}
</style>