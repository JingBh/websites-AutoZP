<?php
namespace JingBh\AutoZP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string id 教育ID
 * @property string invite_code 使用的邀请码
 * @property Carbon updated_at
 * @property Carbon created_at
 */
class AutoZPUser extends Model
{
    protected $table = "autozp_user";

    public $incrementing = false;

    protected $keyType = "string";

    public function getInviteCodeAttribute() {
        $obj = $this->inviteCode();
        return empty($obj) ? null : $obj->code;
    }

    /**
     * 获取当前使用的邀请码
     *
     * @return AutoZPInviteCode
     */
    public function inviteCode() {
        return AutoZPInviteCode::where([
            ["code", "=", $this->invite_code],
            ["enabled", "=", true]
        ])->get();
    }
}
