//ajax打开,跳转到指定页面
$("body").on("click", ".ajax-goto", function () {

    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (!target) {
        layer.msg('未找到执行地址', {icon: 5});
        return false;
    }

    var params = {};

    // 是否设置了参数字段
    // 参数格式：data-ids="{"name":"value"}"
    var ids = $this.attr('data-ids');
    if (typeof ids !== "undefined" && ids !== null && ids !== "") {
        try {
            // 使用 JSON.parse 替代 eval 提高安全性
            var idsObj = JSON.parse(ids);
            $.extend(params, idsObj);
        } catch (e) {
            console.error("JSON解析错误: ", e);
            // 如果JSON解析失败，尝试使用eval作为备选方案
            try {
                var idsObj = eval('(' + ids + ')');
                $.extend(params, idsObj);
            } catch (e2) {
                console.error("eval解析错误: ", e2);
            }
        }
    }

    // 是否设置导出标签
    // 配套标签：target-from='search form'
    if ($this.hasClass('export')) {
        var targetForm = $this.attr('target-form');
        if (targetForm) {
            var form = $('.' + targetForm);
            var formData = form.serializeArray();
            $.each(formData, function (i, field) {
                params[field.name] = field.value;
            });
        }
    }

    // 构建最终URL
    var finalTarget = target;
    if (Object.keys(params).length > 0) {
        var separator = target.indexOf('?') !== -1 ? '&' : '?';
        finalTarget = target + separator + $.param(params);
    }

    log('执行地址：' + finalTarget);

    if ($this.attr('target') == '_blank') {
        window.open(finalTarget);
    } else {
        window.location.href = finalTarget;
    }
    return true;
});
//导出=》选择checkbox数据
$("body").on("click", ".ajax-export-check", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (!target) {
        parent.layer.msg('未找到执行地址', {icon: 5});
        return false;
    }

    // 获取选中的复选框值
    var checkedArr = $('.ajax-list-table input[class="checkboxCtrlId"]:checked');
    var cIds = checkedArr.map(function () {
        return $(this).val();
    }).get();

    if (cIds.length > 0) {
        var params = {id: cIds.join(',')};

        // 是否设置了额外的表单参数
        if ($this.hasClass('export')) {
            var targetForm = $this.attr('target-form');
            if (targetForm) {
                var form = $('.' + targetForm);
                var formData = form.serializeArray();
                $.each(formData, function (i, field) {
                    params[field.name] = field.value;
                });
            }
        }

        var finalTarget = target + "?" + $.param(params);
        log('执行地址：' + finalTarget);

        if ($this.attr('target') == '_blank') {
            window.open(finalTarget);
        } else {
            window.location.href = finalTarget;
        }
    } else {
        parent.layer.msg('请选择操作数据', {icon: 5});
    }
    return true;
});
//ajax打开一个页面
$("body").on("click", ".ajax-open", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (target) {
        var tit = $this.attr('data-title'); //打开标题
        var fun = $this.attr('data-calback'); //回调函数

        // 参数处理
        var params = {};

        // 是否带参数字段
        // 参数传，支持多个参数传送 格式：data-ids="{'tid':'2','name':'张三'}"
        var ids = $this.attr('data-ids');
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 使用 JSON.parse 替代 eval 提高安全性
                var idsObj = JSON.parse(ids);
                $.extend(params, idsObj);
            } catch (e) {
                log("JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("eval解析错误: ", e2);
                }
            }
        }

        // 是否设置了单个值
        var id = $this.attr("data-id");
        if (typeof id !== "undefined" && id !== null && id !== "") {
            params.id = id;
        }

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('打开地址：' + finalTarget);

        // 重定义打开宽度和高度
        var width = $this.attr('width');
        var height = $this.attr('height');
        if (typeof width === "undefined" || width === null || width === "" || width == 0) {
            width = "90%";
        }
        if (typeof height === "undefined" || height === null || height === "" || height == 0) {
            height = "90%";
        }

        // 判断是否是手机页
        var windowWidth = $(window).width();
        if (windowWidth <= 750) {
            width = "90%";
            height = "90%";
        }

        layer.open({
            type: 2,
            title: false,
            shadeClose: false,
            //btn: ['关闭'],
            fixed: true, //不固定
            area: [width, height],
            content: finalTarget,
            success: function (layero, index) {
                layer.iframeAuto(index);
            },
            end: function () {
                if (fun != null && fun !== "") {
                    try {
                        eval(fun);
                        log('执行回调函数：' + fun);
                    } catch (e) {
                        log('回调函数执行错误: ', e);
                    }
                } else {
                    runCurrTurnPage();
                }
            }
        });
    }
    return false;
});

