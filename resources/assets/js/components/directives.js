const disableEl = (el)=> {
    el.setAttribute('disabled', 'disabled')
    el.classList.add("is-disabled");
}

const hideEl = (el)=> { el.style.display = 'none' }
const checkPermissionBind = (permission_attr, type, el, binding, vnode)=> {
    let permissionList = vnode.context.$store.state.user.user_module_permission;
    if(!(permissionList && permissionList.hasOwnProperty(binding.value)
            && permissionList[binding.value][permission_attr] == 1)){
        type == 'hide' ? hideEl(el) : disableEl(el)
    }
};

const checkPermissionUpdate = (permission_attr, type, el, binding, vnode)=> {
    if(binding.value && binding.value[permission_attr] === 1){
        type == 'hide' ? hideEl(el) : disableEl(el)
    }
};

const getPermissionDirective = (permission_attr, type) => {
    return {
        bind(el, binding, vnode){
            checkPermissionBind(permission_attr, type, el, binding, vnode)
        },
        update: function(el, binding, vnode) {
            checkPermissionUpdate(permission_attr, type, el, binding, vnode)
        }
    };
}

export default {
    hasReadPermissionHide: getPermissionDirective('permission1', 'hide'),
    hasReadPermissionDisable: getPermissionDirective('permission1', 'disable'),
    hasUpdatePermissionHide: getPermissionDirective('permission2', 'hide'),
    hasUpdatePermissionDisable: getPermissionDirective('permission2', 'disable'),
    hasDeletePermissionHide: getPermissionDirective('permission3', 'hide'),
    hasDeletePermissionDisable: getPermissionDirective('permission3', 'diable')
}