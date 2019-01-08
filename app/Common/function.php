<?php
/**
 * curl获取接口数据
 * @param $url 链接地址
 * @return mixed
 */
function https_request($url, $data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);

    try{
        $ret = json_decode($output, true);
    }
    catch (Exception $e) {
        $ret = $output;
    }

    return $ret;
}

use Illuminate\Support\Facades\Redis;

/**
 * 从Redis获取string类型的值
 * @param $key
 * @return mixed
 */
function getv_from_r($key) {
    return Redis::get($key);
}

/**
 * 获取小程序二维码
 * @param $url
 * @param $post_data
 * @param $filename
 * @return string
 */
function saveQrcode($url, $post_data, $filename) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);

    $filepath = dirname($filename);
    if(!file_exists($filepath)) {
        mkdir($filepath);
    }

    file_put_contents($filename, $tmpInfo);

    return config("app.url").$filename;
}

/**
 * 链接跳转
 * @param $url
 * @return \Illuminate\Http\RedirectResponse
 */
function redirectToUrl($url) {
    return response()->redirectTo(config("app.url_prefix").$url);
}


/**
 * 数据验证
 * Class numcheck
 */
class numcheck{
    static function is_float($str) {
        $reg = '/^-?\d+(\.\d+)?$/';
        $result = static::check($reg, $str);
        return $result;
    }

    static function is_int($str) {
        $reg = '/^-?\d+$/';
        $result = static::check($reg, $str);
        return $result;
    }

    private static function check($reg, $str) {
        preg_match($reg, $str, $match);
        if(count($match) > 0) {
            return true;
        }

        return false;
    }
}

/**
 * 将对象类型转为数组
 * @param $obj
 * @return bool|mixed
 */
function objToArray($obj) {
    try{
        $ret = json_decode(json_encode($obj), true);
    }
    catch (Exception $e) {
        $ret = false;
    }

    return $ret;
}

/**
 * 获取controller中方法的注解
 * @param $class 类
 * @param $method 方法
 * @param null $key key
 * @return array|mixed 如果key不为空，返回注解中key的值，否则，返回这个注解解析的内容
 * @throws \ReflectionException
 */
function getFunctionAnnotation($class, $method, $key=null) {
    $class = new \ReflectionClass($class);
    $method = $class->getMethod($method);
    $annotation = $method->getDocComment();
    preg_match_all("/@(\w+)\s+(.*)/", $annotation, $match);
    $map = [];
    for($index = 0, $max = count($match[1]); $index < $max; $index ++) {
        $map[$match[1][$index]] = str_replace("\r", "", $match[2][$index]);
    }

    if(!is_null($key)) {
        return $map[$key]??null;
    }

    return $map;
}

/**
 * 验证是否是中国验证码.
 *
 * @param string $number
 * @return bool
 */
function validateChinaPhoneNumber($number)
{
    return (bool) preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $number);
}

/**
 * 验证用户名是否合法.
 *
 * @param string $username
 * @return bool
 */
function validateUsername($username)
{
    return (bool) preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $username);
}

/**
 * Get user login field.
 *
 * @param string $login
 * @param string $default
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 */
function username($login, $default = 'id')
{
    $map = [
        'email' => filter_var($login, FILTER_VALIDATE_EMAIL),
        'phone' => validateChinaPhoneNumber($login),
        'name' => validateUsername($login),
    ];

    foreach ($map as $field => $value) {
        if ($value) {
            return $field;
        }
    }
    return $default;
}