//ajax打开
//可以选择多个checkbox值，同时传送参数 id=3,4,5
$("body").on("click", ".ajax-open-more", function () {
    var $this = $(this);
    var title = $this.attr('data-title'); //打开标题
    var ids = $this.attr('data-ids'); //判断是否有参数传
    var fun = $this.attr('data-calback'); //判断是否有回调函数
    var checkedVal = [];

    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (target) {
        //多个选择的目标id
        $('.ajax-list-table tbody input[class="checkboxCtrlId"]:checked').each(function () {
            checkedVal.push($(this).val());
        });

        if (checkedVal.length > 0) {
            var params = {id: checkedVal.join(',')};

            // 是否设置了参数字段data-ids="{'name':'张三','sex':'女'}"
            if (typeof ids !== "undefined" && ids !== null && ids !== "") {
                try {

                    // 使用 JSON.parse 替代 eval 提高安全性
                    var idsObj = JSON.parse(ids);
                    $.extend(params, idsObj);
                } catch (e) {
                    log("JSON解析错误: ", e);
                    // 如果JSON解析失败，尝试使用eval作为备选方案
                    try {
                        var idsObj = eval('(' + ids + ')');
                        $.extend(params, idsObj);
                    } catch (e2) {
                        log("eval解析错误: ", e2);
                    }
                }
            }

            // 构建最终URL
            var finalTarget = target;
            if (Object.keys(params).length > 0) {
                var separator = target.indexOf('?') !== -1 ? '&' : '?';
                finalTarget = target + separator + $.param(params);
            }

            log('打开地址：' + finalTarget);

            //重定义打开宽度和高度
            var width = $this.attr('width');
            var height = $this.attr('height');
            if (typeof width === "undefined" || width === null || width === "" || width == 0) {
                width = "90%";
            }
            if (typeof height === "undefined" || height === null || height === "" || height == 0) {
                height = "90%";
            }
            //判断是否是手机页
            var windowWidth = $(window).width();
            if (windowWidth <= 750) {
                width = "90%";
                height = "90%";
            }

            //打开窗口
            layer.open({
                type: 2,
                title: false,
                shadeClose: false,
                //btn: ['关闭'],
                fixed: true, //不固定
                area: [width, height],
                content: finalTarget,
                success: function (layero, index) {
                    layer.iframeAuto(index);
                },
                end: function () {
                    if (fun != null && fun !== "") {
                        try {
                            eval(fun);
                            log('执行回调函数：' + fun);
                        } catch (e) {
                            log('回调函数执行错误: ', e);
                        }
                    } else {
                        runCurrTurnPage();
                    }
                }
            });
        } else {
            layer.msg('请选择批量操作数据', {icon: 5});
            return false;
        }
    }

    return false;
});

