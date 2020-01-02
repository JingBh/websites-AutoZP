const avatarUrl = require("../../utils/images").avatar;

$.get("user/info", function(data) {
    // 判断是否已登录
    if (data[1]) {
        let info = data[1];
        $("#userName").text(info["name"]);
        $("#userSchool").text(info["school"]);
        $("#loginButton").slideUp(200);
        $("#logoutButton").slideDown(200).removeAttr("disabled");
        $("#userInfo").show();
    } else {
        $("#logoutButton").slideUp(200);
        $("#loginButton").slideDown(200).removeAttr("disabled");
    }
}).fail(function() {
    alert("加载信息失败，请尝试刷新页面重试。");
}).always(function() {
    $("#userInfoLoading").slideUp(200);
});

$("#userAvatar").attr("src", avatarUrl);
