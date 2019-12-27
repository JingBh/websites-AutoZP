require("../packages");

$("#submitInviteCode").click(function() {
    $(this).attr("disabled", "disbaled")
        .html('<span class="spinner-border spinner-border-sm"></span> 请稍候...');
    $.post("/invite_code/verify").fail(function() {
        alert("请求出错，请稍后重试。");
    }).always(function() {
        $("#submitInviteCode").removeAttr("disabled").html("提交");
    });
});
