@extends("autozp::layouts.layout")

@section("content")
    @include("autozp::layouts.head")
    @component("autozp::components.modal")
        @slot("id", "loginModal")
        @slot("title", "登录综评系统")
        <p>请输入<strong>综评系统</strong>的用户名和密码来登录。</p>
        <div id="loginForm">
            <div class="form-group">
                <label for="loginInputUsername">教育ID</label>
                <input type="text" class="form-control" id="loginInputUsername" minlength="8" maxlength="8" placeholder="请输入教育ID">
            </div>
            <div class="form-group">
                <label for="loginInputPassword">密码</label>
                <input type="password" class="form-control" id="loginInputPassword" placeholder="请输入密码">
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
        </div>
        <p class="small text-muted mb-0">登录操作可能需要数秒的时间，请耐心等待。</p>
        @slot("footer")
            <button class="btn btn-primary" id="loginSubmit">登录</button>
        @endslot
    @endcomponent
@endsection

@section("bodyjs")
    <script src="{{ asset(mix("js/home.js", "vendor/jingbh/autozp")) }}"></script>
@endsection
