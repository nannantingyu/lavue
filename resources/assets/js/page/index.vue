<template>
    <p>欢迎登陆 <em>聚金小程序</em> 后台管理系统</p>
</template>

<script>
    import {mapState, mapActions} from "vuex"
    export default {
        name: "index",
        data() {
            return {
            }
        },
        computed: {
            ...mapState({
                "formLabelWidth": state=>state.user.formLabelWidth,
            })
        },
        methods: {
            ...mapActions({
            }),
        }
    }
</script>

<style scoped>
</style>