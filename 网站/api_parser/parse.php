<?php
/**
 * CLI 统一解析入口
 * 用法: php8.2 parse.php <url>
 * 输出: JSON
 */
define('API_DIR', __DIR__ . '/');
define('DOUYIN_COOKIE_FILE', __DIR__ . '/douyin_cookie.txt');
define('DOUYIN_FALLBACK_API', 'https://api.bugpk.com/api/douyin?url=');

$url = $argv[1] ?? '';
if (empty($url)) {
    output(400, '请提供url参数');
}

$bs = parsePlatform($url);

if ($bs === 'doubao') {
    handleDoubao($url);
}
if ($bs === 'jimeng') {
    handleJimeng($url);
}
if ($bs === 'douyin') {
    handleDouyin($url);
}

$platforms = [
    'kuaishou'    => ['file' => 'kuaishou/ksjx.php'],
    'pipix'       => ['file' => 'ppxia.php'],
    'izuiyou'     => ['file' => 'zuiyou.php'],
    'toutiaoimg'  => ['file' => 'toutiao.php'],
    'ippzone'     => ['file' => 'pipigx.php'],
    'bilibili'    => ['file' => 'bilibili/bilibili.php'],
    'xhs_parse'   => ['file' => 'xiaohongshu/xhsjx.php'],
    'weibo'       => ['file' => 'weibo.php'],
    'weibo_v'     => ['file' => 'weibo_v.php'],
];

$pythonParsers = [
    '163' => ['cmd' => 'python3 ' . API_DIR . 'wyy_cli.py'],
];

if (isset($pythonParsers[$bs])) {
    $cmd = $pythonParsers[$bs]['cmd'] . ' ' . escapeshellarg($url);
    $data = shell_exec($cmd);
    if ($data === null || $data === false) {
        output(500, 'Python解析服务执行失败');
    }
    $result = json_decode($data, true);
    if ($result === null) {
        output(500, 'Python解析返回非JSON数据');
    }
    output($result['code'] ?? 500, $result['msg'] ?? '解析失败', $result['data'] ?? []);
}

$externalApis = [
    'weishi'    => 'https://www.80zy.com/dspjson.php?url=',
    'huoshan'   => 'https://www.80zy.com/dspjson.php?url=',
    'ixigua'    => 'https://www.80zy.com/dspjson.php?url=',
    'meipai'    => 'https://ys.5266s.cn/chuanma/index.php?url=',
    'acfun'     => 'https://www.80zy.com/dspjson.php?url=',
    'migu'      => 'https://api.yyy001.com/api/mgmuisc?msg=',
];

if (isset($externalApis[$bs])) {
    handleExternalApi($url, $externalApis[$bs]);
}

if (!isset($platforms[$bs])) {
    output(400, '不支持的平台: ' . $bs);
}

$platform = $platforms[$bs];
$apiFile = API_DIR . $platform['file'];

if (!file_exists($apiFile)) {
    output(500, '解析模块不存在: ' . $platform['file']);
}

// 设置 $_GET/$_POST 兼容解析器代码
$_GET['url'] = $url;
$_POST['url'] = $url;
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

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

    // 优先使用本地 Cookie 解析
    if (file_exists(DOUYIN_COOKIE_FILE)) {
        $cookie = trim(file_get_contents(DOUYIN_COOKIE_FILE));
        if (!empty($cookie)) {
            $result = parseDouyinLocal($url, $cookie);
        }
    }

    // Cookie解析失败或未配置，尝试无Cookie解析
    if ($result === null || ($result['code'] ?? 500) !== 200) {
        $result = parseDouyinNoCookie($url);
    }

    // 回退到外部 API
    if ($result === null || ($result['code'] ?? 500) !== 200) {
        $result = parseDouyinExternal($url);
    }

    output($result['code'] ?? 500, $result['msg'] ?? '解析失败', $result['data'] ?? []);
}

function handleDoubao($url)
{
    require_once API_DIR . 'doubao/DoubaoParser.php';
    $parser = new DoubaoParser();
    $response = $parser->parse($url);
    $data = json_decode($response, true);
    if ($data === null) {
        output(500, '豆包解析返回非JSON数据');
    }
    output($data['code'] ?? 500, $data['msg'] ?? '解析失败', $data['data'] ?? []);
}

