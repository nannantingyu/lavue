<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-select
                        size="medium"
                        v-model="module_now"
                        @change="change_module"
                        placeholder="请选择模块">
                    <el-option
                            v-for="(val) in module_option"
                            :key=val.id
                            :label="val.name"
                            :value=val.id>
                        <span>{{ "&nbsp;".repeat(val.step*5) }}{{ val.name }}</span>
                    </el-option>
                </el-select>
            </el-col>
        </el-row>
        <el-row>
            <el-table
                    :data="role_permissions"
                    style="width: 100%">
                <el-table-column
                        prop="role_id"
                        :label="columns['role_id']['title']"
                        v-if="columns['role_id']['show']"
                        width="60">
                </el-table-column>
                <el-table-column
                        prop="role_name"
                        :label="columns['role_name']['title']"
                        v-if="columns['role_name']['show']"
                        width="160">
                </el-table-column>
                <el-table-column label="操作" width="*" fixed="right">
                    <template slot-scope="scope">
                        <el-checkbox-group
                                :disabled="!!!module_now || !user_module_permission['permission-update']"
                                v-model="scope.row.permissions"
                                @change="change_role_permission(scope.row.role_id, scope.row.permissions, scope.$index)">
                            <el-checkbox label="读权限"></el-checkbox>
                            <el-checkbox label="更新权限"></el-checkbox>
                            <el-checkbox label="删除权限"></el-checkbox>
                        </el-checkbox-group>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex'
    import {Select, Option, Table, TableColumn, Button, Checkbox, CheckboxGroup} from 'element-ui'
    Vue.use(Select)
    Vue.use(Option)
    Vue.use(TableColumn)
    Vue.use(Table)
    Vue.use(Button)
    Vue.use(Checkbox)
    Vue.use(CheckboxGroup)

    const permission_map = {
        1: '读权限',
        2: '更新权限',
        4: '删除权限',
    }

    export default {
        name: "permission",
        data() {
            return {
                columns: {
                    role_id: {title: "ID", show: true},
                    role_name: {title: "角色名", show: true},
                },
                module_now: '',
                role_permissions: []
            }
        },
        computed: {
            ...mapState({
                'formLabelWidth': state=>state.formLabelWidth,
                'roles': state=>state.user.roles,
                'module_option': state=>state.module.module_option,
                'user_module_permission': state=> state.user.user_module_permission
            }),
        },
        created() {
            this.$store.dispatch('module/get_module_tree_select').then(result=>{
                this.$store.dispatch('user/get_roles').then(result=>{
                    for(let role of result) {
                        this.role_permissions.push({
                            role_id: role.id,
                            role_name: role.role_name,
                            permissions: []
                        })
                    }
                })
            })
        },
        methods: {
            clear_role_permission: function() {
                for(let role in this.role_permissions) {
                    this.role_permissions[role]['permissions'] = [];
                }
            },
            change_module: function(data) {
                this.$store.dispatch('module/get_module_permission', data).then(result=>{
                    this.clear_role_permission()
                    if(Object.keys(result).length > 0) {
                        for(let index in this.role_permissions) {
                            const role = this.role_permissions[index];
                            if(result.hasOwnProperty(role['role_id'])) {
                                if((result[role['role_id']]['permission'] & 1) === 1)
                                    this.role_permissions[index]['permissions'].push(permission_map[1])
                                if((result[role['role_id']]['permission'] & 2) === 2)
                                    this.role_permissions[index]['permissions'].push(permission_map[2])
                                if((result[role['role_id']]['permission'] & 4) === 4)
                                    this.role_permissions[index]['permissions'].push(permission_map['4'])
                            }
                        }
                    }
                });
            },
            change_role_permission: function(role_id, permissions, index) {
                if(!this.module_now) {
                    this.$message.error("请选择模块")
                    return
                }

                let permission1 = permissions.indexOf(permission_map[1])>-1?1:0,
                    permission2 = permissions.indexOf(permission_map[2])>-1?2:0,
                    permission3 = permissions.indexOf(permission_map[4])>-1?4:0
                this.$store.dispatch('module/add_module_permission', {
                    entity_type: 'role',
                    entity_id: role_id,
                    id: this.module_now,
                    permission: permission1 | permission2 | permission3,
                }).then(result=>{
                    this.$message.success('权限修改成功')
                    this.$store.dispatch('user/get_user_module_permission').then(result=>{
                        this.$message.success('重新加载模块')
                    })
                }).catch(errors=>{
                    for(let error_module in errors) {
                        for(let msg of errors[error_module])
                            this.$message.error(msg)
                    }
                })
            }
        }
    }
</script>

<style scoped>
.el-select {
    width: 100%;
}
</style>