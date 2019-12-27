<?php

Route::view("/", "autozp::home");

Route::any("invite_code", "InviteCodeController@show");
