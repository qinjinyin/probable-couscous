$("#add").click(function(){
    var apiname = $("#apiname").val();
    var apibs = $("#apibs").val();
    var apiimg = $("#apiimg").val();
    var isuse = $(':radio[name="isuse"]:checked').val();
    var islocal = $(':radio[name="islocal"]:checked').val();
    
    //远程
    var apiurl = $("#apiurl").val();
    var rarray = $("#rarray").val();
    var rimg = $("#rimg").val();
    var rvideo = $("#rvideo").val();
    var rtitle = $("#rtitle").val();
    var act = $("#act").val();
    var iid = $("#iid").val();
    if (apiname=='') {
        toastr.error('接口名称不能为空', '错误', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
        return false;
    }
    if (apibs=='') {
        toastr.error('接口标识不能为空', '错误', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
        return false;
    }

    $.ajax({
        type: "post",
        url: "/admin/inf/ajaxaddinf",
        dataType: "json",
        data : { "apiname" :apiname,"apibs" :apibs,"apiimg" :apiimg,"isuse" :isuse,"islocal" :islocal,"apiurl" :apiurl,"rarray" :rarray,"rimg" :rimg,"rvideo" :rvideo,"rtitle" :rtitle,"act" :act,"iid" :iid},
        async: true,
        success: function(data) {
            console.log(data);
            if (data.code==1) {
                 //window.location.href="你所要跳转的页面";
                toastr.success(data.msg, '成功', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                //message('success', data.msg);
                setTimeout(function() {
                    location.href = data.url;
                }, 1000);
            }
            if (data.code==0) {
				toastr.info(data.msg, '警告', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                }
            },
            error: function() {
            	toastr.error('请检查你的网络', '错误', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                //message('error', '请检查你的网络');
            }
    	
    })
})