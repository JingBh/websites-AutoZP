<?php
namespace JingBh\AutoZP\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JingBh\AutoZP\AutoZPUser;
use JingBh\AutoZP\WebSpider;

class UserController extends Controller
{
    public function userInfo() {
        $result = AutoZPUser::getTokenFromSession()->updateUserInfo();
        return response()->json($result);
    }

    public function login(Request $request) {
        $username = $request->post("username");
        $password = $request->post("password");
        $remember = $request->post("remember") == "true";
        $result = AutoZPUser::login($username, $password);
        if ($result["success"] === true && $remember === true) {
            $result["object"]->savePassword($password);
        } else $result["object"]->clearPassword();
        return response()->json($result);
    }

    public function logout(Request $request) {
        AutoZPUser::logout();
        if ($request->isMethod("GET")) {
            return redirect("/autozp");
        } else return response()->json([true, null]);
    }

    public function validateCode() {
        $result = WebSpider::getValidateCode();
        return response()->json([true, $result]);
    }
}
