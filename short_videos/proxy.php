<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('API_DIR', __DIR__ . '/short_videos-main/short_videos-main/api/');

$platforms = [
    'douyin'      => ['file' => 'douyin/douyin.php',      'parser' => 'DouyinParser', 'path' => 'douyin/DouyinParser.php'],
    'kuaishou'    => ['file' => 'kuaishou/ksjx.php',       'parser' => 'KuaishouSpider', 'path' => 'kuaishou/KuaishouSpider.php'],
    'pipix'       => ['file' => 'ppxia.php',                'parser' => null,            'path' => null],
    'izuiyou'     => ['file' => 'zuiyou.php',               'parser' => null,            'path' => null],
    'toutiaoimg'  => ['file' => 'toutiao.php',              'parser' => null,            'path' => null],
    'ippzone'     => ['file' => 'pipigx.php',               'parser' => null,            'path' => null],
    'bilibili'    => ['file' => 'bilibili/bilibili.php',    'parser' => null,            'path' => null],
    'xhs_parse'   => ['file' => 'xiaohongshu/xhsjx.php',    'parser' => null,            'path' => null],
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

function parsePlatform($url)
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return '';

    if (strpos($url, 'chenzhongtech') !== false || strpos($url, 'kuaishouapp') !== false) {
        return 'chenzhongtech';
    }
    if (strpos($host, 'douyin.com') !== false || strpos($host, 'douyin.com') !== false) {
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
        'data' => [
            'title' => $data['title'] ?? '',
            'url' => $data['url'] ?? '',
            'cover' => $data['cover'] ?? '',
        ]
    ];
    echo json_encode($result, 480);
    exit;
}
