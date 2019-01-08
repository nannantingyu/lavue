<template>
    <div>
        <el-tree
                :data="modules"
                node-key="id"
                default-expand-all
                @node-drop="handleDrop"
                :draggable="user_module_permission['module-update']"
                :allow-drop="allowDrop"
                :allow-drag="allowDrag">
        <span class="custom-tree-node" slot-scope="{ node, data }">
            <span>{{ node.label }}</span>
            <span>
                <el-button
                    type="text"
                    size="mini"
                    :disabled="!user_module_permission['module-update']"
                    @click.stop="() => append_module(data, node)">
                    添加子模块
                </el-button>
                <el-button
                    type="text"
                    size="mini"
                    :disabled="!user_module_permission['module-update']"
                    @click.stop="() => edit_module(data, node)">
                    修改模块
                </el-button>
            </span>
        </span>
        </el-tree>
        <el-dialog :title="dialog_title" :visible.sync="dialog_visible">
            <addModule ref="dialog_banner_state"></addModule>
        </el-dialog>
    </div>
</template>

<script>
    import {Tree, Dialog} from 'element-ui'
    import addModule from './add-module'
    import {mapState, mapActions, mapMutations} from 'vuex'
    let id = 1000
    Vue.use(Tree)
    Vue.use(Dialog)

    export default {
        name: "module",
        components: { addModule },
        data() {
            return {
                defaultProps: {
                    children: 'children',
                    label: 'label'
                }
            };
        },
        computed: {
            ...mapState({
                'to_strings': state=> state.module.to_strings,
                'modules': state=> state.module.modules,
                'user_module_permission': state=> state.user.user_module_permission
            }),
            dialog_visible: {
                get: function() {
                    return this.$store.state.module.dialog_visible
                },
                set: function(value) {
                    this.$store.commit('module/set_dialog_visible', value)
                }
            },
            dialog_title: function () {
                return this.$store.state.module.form.id?'更新模块':'添加模块'
            }
        },
        created() {
            this.$store.dispatch('module/get_modules').then((result)=>{
                this.$message.success('成功获取模块信息！');
            })
        },
        methods: {
            append_module(data, node) {
                this.$store.commit('module/set_default_form');
                this.$store.commit('module/set_active_module', data)
                this.$store.commit('module/set_parent_label', data.label)
                this.$store.commit('module/set_form_val', {prop: 'pid', val: data.id})
                this.$store.commit('module/set_dialog_visible', true)
            },

            remove(node, data) {
                const parent = node.parent;
                const children = parent.data.children || parent.data;
                const index = children.findIndex(d => d.id === data.id);
                children.splice(index, 1);
            },
            edit_module(data, node) {
                this.$store.commit('module/set_default_form');

                for(let k in data) {
                    this.$store.commit('module/set_form_val', {prop: k, val: data[k]})
                }

                this.$store.commit('module/set_parent_label', this.$store.state.module.module_info[data.pid]['name'])
                this.$store.commit('module/set_active_module', data)
                this.$store.commit('module/set_dialog_visible', true)
            },
            handleDrop(draggingNode, dropNode, dropType, ev) {
                const form = {
                    id: draggingNode.data.id,
                    name: draggingNode.data.name,
                    path: draggingNode.data.path,
                    pid: dropType==='inner'?dropNode.data.id:dropNode.data.pid,
                    sequence: dropType==='inner'?1:dropType==='after'?dropNode.data.sequence+1:dropNode.data.sequence-1,
                    state: 1,
                    display: 1,
                }, _this = this;

                _this.$store.dispatch('module/add_or_update_module', form)
                    .then(function(result){
                        _this.$store.commit('module/set_default_form');
                        _this.$message.success("移动成功");
                    });
            },
            allowDrop(draggingNode, dropNode, type) {
                if (dropNode.data.pid === 0 || (dropNode.data.pid === 1 && type !== 'inner')) {
                    return false;
                } else {
                    return true;
                }
            },
            allowDrag(draggingNode) {
                return draggingNode.data.pid !== 0 && draggingNode.data.pid !== 1;
            }
        }
    }
</script>

<style scoped>
    .custom-tree-node {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        padding-right: 8px;
    }
</style>