@extends("autozp::layouts.layout")

@section("content")
    <div class="container my-3 mt-md-5" id="headContainer">
        <div class="row">
            <div class="col-auto col-sm" id="logoSection">
                <h1 class="mb-1">@component("autozp::components.logo")@endcomponent</h1>
                <p class="mb-0">一个<span class="font-weight-light mx-1">极简</span>综评系统客户端</p>
            </div>
            <div class="col col-sm-5 col-md-4 col-lg-3 col-xl-2">
                <button class="btn btn-outline-primary btn-block" id="loginButton" data-toggle="modal" data-target="#loginModal" disabled="disabled">登录</button>
                <button class="btn btn-outline-danger btn-block hide" id="logoutButton" disabled="disabled">登出</button>
                <button class="btn btn-outline-secondary btn-block" id="aboutButton" disabled="disabled">关于</button>
            </div>
        </div>
        <hr>
        <p class="text-info" id="userInfoLoading">
            <span class="spinner-border spinner-border-sm"></span>
            正在加载用户信息...
        </p>
    </div>
    @component("autozp::components.modal")
        @slot("id", "loginModal")
        @slot("title", "登录综评系统")
        <p>请输入<strong>综评系统</strong>的用户名和密码来登录。</p>
        <div id="loginForm">
            <div class="form-group">
                <label for="loginInputUsername">教育ID</label>
                <input type="text" class="form-control" id="loginInputUsername" minlength="8" maxlength="8" placeholder="请输入教育ID" value="{{ Cookie::get("autozp_username") }}">
            </div>
            <div class="form-group">
                <label for="loginInputPassword">密码</label>
                <input type="password" class="form-control" id="loginInputPassword" placeholder="请输入密码" value="{{ Cookie::get("autozp_password") }}">
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="loginRememberPassword">
                    <label class="custom-control-label" for="loginRememberPassword">允许 AutoZP 存储密码</label>
                </div>
                <p class="small form-text text-muted">
                    <strong>一些高级功能需要存储密码才可用。</strong><br>
                    AutoZP 保证在您未同意的情况下<strong>不</strong>私自存储您的密码。
                </p>
            </div>
            <p class="small text-muted mb-0" id="loginTimeHint">登录操作需要大约 10 秒的时间，请耐心等待。</p>
            <p class="text-danger mb-0 mt-2 hide" id="loginError">登录失败：<span></span></p>
            <p class="text-success mb-0 mt-2 hide" id="loginSuccess">登录成功，正在自动<a href="javascript:location.reload();">跳转</a>...</p>
        </div>
        @slot("footer")
            <button class="btn btn-primary" id="loginSubmit">登录</button>
        @endslot
    @endcomponent
@endsection

@section("bodyjs")
    <script src="{{ asset(mix("js/home.js", "vendor/jingbh/autozp")) }}"></script>
@endsection
