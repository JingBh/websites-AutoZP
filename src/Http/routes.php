<?php

Route::get("/", "HomeController@home");

Route::get("invite_code", "InviteCodeController@show");
Route::post("invite_code/verify", "InviteCodeController@verify");

Route::post("login", "UserController@login");
Route::any("logout", "UserController@logout");
Route::get("login/validateCode", "UserController@validateCode");
Route::get("user/info", "UserController@userInfo");
Route::get("user/photo", "UserController@photo");

Route::get("terms", "StaticController@terms")->name("terms");
