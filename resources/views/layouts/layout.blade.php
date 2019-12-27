<!doctype html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield("meta")
    <link rel="stylesheet" href="{{ asset(mix("css/app.css", "vendor/jingbh/autozp")) }}">
    <title>@yield("title", "主页") - AutoZP</title>
</head>
<body>
@yield("content")
<script src="{{ asset(mix("js/manifest.js", "vendor/jingbh/autozp")) }}"></script>
<script src="{{ asset(mix("js/vendor.js", "vendor/jingbh/autozp")) }}"></script>
@yield("bodyjs")
</body>
</html>
