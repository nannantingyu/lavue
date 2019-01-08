export default {
    app_filter: state => {
        let filter = [];
        for(let k in state.app_name)
            filter.push({text: state.app_name[k], value: k});

        return filter;
    }
}