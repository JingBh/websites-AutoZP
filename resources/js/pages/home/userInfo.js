$.get("user/info", function(data) {
    // 根据是否有教育ID判断是否已登录
    if (data["userNumber"]) {
        $("#loginButton").slideUp(200);
        $("#logoutButton").slideDown(200)
            .removeAttr("disabled");
    } else {
        $("#logoutButton").slideUp(200);
        $("#loginButton").slideDown(200)
            .removeAttr("disabled");
    }
}).fail(function() {
    alert("加载信息失败，请尝试刷新页面重试。");
}).always(function() {
    $("#userInfoLoading").slideUp(200);
});