function handleJimeng($url)
{
    require_once API_DIR . 'jimengai/JimengParser.php';
    $itemId = JimengParser::extractItemIdFromUrl($url);
    if ($itemId === null || $itemId === '') {
        output(400, '无法从链接中提取即梦ID');
    }
    $parser = new JimengParser();
    $response = $parser->parseByItemId($itemId);
    $data = json_decode($response, true);
    if ($data === null) {
        output(500, '即梦解析返回非JSON数据');
    }
    output($data['code'] ?? 500, $data['msg'] ?? '解析失败', $data['data'] ?? []);
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

function parseDouyinNoCookie($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_exec($ch);
    $realUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    preg_match('/[0-9]+/', $realUrl, $idMatches);
    $id = $idMatches[0] ?? null;
    if (empty($id)) {
        return ['code' => 400, 'msg' => '无法提取视频ID'];
    }

    $apiUrl = 'https://www.iesdouyin.com/share/video/' . $id;
    $ch2 = curl_init($apiUrl);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
    ]);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch2);
    curl_close($ch2);

    if ($response === false) {
        return ['code' => 500, 'msg' => '请求抖音分享页失败'];
    }

    preg_match('/window\._ROUTER_DATA\s*=\s*(.*?)<\/script>/s', $response, $matches);
    if (empty($matches[1])) {
        return ['code' => 201, 'msg' => '解析数据失败'];
    }

    $videoInfo = json_decode(trim($matches[1]), true);
    $loaderData = $videoInfo['loaderData'] ?? [];
    if (empty($loaderData)) {
        return ['code' => 201, 'msg' => '数据查找失败'];
    }

    $item = null;
    foreach ($loaderData as $key => $value) {
        if (strpos($key, 'video_') === 0 && isset($value['videoInfoRes']['item_list'][0])) {
            $item = $value['videoInfoRes']['item_list'][0];
            break;
        }
    }
    if ($item === null) {
        return ['code' => 201, 'msg' => '未找到视频数据'];
    }

    $videoUrl = str_replace('playwm', 'play', $item['video']['play_addr']['url_list'][0]);

    $images = [];
    if (isset($item['images']) && is_array($item['images'])) {
        foreach ($item['images'] as $img) {
            if (isset($img['url_list'][0])) {
                $images[] = $img['url_list'][0];
            }
        }
    }

    $hasImages = count($images) > 0;
    $coverUrl = $item['video']['cover']['url_list'][0] ?? ($item['video']['origin_cover']['url_list'][0] ?? '');

    return [
        'code' => 200,
        'msg' => '解析成功',
        'data' => [
            'title' => $item['desc'] ?? '',
            'cover' => $coverUrl,
            'url' => $hasImages ? ('图文解析，共' . count($images) . '张图片') : $videoUrl,
            'type' => $hasImages ? 'image' : 'video',
            'images' => $images,
            'duration' => $item['video']['duration'] ?? null,
        ]
    ];
}

function parsePlatform($url)
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return '';

    // ThinkPHP Api.php 使用的平台标识符映射
    if (strpos($url, 'chenzhongtech') !== false || strpos($url, 'kuaishouapp') !== false) {
        return 'kuaishou';
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
    if (strpos($host, 'doubao.com') !== false) {
        return 'doubao';
    }
    if (strpos($host, 'jimeng.jianying.com') !== false || strpos($host, 'v.jimeng.aiseet.atry.com') !== false) {
        return 'jimeng';
    }
    if (strpos($host, 'weishi.qq.com') !== false) {
        return 'weishi';
    }
    if (strpos($host, 'huoshan.com') !== false || strpos($host, 'huoshancdn.com') !== false) {
        return 'huoshan';
    }
    if (strpos($host, 'music.163.com') !== false || strpos($host, '163.com') !== false) {
        return '163';
    }
    if (strpos($host, 'ixigua.com') !== false) {
        return 'ixigua';
    }
    if (strpos($host, 'meipai.com') !== false) {
        return 'meipai';
    }
    if (strpos($host, 'acfun.cn') !== false || strpos($host, 'acfun.com') !== false) {
        return 'acfun';
    }
    if (strpos($host, 'migu.cn') !== false || strpos($host, 'migu.com') !== false) {
        return 'migu';
    }
    if (strpos($host, 'weibo.com') !== false || strpos($host, 'weibocdn.com') !== false) {
        return 'weibo';
    }
    if (strpos($host, 'video.weibo.com') !== false) {
        return 'weibo_v';
    }

    $parts = explode('.', $host);
    return $parts[count($parts) - 2] ?? '';
}

function output($code, $msg, $data = [])
{
    $result = [
        'code' => $code,
        'msg' => $msg,
        'data' => [],
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

function handleExternalApi($url, $apiBase)
{
    $apiUrl = $apiBase . urlencode($url);
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || empty($response)) {
        output(500, '外部解析服务不可用');
    }

    $data = json_decode($response, true);
    if ($data === null) {
        output(500, '外部解析返回数据异常: ' . substr($response, 0, 200));
    }

    output($data['code'] ?? 500, $data['msg'] ?? '解析失败', $data['data'] ?? []);
}
