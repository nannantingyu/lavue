<template>
    <el-form ref="form" :model="form" :rules="rules">
        <el-form-item :label-width="formLabelWidth" label="模块名" prop="name">
            <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="模块路径" prop="path">
            <el-input v-model="form.path"></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="父模块">
            <el-input v-model="parent_label" disabled></el-input>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="状态" prop="state">
            <el-radio value="1" v-model="form.state" :label="1">启用</el-radio>
            <el-radio value="0" v-model="form.state" :label="0">禁用</el-radio>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="前端显示" prop="display">
            <el-radio value="1" v-model="form.display" :label="1">显示</el-radio>
            <el-radio value="0" v-model="form.display" :label="0">隐藏</el-radio>
        </el-form-item>
        <el-form-item :label-width="formLabelWidth" label="顺序" prop="sequence">
            <el-input v-model="form.sequence" type="number"></el-input>
        </el-form-item>
        <el-form-item>
            <el-col :offset="4" :span="8">
                <el-button type="primary" @click="submitForm()">{{btn_msg}}</el-button>
            </el-col>
        </el-form-item>
    </el-form>
</template>

<script>
    import {Form, FormItem, Input, Upload, Select, Option, Col, Radio} from 'element-ui'
    Vue.use(Form)
    Vue.use(FormItem)
    Vue.use(Input)
    Vue.use(Upload)
    Vue.use(Select)
    Vue.use(Option)
    Vue.use(Col)
    Vue.use(Radio)

    import {mapActions, mapMutations, mapState, mapGetters} from 'vuex'

    export default {
        name: "add-module",
        computed: {
            ...mapState(['formLabelWidth']),
            ...mapState({
                'rules': state=> state.module.rules,
                'form': state=> state.module.form,
                'parent_label': state=> state.module.parent_label,
                'active_module': state=> state.module.active_module
            }),
            btn_msg: function() {
                return this.form.id ? "立即更新" : "立即添加"
            }
        },
        methods: {
            ...mapActions({
                'get_banner_info': 'banner/get_banner_info',
                'add_or_update_banner': 'banner/add_or_update_banner'
            }),
            submitForm: function() {
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        this.$store.dispatch('module/add_or_update_module')
                            .then((result)=>{
                                if(!this.form.id) {
                                    const newChild = { id: result.id, label: this.form.name, children: [] };
                                    this.$store.commit('module/add_new_node_to_active', newChild)
                                    this.$store.commit('module/set_dialog_visible', false);
                                    this.$message.success("添加成功");
                                }
                                else {
                                    this.$store.commit('module/set_dialog_visible', false);
                                    const update_pro = {
                                        'label': this.form.name,
                                        'name': this.form.name,
                                        'path': this.form.path,
                                        'state': this.form.state,
                                        'display': this.form.display,
                                        'sequence': this.form.sequence
                                    }

                                    for(let prop in update_pro)
                                        this.$store.commit('module/set_active_module_val', {prop: prop, val: update_pro[prop]});

                                    this.$message.success("更新成功");
                                }

                                this.$store.commit('module/set_default_form');
                            });
                    else
                        this.$message.error('请填写完整的信息！');
                });
            }
        }
    }
</script>

<style scoped>

</style>