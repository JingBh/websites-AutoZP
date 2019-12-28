if ($("#loginInputPassword").val())
    $("#loginRememberPassword")[0].checked = true;

$("#loginSubmit").click(function() {
    $(this).attr("disabled", "disabled")
        .html('<span class="spinner-border spinner-border-sm"></span> 请稍候...');
    $("#loginTimeHint").removeClass("text-muetd").addClass("text-info");
    $("#loginError, #loginSuccess").hide();
    $.post("login", {
        "username": $("#loginInputUsername").val(),
        "password": $("#loginInputPassword").val(),
        "remember": $("#loginRememberPassword")[0].checked
    }).done(function(data) {
        if (data["success"] === true) {
            $("#loginSuccess").slideDown(200);
            location.reload();
        } else {
            $("#loginError").slideDown(200)
                .children("span").text(data["message"]);
        }
    }).fail(function() {
        alert("请求出错，请稍后重试。");
    }).always(function() {
        $("#loginSubmit").removeAttr("disabled").html("提交");
        $("#loginTimeHint").addClass("text-muetd").removeClass("text-info");
    });
});

$("[id^='loginInput']").keyup(function(event) {
    if (event.key == "Enter") $("#loginSubmit").click();
});

$("#logoutButton").click(function() {
    $.post("logout").done(function() {
        location.reload();
    });
});
