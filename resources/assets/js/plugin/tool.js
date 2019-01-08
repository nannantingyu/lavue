export const trim_char = function(char) {
    const str = this.toString()
    let left = 0, right = str.length - 1;
    while (str[left] === char && left <= right) left ++
    while (str[right] === char && right >= left) right --

    return str.slice(left, right+1)
}
String.prototype.trim_char = trim_char;

const includes_by_key = function(key, value) {
    for(let obj of this) {
        if(obj.hasOwnProperty(key) && obj[key] === value) {
            return true;
        }
    }

    return false;
}

Array.prototype.includes_by_key = includes_by_key;

export const check_time = msg=> {
    return function(rule, value, callback) {
        msg = msg || '请输入时间';
        if(!value) {
            callback(new Error('值不能为空'));
        }
        else if(!/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/.test(value)) {
            callback(msg)
        }
        else {
            callback();
        }
    }
};

export const check_integer_factory = msg=> {
    return function(rule, value, callback) {
        msg = msg || '格式不正确，值应该为数字';
        if(value !== 0 && !value) {
            callback(new Error('值不能为空'));
        }
        else if(!/^\d+$/.test(value)) {
            callback(msg);
        }
        else {
            callback();
        }
    }
};

function simlator_upload_success(_this) {
    return function(e, file, result) {
        var $img, img_path, msg;
        if (!file.inline) {
            return;
        }
        $img = file.img;
        if (!($img.hasClass('uploading') && $img.parent().length > 0)) {
            return;
        }
        if (typeof result !== 'object') {
            try {
                result = $.parseJSON(result);
            } catch (_error) {
                e = _error;
                result = {
                    success: false
                };
            }
        }
        if (result.success === false) {
            msg = result.msg || _this._t('uploadFailed');
            alert(msg);
            img_path = _this.defaultImage;
        } else {
            img_path = result.file_path;
        }
        _this.loadImage($img, img_path, function() {
            var $mask;
            $img.removeData('file');
            $img.removeClass('uploading').removeClass('loading');
            $mask = $img.data('mask');
            if ($mask) {
                $mask.remove();
            }
            $img.removeData('mask');
            $img.attr('src', img_path);
            _this.editor.trigger('valuechanged');
            if (_this.editor.body.find('img.uploading').length < 1) {
                return _this.editor.uploader.trigger('uploadready', [file, result]);
            }
        });
        if (_this.popover.active) {
            _this.popover.srcEl.prop('disabled', false);
            return _this.popover.srcEl.val(result.file_path);
        }
    };
}
//深拷贝
export const deepCopy = (p, c)=>{
    var c = c || {};
    for (var i in p) {
        if (p[i]&&typeof p[i] === 'object') {
            c[i] = (p[i].constructor === Array) ? [] : {};
            deepCopy(p[i], c[i]);
        } else {
            c[i] = p[i];
        }
    }
    return c;
}
export const dateFtt=(fmt,date)=>{
    var o = {
        "M+" : date.getMonth()+1,                 //月份
        "d+" : date.getDate(),                    //日
        "h+" : date.getHours(),                   //小时
        "m+" : date.getMinutes(),                 //分
        "s+" : date.getSeconds(),                 //秒
        "q+" : Math.floor((date.getMonth()+3)/3), //季度
        "S"  : date.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt))
        fmt=fmt.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
    for(var k in o)
        if(new RegExp("("+ k +")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
    return fmt;
}