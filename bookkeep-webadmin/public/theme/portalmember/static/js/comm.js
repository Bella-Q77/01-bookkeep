//为true输出日志
var debug = true;

/**
 * 打印日志
 */
function log(data) {
    if (debug) {
        if (typeof (data) == "object") {
            console.log(JSON.stringify(data)); //console.log(JSON.stringify(data, null, 4));
        } else {
            console.log(data);
        }
    }
}

$(document).ready(function () {

    //nav
    // var obj = null;
    // var As = document.getElementById('starlist').getElementsByTagName('a');
    // obj = As[0];
    // for (i = 1; i < As.length; i++) {
    //     if (window.location.href.indexOf(As[i].href) >= 0)
    //         obj = As[i];
    // }
    //obj.id='selected';

    //nav
    $("#mnavh").click(function () {
        $("#starlist").toggle();
        $("#mnavh").toggleClass("open");

        $(".left-col").toggleClass("open");

    });

    //search	
    $(".searchico").click(function () {
        $(".search").toggleClass("open");
    });

    //searchclose
    $(".searchclose").click(function () {
        $(".search").removeClass("open");
    });

    //nav menu
    $(".menu").click(function (event) {
        $(this).children('.sub').slideToggle();
    });

    //tab
    $('.tab_buttons li').click(function () {
        $(this).addClass('newscurrent').siblings().removeClass('newscurrent');
        $('.newstab>div:eq(' + $(this).index() + ')').show().siblings().hide();
    });

    $('.btn-history').click(function () {
        window.history.back();
    });

});

//ajax打开
$("body").on("click", ".ajax-open", function () {

    if ((target = $(this).attr('href')) || (target = $(this).attr('url')) || (target = $(this).attr('data-url'))) {

        var tit = $(this).attr('data-title');//打开标题
        var ids =$(this).attr('data-ids');//参数传，支持多个参数传送 格式：data-ids="{'tid':'2',''name':'张三'}"
        var fun =$(this).attr('data-calback');//回调函数

        //是否设置了参数字段
        if( typeof(ids)!="undefined" && ids!=0 ){
            var ids=($.param(eval('('+ids+')'),true));
            var target=target+"?"+ids;
        }

        //是否设置了单个值
        var id = $(this).attr("data-id");
        if (typeof (id) != "undefined" && id != 0) {
            var target = target + "?id=" + id;
        }

        log('打开地址：'+target);
        layer.open({
            type: 2,
            title: false,
            shadeClose: false,
            //btn: ['关闭'],
            fixed: true, //不固定
            area: ['90%', '90%'],
            content: target,
            success: function(layero, index) {
                layer.iframeAuto(index);
            },
            end: function () {
                log(fun);
                if(fun!=null){
                    eval(fun);
                }else{

                }
            }
        });
    }
    return false;
});


//ajax get请求
$("body").on("click", ".ajax-get", function () {

    var target;

    if ($(this).hasClass('confirm')) {

        if (!confirm('确认要执行该操作吗?')) {

            return false;
        }
    }

    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {

        if ($(this).attr('is-jump') == 'true') {

            location.href = target;

        } else {
            $.get(target).success(function (data) {

                flyalert(data);
            });
        }
    }

    return false;
});


// PJAX模式重写表单POST提交处理
$("body").on("click", ".ajax-post", function () {

    var target, query, form;
    var target_form = $(this).attr('target-form');
    var that = this;
    var nead_confirm = false;


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
            success: function (rtnJsonData) {
                flyalert(rtnJsonData);
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


/**
 * 提示或提示并跳转
 */
var flyalert = function (data) {
    log(data);
    if (data.code) {

        layer.msg(data.msg, {icon: 1});
    } else {

        if (typeof data.msg == "string") {

            layer.msg(data.msg, {icon: 5});
        } else {

            var err_msg = '';

            for (var item in data.msg) {
                err_msg += "Θ " + data.msg[item] + "<br/>";
            }
            layer.msg(err_msg, {icon: 5});
        }
    }

    if (data.url) {
        setTimeout(function () {
            location.href = data.url;
        }, 1500);
    }

    if (data.code && !data.url) {
        setTimeout(function () {
            location.reload();
        }, 1500);
    }
};
