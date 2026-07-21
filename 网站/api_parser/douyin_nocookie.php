<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

$url = $_GET['url'] ?? ($argv[2] ?? '');

if (empty($url)) {
    echo json_encode(['code' => 201, 'msg' => 'url为空'], 480);
    exit;
}

function douyinNocookie($url)
{
    $header = array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1');

    $id = extractIdNc($url);
    if (empty($id)) {
        return array('code' => 400, 'msg' => '无法解析视频ID');
    }

    $response = curlNc('https://www.iesdouyin.com/share/video/' . $id, $header);
    preg_match('/window\._ROUTER_DATA\s*=\s*(.*?)<\/script>/s', $response, $matches);

    if (empty($matches[1])) {
        return array('code' => 201, 'msg' => '解析数据失败');
    }

    $videoInfo = json_decode(trim($matches[1]), true);
    if (!isset($videoInfo['loaderData']['video_(id)/page']['videoInfoRes']['item_list'][0])) {
        return array('code' => 201, 'msg' => '数据查找失败');
    }

    $item = $videoInfo['loaderData']['video_(id)/page']['videoInfoRes']['item_list'][0];

    $videoUrl = str_replace('playwm', 'play', $item['video']['play_addr']['url_list'][0]);

    $images = [];
    if (isset($item['images']) && is_array($item['images'])) {
        foreach ($item['images'] as $img) {
            if (isset($img['url_list'][0])) {
                $images[] = $img['url_list'][0];
            }
        }
    }

    $music = null;
    if (!empty($item['music'])) {
        $music = array(
            'title' => $item['music']['title'] ?? '',
            'author' => $item['music']['author'] ?? '',
            'avatar' => $item['music']['cover_large']['url_list'][0] ?? '',
            'url' => $item['video']['play_addr']['uri'] ?? '',
        );
    }

    $hasImages = count($images) > 0;

    return array(
        'code' => 200,
        'msg' => '解析成功',
        'data' => array(
            'title' => $item['desc'] ?? '',
            'cover' => $item['video']['cover']['url_list'][0] ?? '',
            'url' => $hasImages ? '图文解析，共' . count($images) . '张图片' : $videoUrl,
            'type' => $hasImages ? 'image' : 'video',
            'images' => $images,
            'duration' => $item['video']['duration'] ?? null,
            'music' => $music,
        )
    );
}

function extractIdNc($url)
{
    $headers = @get_headers($url, true);
    if ($headers === false) {
        $loc = $url;
    } else {
        $loc = isset($headers['Location']) ? (is_array($headers['Location']) ? end($headers['Location']) : $headers['Location']) : $url;
    }
    if (!is_string($loc)) {
        $loc = strval($loc);
    }
    preg_match('/[0-9]+|(?<=video\/)[0-9]+/', $loc, $id);
    return !empty($id) ? $id[0] : null;
}

function curlNc($url, $header = null)
{
    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($con, CURLOPT_AUTOREFERER, 1);
    curl_setopt($con, CURLOPT_TIMEOUT, 10);
    if (isset($header)) {
        curl_setopt($con, CURLOPT_HTTPHEADER, $header);
    }
    $result = curl_exec($con);
    curl_close($con);
    return $result;
}

$response = douyinNocookie($url);
echo json_encode($response, 480);
