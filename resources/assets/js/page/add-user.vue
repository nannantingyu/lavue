<template>
    <el-form ref="registForm" :model="regist" :rules="rules">
        <el-form-item :label-width="formLabelWidth" label="用户名" prop="name">
            <el-input v-model="regist.name"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="密码" prop="password">
            <el-input v-model="regist.password" type="password"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="重复密码" prop="repassword">
            <el-input v-model="regist.repassword" type="password"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="昵称" prop="nickname">
            <el-input v-model="regist.nickname"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="邮箱" prop="email">
            <el-input v-model="regist.email" type="email"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="手机号" prop="phone">
            <el-input v-model="regist.phone"></el-input>
        </el-form-item>
        <el-row>
            <el-col :span="4" :offset="10">
                <el-button @click="registUser">注册</el-button>
            </el-col>
        </el-row>
    </el-form>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex'
    import {Form, Button, FormItem, Input, Card} from 'element-ui'
    Vue.use(Form)
    Vue.use(Button)
    Vue.use(FormItem)
    Vue.use(Input)
    Vue.use(Card)

    export default {
        name: "regist",
        computed: {
            ...mapState({
                'rules': state=>state.user.user_rules,
                'formLabelWidth': state=>state.user.formLabelWidth,
                'regist': state=>state.user.regist,
            }),
        },
        methods: {
            registUser: function() {
                const _this = this;
                this.$refs['registForm'].validate((valid) => {
                    if (valid) {
                        _this.$store.dispatch('user/regist').then(()=>{
                            _this.$message.success("添加成功");
                            _this.$router.push({path: '/index'})
                        }).catch((errors)=>{
                            for(let error_module in errors) {
                                for(let msg of errors[error_module])
                                    this.$message.error(msg)
                            }
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