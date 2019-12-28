<?php

Route::get("/", "HomeController@home");

Route::get("invite_code", "InviteCodeController@show");
Route::post("invite_code/verify", "InviteCodeController@verify");

Route::post("login", "UserController@login");
Route::any("logout", "UserController@logout");
Route::get("user/info", "UserController@userInfo");
