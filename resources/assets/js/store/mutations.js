export default {
    set_app_name: (state, app_name, rootState) => {
        state.app_name = app_name
    },
    transfer: function(state, img) {
        const imgs = img?'http://images.jujin8.com'+img.replace('/uploads/crawler', '/uploads'):'';
        return imgs;
    }
}