<?php
namespace JingBh\AutoZP\Traits;

use JingBh\AutoZP\SchoolsId;

trait StudentInfo
{
    /**
     * 获取学生姓名
     *
     * @return string
     */
    public function getName() {
        $userInfo = $this->updateUserInfo();
        return filled($userInfo) ? $userInfo["name"] : "";
    }

    /**
     * 获取学生所在学校
     *
     * @return string
     */
    public function getSchool() {
        $userInfo = $this->updateUserInfo();
        if (filled($userInfo)) {
            $schoolRaw = $userInfo["orgName"];
            $school = str_replace("主校区", "", $schoolRaw);
            return trim($school);
        } else return "";
    }

    /**
     * 获取学校ID
     * 不一定成功
     *
     * @return null|string
     */
    public function getSchoolId() {
        $school = $this->getSchool();
        $info = SchoolsId::get($school);
        return $info["id"];
    }

    /**
     * 获取学生性别
     *
     * @return string
     */
    public function getGender() {
        $userInfo = $this->updateUserInfo();
        return filled($userInfo) ? $userInfo["sex"] : "";
    }

    /**
     * 获取学年与学期信息
     *
     * @return array
     */
    public function getTermInfo() {
        $userInfo = $this->updateUserInfo();
        if (filled($userInfo)) {
            // TODO: Should update Lesson Info.
            return [
                "yearId" => $userInfo["schoolyearId"],
                "name" => $userInfo["schoolyearName"],
                "id" => $userInfo["choolsemesterId"],
                "term" => $userInfo["choolsemesterName"]
            ];
        } else return [];
    }

    /**
     * 获取班级信息
     *
     * @return array
     */
    public function getClass() {
        $userInfo = $this->updateUserInfo();
        if (filled($userInfo)) {
            return $userInfo["classList"][0];
        } else return [];
    }

    /**
     * 获取入学年份
     * 不一定成功
     *
     * @return null|int
     */
    public function getGradeYear() {
        $classInfo = $this->getClass();
        $match = preg_match('/([0-9]*)级/', $classInfo["name"], $matches);
        return $match == 1 ? intval($matches[1]) : null;
    }
}
