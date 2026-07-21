<?php
namespace app\index\controller;
use think\Controller;
class Test extends Controller
{
    public function shell()
    {
        $url = 'https://www.doubao.com/test';
        $data = shell_exec('php8.2 /workspace/website/网站/api_parser/parse.php ' . escapeshellarg($url) . ' 2>/dev/null');
        echo 'LEN:' . strlen($data) . ' JSON:' . $data;
    }
}
