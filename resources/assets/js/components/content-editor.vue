<template>
    <div>
        <textarea :id="editorName" placeholder="Balabala" autofocus></textarea>
    </div>
</template>

<script>
    import 'simditor/styles/simditor.css';
    import $ from 'jquery';
    import Simditor from 'simditor';
    import {mapState, mapMutations, mapActions} from 'vuex'

    export default {
        name: "contenteditor",
        data() {
            return {
                editorName: new Date().getTime(),
                toolbar: "请输入..."
            }
        },
        mounted() {
            let editor = new Simditor({
                textarea: $('#' + this.editorName),
                toolbar: this.toolbar,
                upload: {
                    url: '/uploads_image',
                    fileKey: 'fileDataFileName',
                    connectionCount: 3,//同时上传个数
                    leaveConfirm: '正在上传文件'
                },
                pasteImage: true,
                tabIndent: true,
            });

            this.set_editor(editor)
        },
        computed: {
            ...mapState({
                'editor': state=>state.article.editor
            }),
        },
        methods: {
            ...mapMutations({
               'set_editor': 'article/set_editor'
            })
        }
    }
</script>

<style scoped>
</style>