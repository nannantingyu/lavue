<template>
    <el-card class="box-card">
        <el-form ref="login-form" :model="login" :rules="rules">
            <el-form-item :label-width="formLabelWidth" label="用户名" prop="username">
                <el-input v-model="login.username" @keyup.enter.native="user_login"></el-input>
            </el-form-item>
            <el-form-item :label-width="formLabelWidth" label="密码" prop="password">
                <el-input v-model="login.password" @keyup.enter.native="user_login" type="password"></el-input>
            </el-form-item>
            <el-row>
                <el-col :span="4" :offset="10">
                    <el-button @click="user_login">登录</el-button>
                </el-col>
            </el-row>
        </el-form>
    </el-card>
</template>

<script>
    import {Form, Button, FormItem, Input, Card} from 'element-ui'
    Vue.use(Form)
    Vue.use(Button)
    Vue.use(FormItem)
    Vue.use(Input)
    Vue.use(Card)

    import {mapState, mapActions} from "vuex"
    export default {
        name: "login",
        data() {
            return {
                login: {
                    username: '',
                    password: ''
                },
            }
        },
        computed: {
            ...mapState({
                "formLabelWidth": state=>state.user.formLabelWidth,
                "rules": state=>state.user.rules
            })
        },
        methods: {
            ...mapActions({
                'loginAction': 'user/login'
            }),
            user_login: function() {
                const _this = this;
                this.$refs['login-form'].validate((valid) => {
                    if (valid)
                        _this.loginAction(_this.login).then(result=> {
                            _this.$router.push({path: '/index'})
                        }).catch(errors=> {
                            for(let error_module in errors) {
                                for(let msg of errors[error_module])
                                    this.$message.error(msg)
                            }
                        })
                    else _this.$message.error('请填写完整的信息！');
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
</style>