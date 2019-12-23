<?php
namespace JingBh\AutoZP;

use GuzzleHttp\Client;

class WebSpider
{
    /**
     * 登录综评系统
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $flag 获取验证码时返回的 $flag
     * @param string $validate_code 验证码
     * @return array 其中token为身份验证使用的Bearer秘钥
     */
    public static function login($username, $password, $flag, $validate_code) {
        $client = self::http();
        $encrypted_password = self::encryptPassword($password, $validate_code);
        $response = $client->post("auth/login", [
            "form_params" => [
                "application" => "student",
                "usernameOrEmail" => $username,
                "password" => $encrypted_password,
                "flag" => $flag,
                "validateCode" => $validate_code
            ]
        ]);
        $body = json_decode($response->getBody()->getContents());
        return [
            "success" => $body->success,
            "code" => $body->code,
            "message" => $body->message,
            "token" => $body->success
                ? $body->data->token : null
        ];
    }

    public static function userInfo($token) {

    }

    /**
     * 获取综评系统登录验证码
     *
     * @return array
     */
    public static function getValidateCode() {
        $client = self::http();
        $response = $client->get("auth/validateCode");
        $body = json_decode($response->getBody()->getContents());
        $image_url = "data:image/jpeg;base64," . $body->data->imgsrc;
        $image = file_get_contents($image_url);
        return [
            "flag" => $body->data->flag,
            "image_url" => $image_url,
            "image_base64" => $body->data->imgsrc,
            "image" => $image
        ];
    }

    protected static function encryptPassword($password, $validate_code) {
        $key = self::getPublicKey();
        $data = "{$validate_code}:{$password}";
        $encrypt = openssl_public_encrypt($data, $encrypted, $key);
        if ($encrypt) $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    protected static function getPublicKey() {
        $path = realpath(__DIR__ . "/../keys/password-encrypt.pem");
        return file_get_contents($path);
    }

    protected static function http($api=true, $token=null) {
        $options = [
            "base_uri" => $api
                ? "http://gzzp.bjedu.cn:8004/"
                : "http://gzzp.bjedu.cn/",
            "connect_timeout" => 30,
            "headers" => [
                "User-Agent" => "Mozilla/5.0 JWS AutoZPBot",
                "Referer" => "http://gzzp.bjedu.cn/",
                "Origin" => "http://gzzp.bjedu.cn"
            ],
            "timeout" => 30
        ];

        if (filled($token)) {
            $options["headers"]["Authorization"] = "Bearer {$token}";
            $options["query"]["token"] = $token;
        }

        return new Client($options);
    }
}
