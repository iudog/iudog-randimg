<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'peizhi.php';
$lianjie_biaoshi = isset($_GET['id']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['id']) : '';
if (empty($lianjie_biaoshi) && !empty($peizhi_shuju['lianjie_peizhi'])) {
    $lianjie_biaoshi = $peizhi_shuju['lianjie_peizhi'][0]['biaoshi'];
}
$guanlian_wenjianjia = huode_wenjianjia_by_lianjie($peizhi_shuju, $lianjie_biaoshi);
if (empty($guanlian_wenjianjia)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo '未找到指定的链接配置或标识符为空';
    exit;
}
$suoyou_tupian = array();
foreach ($guanlian_wenjianjia as $wenjianjia) {
    $anquan_biaoshi = preg_replace('/[^a-zA-Z0-9_]/', '', $wenjianjia['biaoshi']);
    $wenjianjia_lujing = 'tupian/' . $anquan_biaoshi;
    if (is_dir($wenjianjia_lujing)) {
        $dangqian_tupian = glob($wenjianjia_lujing . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        if ($dangqian_tupian) {
            $suoyou_tupian = array_merge($suoyou_tupian, $dangqian_tupian);
        }
    }
}
if (empty($suoyou_tupian)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo '关联文件夹中暂无图片，请先上传图片';
    exit;
}
$suiji_suoyin = array_rand($suoyou_tupian);
$suiji_tupian = $suoyou_tupian[$suiji_suoyin];
if (file_exists($suiji_tupian)) {
    if (function_exists('mime_content_type')) {
        $mime_leixing = mime_content_type($suiji_tupian);
    } else {
        $kuozhanming = strtolower(pathinfo($suiji_tupian, PATHINFO_EXTENSION));
        $mime_map = array(
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp'
        );
        $mime_leixing = isset($mime_map[$kuozhanming]) ? $mime_map[$kuozhanming] : 'image/jpeg';
    }
    if (ob_get_length()) ob_clean();
    header('Content-Type: ' . $mime_leixing);
    header('Cache-Control: no-cache, must-revalidate');
    readfile($suiji_tupian);
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo '图片文件已丢失';
}
exit;
?>