<template>
    <el-form ref="form" :model="form" :rules="rules">
        <el-form-item :label-width="formLabelWidth" label="链接" prop="url">
            <el-input v-model="form.url"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="类型" prop="type">
            <el-select v-model="form.type" placeholder="请选择">
                <el-option key="update" label="更新" value="update"></el-option>
                <el-option key="delete" label="删除" value="delete"></el-option>
                <el-option key="api" label="API调用" value="api"></el-option>
            </el-select>
        </el-form-item>
        <el-row>
            <el-col :span="4" :offset="10">
                <el-button @click="genetate_template">生成模板</el-button>
            </el-col>
        </el-row>
    </el-form>
</template>

<script>
    import {mapState} from 'vuex'
    import {Form, Button, FormItem, Input, Select, Option} from 'element-ui'
    Vue.use(Form)
    Vue.use(Button)
    Vue.use(FormItem)
    Vue.use(Select)
    Vue.use(Input)
    Vue.use(Option)

    export default {
        name: "regist",
        computed: {
            ...mapState({
                'rules': state=>state.template.rules,
                'formLabelWidth': state=>state.formLabelWidth,
                'form': state=>state.template.form,
            }),
        },
        methods: {
            genetate_template: function() {
                const _this = this;
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        _this.$store.dispatch('template/generate_template').then(()=>{
                            _this.$message.success("更新成功");
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