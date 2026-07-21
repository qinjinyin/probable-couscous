<?php
$url = $_POST['url'] ?? ($_GET['url'] ?? '');
if (empty($url)) die('no url');
$cmd = 'php8.2 /workspace/website/网站/api_parser/parse.php ' . escapeshellarg($url) . ' 2>/dev/null';
$data = shell_exec($cmd);
header('Content-Type: application/json');
echo $data;
