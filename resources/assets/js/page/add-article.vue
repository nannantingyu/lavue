<template>
    <div>
        <el-form ref="form" :model="form" :rules="rules" label-width="80px">
            <el-form-item label="标题" prop="title">
                <el-input v-model="form.title"></el-input>
            </el-form-item>

            <el-form-item label="内容" prop="title">
                <script id="editor" type="text/plain"></script>
            </el-form-item>

            <!--<el-form-item>-->
                <!--<ContentEditor></ContentEditor>-->
            <!--</el-form-item>-->

            <el-form-item label="图片" prop="image">
                <el-upload
                        class="avatar-uploader"
                        :limit="1"
                        action="/uploads_image"
                        name="fileDataFileName"
                        list-type="picture-card"
                        :headers="headers"
                        :file-list="fileimgs"
                        :on-success="handleSuccess"
                        :on-remove="handleRemove">
                    <i class="el-icon-plus"></i>
                </el-upload>
            </el-form-item>

            <el-form-item label="描述" prop="description">
                <el-input type="textarea" v-model="form.description"></el-input>
            </el-form-item>

            <el-form-item label="关键词" prop="keywords">
                <el-input v-model="form.keywords"></el-input>
            </el-form-item>

            <el-form-item label="作者" prop="author">
                <el-input v-model="form.author"></el-input>
            </el-form-item>

            <el-form-item label="发布时间">
                <el-col :span="12">
                    <el-form-item prop="publish_time">
                        <el-date-picker type="datetime" placeholder="选择日期"
                                        v-model="form.publish_time"
                                        format="yyyy-MM-dd HH:mm:ss"
                                        value-format="yyyy-MM-dd HH:mm:ss"
                                        style="width: 100%;"></el-date-picker>
                    </el-form-item>
                </el-col>
            </el-form-item>

            <el-form-item label="类型" prop="type">
                <el-input v-model="form.type"></el-input>
            </el-form-item>

            <el-form-item label="分类" prop="categories">
                <el-select v-model="form.categories" multiple placeholder="请选择分类">
                    <el-option
                            v-for="item in article_categories"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="发布状态" prop="state">
                <el-switch v-model="form.state"></el-switch>
            </el-form-item>

            <el-form-item label="推荐状态" prop="recommend">
                <el-switch v-model="form.recommend"></el-switch>
            </el-form-item>

            <el-form-item label="点击量" prop="hits">
                <el-input type="number" v-model="form.hits"></el-input>
            </el-form-item>

            <el-form-item label="点赞数" prop="favor">
                <el-input type="number" v-model="form.favor"></el-input>
            </el-form-item>

            <el-form-item label="原创类型" prop="source_type">
                <el-select v-model="form.source_type" placeholder="请选择原创类型">
                    <el-option label="爬虫" value="crawl"></el-option>
                    <el-option label="原创" value="original"></el-option>
                    <el-option label="半原创" value="half"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="入库时间">
                <el-input disabled v-model="form.created_at"></el-input>
            </el-form-item>

            <el-form-item label="更新时间">
                <el-input disabled v-model="form.updated_at"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="submitForm">立即创建</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import ContentEditor from '../components/content-editor'
    import {mapActions, mapState, mapGetters, mapMutations} from 'vuex'
    import {FormItem, Input, Select, Option, Switch, DatePicker, Upload, Form} from 'element-ui'
    Vue.use(FormItem);
    Vue.use(Input);
    Vue.use(Select);
    Vue.use(Option);
    Vue.use(Switch);
    Vue.use(DatePicker);
    Vue.use(Upload);
    Vue.use(Form);

    import '../static/Ueditor/ueditor.config'
    import '../static/Ueditor/ueditor.all.min'
    import '../static/Ueditor/lang/zh-cn/zh-cn.js'
    import '../static/Ueditor/ueditor.parse.min'
    import '../static/Ueditor/themes/default/css/ueditor.css'

    export default {
        name: "article-page",
        components: {ContentEditor},
        computed: {
            ...mapState(['headers', 'formLabelWidth']),
            ...mapState({
                'form': state=>state.article.form,
                'article_categories': state=>state.article.article_categories,
                'rules': state=>state.article.rules,
                'editor': state=>state.article.editor
            }),
            ...mapGetters({
                'fileimgs': 'article/fileimgs',
            }),
        },
        mounted() {
            const _this = this;
            if(this.editor) {
                this.editor.destroy();
            }

            let editor = UE.getEditor('editor', {
                initialFrameWidth: "100%",
                initialFrameHeight: 600
            });

            this.set_editor(editor);

            let id = this.$route.params.id;
            if(id) this.getArticle(this.$route.params.id);
            else this.set_default_form()

            if(this.article_categories.length <= 0) {
                this.$store.dispatch("category/get_category_lists").then(result=> {
                    _this.set_article_categories(result);
                })
            };
        },
        methods: {
            ...mapActions({
                'getArticle': 'article/getArticle',
                'add_or_update_article': 'article/add_or_update_article'
            }),
            ...mapMutations({
                'set_form_value': 'article/set_form_value',
                'set_default_form': "article/set_default_form",
                'set_article_categories': "article/set_article_categories",
                'set_editor': 'article/set_editor'
            }),
            handleSuccess: function(response, file, file_list) {
                if(response.success) {
                    this.set_form_value({key: 'image', value: response.file_path})
                }
            },
            handleRemove: function() {
                this.set_form_value({key: 'image', value: null})
            },
            submitForm: function() {
                const _this = this;
                console.log(this.editor.getContent());
                this.$refs['form'].validate((valid) => {
                    if (valid)
                        _this.add_or_update_article(this.form).then(function(result){
                            _this.$message.success("添加成功");
                            _this.$router.push({path: '/article'});
                        }).catch(msg=>{
                            for(let k in msg) {
                                for (let m of msg[k]) {
                                    _this.$message.error(m);
                                }
                            }
                        });
                    else _this.$message.error('请填写完整的信息！');
                });
            },
        }
    }
</script>

<style scoped>

</style>