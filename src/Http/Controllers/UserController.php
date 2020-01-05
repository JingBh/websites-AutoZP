<?php
namespace JingBh\AutoZP\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JingBh\AutoZP\AutoZPUser;
use JingBh\AutoZP\WebSpider;

class UserController extends Controller
{
    public function userInfo() {
        $obj = AutoZPUser::getTokenFromSession();
        $userInfo = $obj->updateUserInfo();
        $result = filled($userInfo) ? [
            "id" => $obj->userId,
            "name" => $obj->getName(),
            "gender" => $obj->getGender(),
            "school" => $obj->getSchool()
        ] : null;
        return response()->json([true, $result]);
    }

    public function login(Request $request) {
        $username = $request->post("username");
        $password = $request->post("password");
        $flag = $request->post("flag");
        $validateCode = $request->post("validateCode");
        $remember = $request->post("remember") == "true";
        $result = AutoZPUser::login($username, $password, $flag, $validateCode);
        if ($result["success"] === true && $remember === true) {
            $result["object"]->savePassword($password);
        } elseif ($result["object"]->isLoggedIn()) {
            $result["object"]->clearPassword();
        }
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
        Arr::forget($result, ["image", "image_base64"]);
        return response()->json([true, $result]);
    }

    public function photo() {
        $obj = AutoZPUser::getTokenFromSession();
        if ($obj->isLoggedIn()) {
            $photo = $obj->getPhoto();
            if (filled($photo)) {
                return response()->streamDownload(function() use ($photo) {
                    echo $photo;
                }, null, [
                    "Content-Type" => "image/jpeg"
                ], "inline");
            }
        }
        return response("Photo not found.")->setStatusCode(404);
    }
}
