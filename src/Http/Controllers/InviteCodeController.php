<?php
namespace JingBh\AutoZP\Http\Controllers;

use App\Http\Controllers\Controller;

class InviteCodeController extends Controller
{
    public function show() {
        return view("autozp::verify_invite");
    }

    public function verify() {

    }
}