// ajax删除
$("body").on("click", ".ajax-del", function () {
    var $this = $(this);
    var target = $this.attr('data-url') || $this.attr('href') || '';

    if (target === '') {
        layer.msg('未找到执行地址~', {icon: 5});
        return false;
    }

    // 是否设置了参数字段，执行回调函数
    var ids = $this.attr('data-ids');
    var fun = $this.attr('data-calback');

    // 参数处理
    var params = {};

    if (typeof ids !== "undefined" && ids !== null && ids !== "") {
        try {
            // 预处理字符串，转换为标准JSON格式
            var standardizedIds = ids
                .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                .replace(/'/g, '"')       // 将单引号转换为双引号
                .replace(/\\'/g, "'")     // 处理转义的单引号
                .replace(/\\"/g, '"');    // 处理转义的双引号

            log('data-JSON: ' + standardizedIds);
            // 使用 JSON.parse 替代 eval 提高安全性
            var idsObj = JSON.parse(standardizedIds);
            $.extend(params, idsObj);
        } catch (e) {
            log("data-JSON解析错误: ", e);
            // 如果JSON解析失败，尝试使用eval作为备选方案
            try {
                var idsObj = eval('(' + ids + ')');
                $.extend(params, idsObj);
            } catch (e2) {
                log("eval解析错误: ", e2);
            }
        }
    }

    // 构建最终URL
    var finalTarget = target;
    if (Object.keys(params).length > 0) {
        var separator = target.indexOf('?') !== -1 ? '&' : '?';
        finalTarget = target + separator + $.param(params);
    }

    log('删除执行地址：' + finalTarget);

    layer.confirm('您确定要删除吗?', {btn: ['确定', '取消'], icon: 3, title: "提示"}, function (index) {
        layer.close(index); //点击 =》确认框=》关闭

        log('确定执行删除操作：');
        $.ajax({
            type: "POST",
            url: finalTarget,
            data: '',
            dataType: "json",
            beforeSend: function () {
                layer.msg('数据处理中...', {icon: 16, time: 100000, shade: [0.5, '#000', true]});
            },
            success: function (result) {
                if (result.code == '1') {
                    //操作成功提示
                    layer.msg(result.msg, {icon: 1});
                    if (fun != null && fun !== "") {
                        try {
                            eval(fun);
                            log('执行回调函数：' + fun);
                        } catch (e) {
                            log('回调函数执行错误: ', e);
                        }
                    } else {
                        setTimeout(function () {
                            runCurrTurnPage();
                        }, 1);
                    }
                } else {
                    layer.msg(result.msg, {icon: 5});
                }
            },
            error: function (xhr, status, error) {
                layer.msg('请求失败: ' + error, {icon: 5});
            },
            complete: function () {
                // 执行完之后执行
            },
        }); //end ajax post
    });

    return false;
});

//ajax get请求=》单个请求
$("body").on("click", ".ajax-get", function () {
    var $this = $(this);

    //提示操作
    if ($this.hasClass('confirm')) {
        if (!confirm('确认要执行该操作吗?')) {
            return false;
        }
    }

    //是否有加载提示
    var ajaxload = null;
    if ($this.hasClass('ajaxload')) {
        //页面层-自定义
        ajaxload = layer.msg('正在处理,请稍等...', {
            icon: 16,
            time: 100000,
            shade: [0.5, '#000', true]
        }); //0代表加载的风格，支持0-2
    }

    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (target) {
        var ids = $this.attr('data-ids'); //判断是否有参数传
        var fun = $this.attr('data-calback'); //判断是否有回调函数

        // 参数处理
        var params = {};
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 预处理字符串，转换为标准JSON格式
                var standardizedIds = ids
                    .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                    .replace(/'/g, '"')       // 将单引号转换为双引号
                    .replace(/\\'/g, "'")     // 处理转义的单引号
                    .replace(/\\"/g, '"');    // 处理转义的双引号

                // 使用 JSON.parse 解析标准化后的字符串
                var idsObj = JSON.parse(standardizedIds);
                $.extend(params, idsObj);
            } catch (e) {
                log("ajax-get JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("ajax-get eval解析错误: ", e2);
                }
            }
        }

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('GET执行地址：' + finalTarget);

        //执行get请求
        $.get(finalTarget).done(function (data) {
            if (data.code) {
                parent.layer.msg(data.msg, {icon: 1});
                if (fun != null && fun !== "") {
                    try {
                        eval(fun);
                        log('执行回调函数：' + fun);
                    } catch (e) {
                        log('回调函数执行错误: ', e);
                    }
                } else {
                    setTimeout(function () {
                        runCurrTurnPage();
                    }, 1500);
                }
            } else {
                parent.layer.msg(data.msg, {icon: 5});
            }
        }).fail(function (xhr, status, error) {
            parent.layer.msg('请求失败: ' + error, {icon: 5});
        }).always(function () {
            if (ajaxload) {
                layer.close(ajaxload);
            }
        });
    }
    return false;
});

//ajax get -more 请求=》选择多个时使用
$("body").on("click", ".ajax-get-more", function () {
    var $this = $(this);

    if (!confirm('确认要执行该操作吗?')) {
        return false;
    }

    // 获取选中的复选框值
    var checkedArr = $('.ajax-list-table input[class="checkboxCtrlId"]:checked');
    var cIds = checkedArr.map(function () {
        return $(this).val();
    }).get().join(',');

    if (cIds.length > 0) {
        var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

        if (target) {
            var ids = $this.attr('data-ids'); //判断是否有参数传
            var fun = $this.attr('data-calback'); //判断是否有回调函数

            // 参数处理
            var params = {id: cIds};

            if (typeof ids !== "undefined" && ids !== null && ids !== "") {
                try {
                    // 预处理字符串，转换为标准JSON格式
                    var standardizedIds = ids
                        .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                        .replace(/'/g, '"')       // 将单引号转换为双引号
                        .replace(/\\'/g, "'")     // 处理转义的单引号
                        .replace(/\\"/g, '"');    // 处理转义的双引号

                    // 使用 JSON.parse 解析标准化后的字符串
                    var idsObj = JSON.parse(standardizedIds);
                    $.extend(params, idsObj);
                } catch (e) {
                    log("ajax-get-more JSON解析错误: ", e);
                    // 如果JSON解析失败，尝试使用eval作为备选方案
                    try {
                        var idsObj = eval('(' + ids + ')');
                        $.extend(params, idsObj);
                    } catch (e2) {
                        log("ajax-get-more eval解析错误: ", e2);
                    }
                }
            }

            // 构建最终URL
            var finalTarget = target;
            if (Object.keys(params).length > 0) {
                var separator = target.indexOf('?') !== -1 ? '&' : '?';
                finalTarget = target + separator + $.param(params);
            }

            log('GET-More执行地址：' + finalTarget);

            // 执行POST请求（虽然函数名是get-more，但实际执行的是POST请求）
            $.post(finalTarget, {id: cIds}, function (data) {
                if (data.code == '1') {
                    parent.layer.msg(data.msg, {icon: 1});
                    if (fun != null && fun !== "") {
                        try {
                            eval(fun);
                        } catch (e) {
                            log('回调函数执行错误: ', e);
                        }
                    } else {
                        setTimeout(function () {
                            runCurrTurnPage();
                        }, 1500);
                    }
                } else {
                    parent.layer.msg(data.msg, {icon: 5});
                }
            }, "json").fail(function (xhr, status, error) {
                parent.layer.msg('请求失败: ' + error, {icon: 5});
            });
        }
    } else {
        parent.layer.msg('请选择批量操作数据', {icon: 5});
    }
    return false;
});

// 重写表单POST提交处理
$("body").on("click", ".ajax-post", function () {
    var $this = $(this);
    var target, query, form;
    var targetForm = $this.attr('target-form');
    var needConfirm = false;

    if (($this.attr('type') == 'submit') || (target = $this.attr('href')) || (target = $this.attr('url'))) {
        form = $('.' + targetForm);

        if ($this.attr('hide-data') === 'true') {
            //无数据时也可以使用的功能
            form = $('.hide-data');
            query = form.serialize();
        } else if (form.get(0) == undefined) {
            return false;
        } else if (form.get(0).nodeName == 'FORM') {
            if ($this.hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }

            if ($this.attr('url') !== undefined) {
                target = $this.attr('url');
            } else {
                target = form.get(0).action;
            }
            query = form.serialize();
        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
            form.each(function (k, v) {
                if (v.type == 'checkbox' && v.checked == true) {
                    needConfirm = true;
                }
            });

            if (needConfirm && $this.hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.serialize();
        } else {
            if ($this.hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.find('input,select,textarea').serialize();
        }

        //防止重复提交
        var isRepeatButton = $this.hasClass('no-repeat-button');
        if (isRepeatButton) {
            $this.prop('disabled', true);
        }

        //ajax提交
        $.ajax({
            type: "POST",
            url: target,
            data: query,
            dataType: "json",
            beforeSend: function () {
                layer.msg('正在处理,请稍等...', {
                    icon: 16,
                    time: 100000,
                    shade: [0.5, '#333', true]
                });
            },
            success: function (result) {
                if (result.code == '1') {
                    layer.msg(result.msg, {
                        icon: 1,
                        time: 500,
                        shade: [0.5, '#000', true]
                    }, function () {
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                    });
                } else {
                    layer.msg(result.msg, {icon: 5});
                }
            },
            error: function (xhr, status, error) {
                layer.msg('请求失败: ' + error, {icon: 5});
            },
            complete: function () {
                //执行完之后执行
                if (isRepeatButton) {
                    $this.prop('disabled', false);
                }
            },
        });//end ajax post
    }

    return false;
});


// 提交~针对本页
$("body").on("click", ".ajax-post-trace", function () {
    var target, query, form;
    var target_form = $(this).attr('target-form');
    var that = this;
    var nead_confirm = false;
    var fun = $(this).attr('data-calback');//判断是否有回调函数

    if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        form = $('.' + target_form);
        if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
            form = $('.hide-data');
            query = form.serialize();
        } else if (form.get(0) == undefined) {

            return false;

        } else if (form.get(0).nodeName == 'FORM') {

            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }

            if ($(this).attr('url') !== undefined) {
                target = $(this).attr('url');
            } else {
                target = form.get(0).action;
            }
            query = form.serialize();

        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {

            form.each(function (k, v) {
                if (v.type == 'checkbox' && v.checked == true) {
                    nead_confirm = true;
                }
            })
            if (nead_confirm && $(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.serialize();
        } else {

            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.find('input,select,textarea').serialize();
        }

        var is_repeat_button = $(that).hasClass('no-repeat-button');
        if (is_repeat_button) {
            $(that).prop('disabled', true);
        }
        $.ajax({
            type: "POST",
            url: target,
            data: query,
            dataType: "json",
            success: function (result) {
                if (result.code == '1') {
                    form[0].reset();
                    layer.msg(result.msg, {icon: 1, time: 500, shade: [0.5, '#000', true]}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                        if (fun != null) {
                            eval(fun);
                        }
                    });
                } else {
                    layer.msg(result.msg, {icon: 5});
                }
            },
            complete: function () { //执行完之后执行
                if (is_repeat_button) {
                    $(that).prop('disabled', false);
                }
            },
        });//end ajax post
    }
    return false;
});

//更改列表字段
$("body").on("change", ".ajax-input", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (target) {
        var val = $this.val();
        var params = {};

        // 是否设置了参数字段
        var ids = $this.attr('data-ids');
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 预处理字符串，转换为标准JSON格式
                var standardizedIds = ids
                    .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                    .replace(/'/g, '"')       // 将单引号转换为双引号
                    .replace(/\\'/g, "'")     // 处理转义的单引号
                    .replace(/\\"/g, '"');    // 处理转义的双引号

                // 使用 JSON.parse 解析标准化后的字符串
                var idsObj = JSON.parse(standardizedIds);
                $.extend(params, idsObj);
            } catch (e) {
                log("ajax-input JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("ajax-input eval解析错误: ", e2);
                }
            }
        }

        // 添加当前元素的id和value
        params.id = $this.attr('data-id');
        params.value = val;

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('INPUT执行地址：' + finalTarget);

        // 执行POST请求
        $.post(finalTarget, params, function (data) {
            if (data.code) {
                parent.layer.msg(data.msg, {icon: 1});
            } else {
                parent.layer.msg(data.msg, {icon: 5});
            }
        }, "json").fail(function (xhr, status, error) {
            parent.layer.msg('请求失败: ' + error, {icon: 5});
        });
    }

    return false;
});

//列表启用关闭
$("body").on("click", ".ajax-checkbox", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');

    if (target) {
        var val = $this.prop('checked') ? 1 : 0;
        var id = $this.attr('data-id');
        var params = {id: id, value: val};

        // 是否设置了参数字段
        var ids = $this.attr('data-ids');
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 预处理字符串，转换为标准JSON格式
                var standardizedIds = ids
                    .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                    .replace(/'/g, '"')       // 将单引号转换为双引号
                    .replace(/\\'/g, "'")     // 处理转义的单引号
                    .replace(/\\"/g, '"');    // 处理转义的双引号

                // 使用 JSON.parse 解析标准化后的字符串
                var idsObj = JSON.parse(standardizedIds);
                $.extend(params, idsObj);
            } catch (e) {
                log("ajax-checkbox JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("ajax-checkbox eval解析错误: ", e2);
                }
            }
        }

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('CHECKBOX执行地址：' + finalTarget);

        // 执行POST请求
        $.post(finalTarget, params, function (data) {
            if (data.code) {
                layer.msg(data.msg, {icon: 1});
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        }, "json").fail(function (xhr, status, error) {
            layer.msg('请求失败: ' + error, {icon: 5});
        });
    }
    return true;
});


//列表排序处理
$("body").on("change", ".ajax-sort", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');
    var val = $this.val();

    if (target) {
        // 验证输入值是否为正整数
        if (!((/^(\+|-)?\d+$/.test(val)) && val >= 0)) {
            layer.msg('请输入正整数', {icon: 5});
            return false;
        }

        var params = {
            id: $this.attr('data-id'),
            value: val
        };

        // 是否设置了参数字段
        var ids = $this.attr('data-ids');
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 预处理字符串，转换为标准JSON格式
                var standardizedIds = ids
                    .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                    .replace(/'/g, '"')       // 将单引号转换为双引号
                    .replace(/\\'/g, "'")     // 处理转义的单引号
                    .replace(/\\"/g, '"');    // 处理转义的双引号

                // 使用 JSON.parse 解析标准化后的字符串
                var idsObj = JSON.parse(standardizedIds);
                $.extend(params, idsObj);
            } catch (e) {
                log("ajax-sort JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("ajax-sort eval解析错误: ", e2);
                }
            }
        }

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('SORT执行地址：' + finalTarget);

        // 执行POST请求
        $.post(finalTarget, params, function (data) {
            if (data.code) {
                layer.msg(data.msg, {icon: 1});
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        }, "json").fail(function (xhr, status, error) {
            layer.msg('请求失败: ' + error, {icon: 5});
        });
    }

    return false;
});

//排列表字段序，可以传多个参数
$("body").on("change", ".ajax-field", function () {
    var $this = $(this);
    var target = $this.attr('href') || $this.attr('url') || $this.attr('data-url');
    var val = $this.val();

    if (target) {
        var params = {
            id: $this.attr('data-id'),
            value: val
        };

        // 是否设置了参数字段
        var ids = $this.attr('data-ids');
        if (typeof ids !== "undefined" && ids !== null && ids !== "") {
            try {
                // 预处理字符串，转换为标准JSON格式
                var standardizedIds = ids
                    .replace(/&quot;/g, '"')  // 将HTML实体转换为双引号
                    .replace(/'/g, '"')       // 将单引号转换为双引号
                    .replace(/\\'/g, "'")     // 处理转义的单引号
                    .replace(/\\"/g, '"');    // 处理转义的双引号

                // 使用 JSON.parse 解析标准化后的字符串
                var idsObj = JSON.parse(standardizedIds);
                $.extend(params, idsObj);
            } catch (e) {
                log("ajax-field JSON解析错误: ", e);
                // 如果JSON解析失败，尝试使用eval作为备选方案
                try {
                    var idsObj = eval('(' + ids + ')');
                    $.extend(params, idsObj);
                } catch (e2) {
                    log("ajax-field eval解析错误: ", e2);
                }
            }
        }

        // 构建最终URL
        var finalTarget = target;
        if (Object.keys(params).length > 0) {
            var separator = target.indexOf('?') !== -1 ? '&' : '?';
            finalTarget = target + separator + $.param(params);
        }

        log('FIELD执行地址：' + finalTarget);

        // 执行POST请求
        $.post(finalTarget, params, function (data) {
            if (data.code) {
                layer.msg(data.msg, {icon: 1});
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        }, "json").fail(function (xhr, status, error) {
            layer.msg('请求失败: ' + error, {icon: 5});
        });
    }

    return false;
});

/**
 * 提示或提示并跳转
 */
var lqfalert = function (data) {
    if (data.code) {
        // 成功情况
        layer.msg(data.msg, {icon: 1});
    } else {
        // 失败情况
        if (typeof data.msg == "string") {
            // 单个错误消息
            layer.msg(data.msg, {icon: 5});
        } else if (typeof data.msg == "object") {
            // 多个错误消息
            var err_msg = '';
            for (var item in data.msg) {
                err_msg += "Θ " + data.msg[item] + "<br/>";
            }
            layer.msg(err_msg, {icon: 5});
        } else {
            // 其他情况
            layer.msg('操作失败', {icon: 5});
        }
    }
    if (data.url) {
        // 如果有URL则跳转
        setTimeout(function () {
            location.href = data.url;
        }, 1500);
    } else if (data.code && !data.url) {
        // 成功且没有URL则刷新页面
        setTimeout(function () {
            location.reload();
        }, 1500);
    }
};

// 保持当前页码而不是重置到第一页
var runCurrTurnPage = function () {
    if (window.ajaxPagination && window.ajaxPagination.instances.size > 0) {
        if (window.ajaxPagination.instances.size === 1) {
            const firstInstance = window.ajaxPagination.instances.values().next().value;
            turnPage(firstInstance.pageNum, firstInstance.tableId);
        } else {
            // 尝试通过上下文找到对应实例
            const $container = $this.closest('.ibox-content, .page-list-container');
            if ($container.length > 0) {
                const tableId = $container.attr('data-list-table');
                if (tableId) {
                    const instance = window.ajaxPagination.getInstance(tableId);
                    if (instance) {
                        turnPage(instance.pageNum, tableId);
                    } else {
                        turnPage(pageNum);
                    }
                } else {
                    turnPage(pageNum);
                }
            } else {
                turnPage(pageNum);
            }
        }
    } else {
        turnPage(pageNum);
    }
}