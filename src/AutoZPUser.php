<?php
namespace JingBh\AutoZP;

use Illuminate\Support\Facades\Cache;
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

    /**
     * 用户教育ID
     *
     * @var string
     */
    public $userId = "";

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
                if (filled($this->userInfo)) $this->userId = $this->userInfo["userNumber"];
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
        $this->updateUserInfo();
        $obj = Model::find($this->userId);
        if (empty($obj)) {
            $obj = new Model;
            $obj->id = $this->userId;
            $obj->invite_code = InviteCode::getFromCookie();
            $obj->save();
        }
        $this->obj = $obj;
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
     * 将 Token 保存在 Session 和 Cache 中
     */
    public function saveToken() {
        Session::put("autozp_token", $this->token);
        Cache::put("autozp_token_{$this->userId}", $this->token, 3600);
    }

    /**
     * 将保存在 Session 和 Cache 中的 Token 删除
     */
    public function clearToken() {
        Session::remove("autozp_token");
        Cache::forget("autozp_token_{$this->userId}");
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
     * 不填验证码时为自动识别
     *
     * @param string $username
     * @param string $password
     * @param string|null $flag
     * @param string|null $validateCode
     * @return array
     * @throws \Throwable
     */
    public static function login($username, $password, $flag=null, $validateCode=null) {
        $user = Model::find($username);
        if (filled($user) || InviteCode::isValid(null, true)) {
            $token = Cache::get("autozp_token_{$username}");
            if (blank($token)) {
                if (filled($validateCode) && filled($flag)) {
                    $response = WebSpider::login($username, $password, $flag, $validateCode);
                } else $response = NoMoreValidateCode::getAndFuckIt($username, $password);
            } else {
                $response = [
                    "success" => true,
                    "message" => "已从缓存中获取登录信息",
                    "data" => ["token" => $token]
                ];
            }
        } else $response = [
            "success" => false,
            "message" => "您的邀请码不能用来登录此账号。",
            "data" => null
        ];
        if ($response["success"] === true) {
            $response["object"] = new self($response["data"]["token"]);
        } else $response["object"] = new self;
        $response["object"]->userId = $username;
        if ($response["object"]->isLoggedIn()) {
            $response["object"]->updateModel();
            $response["object"]->saveToken();
        } else {
            $response["success"] = false;
            $response["message"] = "这通常并不是您的错，请直接重试。";
            $response["object"]->clearToken();
        }
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
