<?php
namespace JingBh\AutoZP;

use Illuminate\Support\Facades\Cookie;
use JingBh\AutoZP\Models\AutoZPInviteCode;

class InviteCode
{
    /**
     * 检查邀请码是否正确
     *
     * @param string|null $code 要检查的邀请码，若为空则自动从 Cookie 中获取
     * @param bool $must_unused 是否必须为未使用过的邀请码
     * @return bool
     */
    public static function isValid($code=null, $must_unused=false) {
        if (blank($code)) $code = self::getFromCookie();
        $obj = AutoZPInviteCode::where([
            ["code", "=", $code],
            ["enabled", "=", true]
        ])->first();
        if (empty($obj)) {
            return false;
        } elseif ($must_unused === true) {
            return empty($obj->user);
        } else return true;
    }

    /**
     * 从 Cookie 中获取用户当前邀请码
     *
     * @return string|null
     */
    public static function getFromCookie() {
        return Cookie::get("autozp_invite_code", null);
    }
}
