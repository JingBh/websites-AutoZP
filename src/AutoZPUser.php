<?php
namespace JingBh\AutoZP;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use JingBh\AutoZP\Models\AutoZPUser as Model;

class AutoZPUser
{
    /**
     * 用户名密码 Cookie 过期时间
     *
     * @var int
     */
    const cookie_time = 60 * 24 * 30;

    /**
     * @var string
     */
    protected $token = "";

    /**
     * 用户模型
     *
     * @var Model|null
     */
    protected $obj = null;

    /**
     * 从综评系统获取的用户信息
     *
     * @var array|null
     */
    protected $userInfo = null;

    public function __construct($token="") {
        $this->token = $token;
    }

    /**
     * 从综评系统获取用户信息
     *
     * @return array|null
     */
    public function updateUserInfo() {
        if (blank($this->userInfo)) {
            try {
                $response = WebSpider::userInfo($this->token);
                $this->userInfo = $response["data"];
            } catch (\Exception $e) {
                $this->userInfo = null;
            }
        }
        return $this->userInfo;
    }

    /**
     * 实例化用户模型
     *
     * @return Model
     */
    public function updateModel() {
        $userInfo = $this->updateUserInfo();
        if (filled($userInfo)) {
            $id = $userInfo["userNumber"];
            $obj = Model::find($id);
            if (empty($obj)) {
                $obj = new Model;
                $obj->id = $id;
                $obj->invite_code = InviteCode::getFromCookie();
                $obj->save();
            }
            $this->obj = $obj;
        }
        return $this->obj;
    }

    /**
     * 检查是否已登录
     *
     * @return bool
     */
    public function isLoggedIn() {
        $this->updateUserInfo();
        return filled($this->userInfo);
    }

    /**
     * 将 Token 保存在 Session 中
     */
    public function saveToken() {
        Session::put("autozp_token", $this->token);
    }

    /**
     * 在数据库和 Cookie 中保存密码
     * 密码已加密存储
     *
     * @param $password
     * @return void
     */
    public function savePassword($password) {
        $obj = $this->updateModel();
        if (filled($obj)) {
            $obj->password = $password;
            $obj->save();
        }
        $this->obj = $obj;
        Cookie::queue("autozp_username", $obj->id, self::cookie_time);
        Cookie::queue("autozp_password", $obj->password, self::cookie_time);
    }

    /**
     * 清除存储的密码
     *
     * @return void
     */
    public function clearPassword() {
        $obj = $this->updateModel();
        if (filled($obj)) {
            $obj->password = null;
            $obj->save();
        }
        $this->obj = $obj;
    }

    /**
     * 登录综评系统
     *
     * @param $username
     * @param $password
     * @return array
     * @throws \Throwable
     */
    public static function login($username, $password) {
        $user = Model::find($username);
        if (filled($user) || InviteCode::isValid(null, true)) {
            $response = NoMoreValidateCode::getAndFuckIt($username, $password);
        } else $response = [
            "success" => false,
            "message" => "您的邀请码不能用来登录此账号。",
            "data" => null
        ];
        if ($response["success"] === true) {
            $response["object"] = new self($response["data"]["token"]);
            $response["object"]->updateModel();
            $response["object"]->saveToken();
        } else $response["object"] = new self;
        return $response;
    }

    /**
     * 退出登录并清理 Session
     */
    public static function logout() {
        Session::remove("autozp_token");
    }

    /**
     * 从 Session 中获取当前用户 Token
     *
     * @param boolean $construct 是否实例化此类
     * @return self|string|null
     */
    public static function getTokenFromSession($construct=true) {
        $token = Session::get("autozp_token");
        return $construct ? new self($token) : $token;
    }
}
