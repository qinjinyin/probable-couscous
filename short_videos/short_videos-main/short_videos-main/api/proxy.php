<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('API_DIR', __DIR__ . '/');
define('DOUYIN_COOKIE_FILE', __DIR__ . '/douyin_cookie.txt');
define('DOUYIN_FALLBACK_API', 'https://api.bugpk.com/api/douyin?url=');

$platforms = [
    'kuaishou'    => ['file' => 'kuaishou/ksjx.php'],
    'pipix'       => ['file' => 'ppxia.php'],
    'izuiyou'     => ['file' => 'zuiyou.php'],
    'toutiaoimg'  => ['file' => 'toutiao.php'],
    'ippzone'     => ['file' => 'pipigx.php'],
    'bilibili'    => ['file' => 'bilibili/bilibili.php'],
    'xhs_parse'   => ['file' => 'xiaohongshu/xhsjx.php'],
];

$url = $_GET['url'] ?? ($_POST['url'] ?? '');

if (empty($url)) {
    output(400, '请提供url参数');
}

$host = parse_url($url, PHP_URL_HOST);
if (!$host) {
    output(400, '无效的URL');
}

$bs = parsePlatform($url);

if ($bs === 'douyin') {
    handleDouyin($url);
}

if (!isset($platforms[$bs])) {
    output(400, '不支持的平台: ' . $bs);
}

$platform = $platforms[$bs];
$apiFile = API_DIR . $platform['file'];

if (!file_exists($apiFile)) {
    output(500, '解析模块不存在: ' . $platform['file']);
}

$_GET['url'] = $url;
$_POST['url'] = $url;

ob_start();
require $apiFile;
$response = ob_get_clean();

if (empty($response)) {
    output(500, '解析服务返回空响应');
}

$data = json_decode($response, true);
if ($data === null) {
    output(500, '解析服务返回非JSON数据: ' . substr($response, 0, 200));
}

output($data['code'] ?? 500, $data['msg'] ?? '未知错误', $data['data'] ?? []);

function handleDouyin($url)
{
    $result = null;

    if (file_exists(DOUYIN_COOKIE_FILE)) {
        $cookie = trim(file_get_contents(DOUYIN_COOKIE_FILE));
        if (!empty($cookie)) {
            $result = parseDouyinLocal($url, $cookie);
        }
    }

    if ($result === null || ($result['code'] ?? 500) !== 200) {
        $result = parseDouyinExternal($url);
    }

    output($result['code'] ?? 500, $result['msg'] ?? '解析失败', $result['data'] ?? []);
}

function parseDouyinLocal($url, $cookie)
{
    require_once API_DIR . 'douyin/DouyinParser.php';
    $parser = new DouyinParser();
    $parser->setCookie($cookie);
    $response = $parser->parse($url);
    return json_decode($response, true);
}

function parseDouyinExternal($url)
{
    $apiUrl = DOUYIN_FALLBACK_API . urlencode($url);
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        return ['code' => 500, 'msg' => '外部解析服务不可用'];
    }

    $data = json_decode($response, true);
    if ($data === null) {
        return ['code' => 500, 'msg' => '外部解析返回数据异常'];
    }

    return $data;
}

function parsePlatform($url)
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return '';

    if (strpos($url, 'chenzhongtech') !== false || strpos($url, 'kuaishouapp') !== false) {
        return 'chenzhongtech';
    }
    if (strpos($host, 'douyin.com') !== false) {
        return 'douyin';
    }
    if (strpos($host, 'kuaishou.com') !== false) {
        return 'kuaishou';
    }
    if (strpos($host, 'pipix.com') !== false) {
        return 'pipix';
    }
    if (strpos($host, 'izuiyou.com') !== false) {
        return 'izuiyou';
    }
    if (strpos($host, 'toutiaoimg.com') !== false || strpos($host, 'toutiao.com') !== false) {
        return 'toutiaoimg';
    }
    if (strpos($host, 'ippzone.com') !== false) {
        return 'ippzone';
    }
    if (strpos($host, 'bilibili.com') !== false) {
        return 'bilibili';
    }
    if (strpos($host, 'xiaohongshu.com') !== false || strpos($host, 'xhslink.com') !== false) {
        return 'xhs_parse';
    }

    $parts = explode('.', $host);
    return $parts[count($parts) - 2] ?? '';
}

function output($code, $msg, $data = [])
{
    $result = [
        'code' => $code,
        'msg' => $msg,
    ];
    if ($code == 200) {
        $isImage = ($data['type'] ?? '') === 'image';
        $images = $data['images'] ?? [];

        $videoUrl = $data['url'] ?? '';
        if ($isImage && empty($videoUrl) && !empty($images[0])) {
            $videoUrl = $images[0];
        }

        $result['data'] = [
            'title' => $data['title'] ?? '',
            'url' => $videoUrl,
            'cover' => $data['cover'] ?? '',
        ];

        if ($isImage && !empty($images)) {
            $result['data']['type'] = 'image';
            $result['data']['images'] = $images;
            $result['data']['total_images'] = count($images);
        }
    }
    echo json_encode($result, 480);
    exit;
}
