<?php
namespace JingBh\AutoZP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

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
        return self::apiJsonResponse($response);
    }

    public static function userInfo($token) {
        $client = self::http($token);
        $response = $client->get("school/user/getUserInfo");
        return self::apiJsonResponse($response);
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

    /**
     * 生成一个 Guzzle HTTP 客户端
     *
     * @param string|null $token Bearer Token
     * @param bool $api 是否为调用 API
     * @return Client
     */
    protected static function http($token=null, $api=true) {
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

    /**
     * 将 API 响应转换为数组
     *
     * @param ResponseInterface $response
     * @return array
     */
    protected static function apiJsonResponse($response) {
        $body = json_decode($response->getBody()->getContents(), true);
        return [
            "success" => $body["success"],
            "message" => $body["message"],
            "data" => $body["success"]
                ? $body["data"] : null
        ];
    }
}
