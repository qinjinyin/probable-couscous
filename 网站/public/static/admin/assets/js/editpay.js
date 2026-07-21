function formdata() {
    $.ajax({
        method: "post",
        data: $('#formdata').serialize(), //jq提供的获取form表单数据的快捷方式，通过form内标签的name属性{"username":"admin","passwd":"123456"}
        url: "/admin/Setpay/editpay",
        success: function(data) { //请求数据并返回结果给success,是一个对象，类似python的字典。回调函数。data只是一个函数的参数，跟上面的data不一样
            if (data.code == 1) {
                //window.location.href="你所要跳转的页面";
                toastr.success(data.msg, '成功', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                //message('success', data.msg);
                setTimeout(function() {
                    location.href = data.url;
                }, 1000);
            }
            if (data.code == 0) {
                toastr.info(data.msg, '警告', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
            }
        }
    })
}