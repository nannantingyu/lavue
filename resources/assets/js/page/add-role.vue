<template>
    <el-form ref="form" :model="role"
             :rules="rules"
             :disabled="!user_module_permission['add-role-update']">
        <el-form-item :label-width="formLabelWidth" label="角色名" prop="role_name">
            <el-input v-model="role.role_name"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="状态" prop="state">
            <el-radio value="1" v-model="role.state" :label="1">显示</el-radio>
            <el-radio value="0" v-model="role.state" :label="0">隐藏</el-radio>
        </el-form-item>
        <el-row>
            <el-col :span="4" :offset="10">
                <el-button @click="add_role">添加</el-button>
            </el-col>
        </el-row>
    </el-form>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex'
    import {Form, Button, FormItem, Input, Radio} from 'element-ui'
    Vue.use(Form)
    Vue.use(Button)
    Vue.use(FormItem)
    Vue.use(Input)
    Vue.use(Radio)

    export default {
        name: "add-role",
        computed: {
            ...mapState({
                'rules': state=>state.user.role_rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'role': state=>state.user.role,
                'user_module_permission': state=> state.user.user_module_permission
            })
        },
        methods: {
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
            }
        }
    }
</script>

<style scoped>
.box-card {
    max-width: 400px;
    margin: 100px auto;
}
</style>