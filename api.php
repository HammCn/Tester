<?php
header("Content-Type: application/json;chartset=uft-8");

if (isset($_GET['key'])) {
    $key = htmlspecialchars($_GET['key']);
    $path = "./data/" . $key . ".php";
    $result = '';
    if (file_exists($path)) {
        $result = file_get_contents($path);
    }
    jok('', $result);
    die;
}

$post = file_get_contents('php://input');
$post = json_decode($post, true);
if (!$post) {
    jerr('参数错误');
}
$url = $post['url'];
if (!$url) {
    jerr('请填写API请求地址');
}
$method = $post['method'] ?? 'GET';
$body = $post['body'] ?? "";
$header = $post['header'] ?? "";
$cookie = $post['cookie'] ?? "";

if (substr($header, 0 - strlen(PHP_EOL)) == PHP_EOL) {
    $header = substr($header, 0, strlen($header) - strlen(PHP_EOL));
}
$header = explode(PHP_EOL, $header);
$result = curlHelper($url, $body, $header, $cookie, $method);

$key = sha1(time() . rand(10000000, 99999999));
$dir = "./data/" . date('Ymd');
if (!is_dir($dir)) {
    mkdir($dir);
}
file_put_contents($dir . "/" . $key . ".php", json_encode([
    "url" => $url,
    "body" => $body,
    "header" => $header,
    "cookie" => $cookie,
    "method" => $method
]));
$result['key'] = date('Ymd') . "/" . $key;
jok('', $result);

function jok($msg, $data)
{
    echo json_encode([
        'code' => 200,
        'msg' => $msg,
        'data' => $data,
    ]);
    die;
}
function jerr($msg)
{
    echo json_encode([
        'code' => 500,
        'msg' => $msg,
    ]);
    die;
}
function curlHelper($url, $data = null, $header = [], $cookies = "", $method = 'GET')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);

    switch ($method) {
        case "GET":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PATCH":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "TRACE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "TRACE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "OPTIONS":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "HEAD":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        default:
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);
    $output = [];
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // 根据头大小去获取头信息内容
    $output['header'] = substr($response, 0, $headerSize);
    $output['body'] = substr($response, $headerSize, strlen($response) - $headerSize);
    $output['detail'] = curl_getinfo($ch);
    
    curl_close($ch);
    return $output;
}
