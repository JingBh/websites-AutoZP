let placeholderImageSrc = require("../../utils/placeholderImage").src;

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
        "flag": $("#loginInputValidateFlag").val(),
        "validateCode": $("#loginInputValidateCode").val(),
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

$("#loginValidateCodeImage").attr("src", placeholderImageSrc);
$.get("login/validateCode", function(data) {
    if (data[0] === true) {
        let result = data[1];
        $("#loginValidateCodeImage").attr("src", result["image_url"]);
        $("#loginInputValidateFlag").val(result["flag"]);
    } else $("#loginValidateCodeGroup").hide();
}).fail(function() {
    $("#loginValidateCodeGroup").hide();
});

$("#logoutButton").click(function() {
    $.post("logout").done(function() {
        location.reload();
    });
});

$("#loginAgreeTerms").change(function() {
    let submitButton = $("#loginSubmit");
    if (this.checked === true) {
        submitButton.stop().fadeIn(100);
    } else submitButton.stop().fadeOut(100);
});
