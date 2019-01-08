<template>
    <div>
        <el-select v-model="role_now" placeholder="请选择角色" @change="change_role">
            <el-option
                    v-for="(val, key) in roles"
                    :key=val.id
                    :label="val.role_name"
                    :value=val.id></el-option>
        </el-select>
        <el-row class="transfer-panel">
            <el-transfer
                    class="transfer-full-width"
                    v-model="role_users"
                    :data="user_lists"
                    @change="handleChange"
                    :titles="['空余用户', '角色下用户']"></el-transfer>
        </el-row>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex'
    import {Select, Option, Transfer} from 'element-ui'
    Vue.use(Select)
    Vue.use(Option)
    Vue.use(Transfer)

    export default {
        name: "role-user",
        data() {
            return {
                role_now: '',
                user_lists: []
            }
        },
        computed: {
            ...mapState({
                'formLabelWidth': state=>state.formLabelWidth,
                'roles': state=>state.user.roles,
                'users': state=>state.user.users,
                'user_module_permission': state=> state.user.user_module_permission
            }),
            'role_users': {
                get() {
                    return this.$store.state.user.role_users
                },
                set(value) {
                    this.$store.commit('user/set_role_users', value)
                }
            }
        },
        created() {
            this.$store.dispatch('user/get_roles').then(result=>{
                this.$store.dispatch('user/get_users').then(result=>{
                    for(let u of this.users) {
                        this.user_lists.push({
                            key: u['id'],
                            label: u['nickname'],
                            disabled: !this.user_module_permission['role-user-update']
                        })
                    }
                })
            })
        },
        methods: {
            change_role: function(data) {
                this.$store.dispatch('user/get_role_users', data).then((result)=> {
                })
            },
            add_role: function() {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        _this.$store.dispatch('user/add_role').then(()=>{
                            _this.$message.success("添加成功");
                        }).catch((msg)=>{
                            _this.$message.error(msg);
                        })
                    } else {
                        _this.$message.error('请填写完整的信息！');
                        return false;
                    }
                });
            },
            handleChange: function(value, direction, movedKeys) {
                if(!this.role_now) {
                    this.$message.error('先选择角色')
                    let original_role_users = this.role_users;
                    if(direction === 'left'){
                        original_role_users = original_role_users.concat(movedKeys);
                    }
                    else {
                        for(let uid of movedKeys) {
                            let index = original_role_users.indexOf(uid);
                            if(index > -1) {
                                original_role_users.splice(index, 1)
                            }
                        }
                    }

                    return false;
                }

                let method = "assign_role_for_users";
                if(direction === 'left') {
                    method = "retract_role_for_users"
                }

                this.$store.dispatch('user/'+method, {
                    users: movedKeys.join(','),
                    role_id: this.role_now
                }).then(result=>{
                    this.$message.success('移动成功');
                })
            }
        }
    }
</script>

<style scoped>
    .box-card {
        max-width: 400px;
        margin: 100px auto;
    }
    .transfer-panel {
        margin-top: 20px;
    }
</style>