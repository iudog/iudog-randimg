<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'peizhi.php';
$wenjian_shuzu = $_FILES['tupian_wenjian'];
$chenggong_shuliang = 0;
$shibai_shuliang = 0;
$cuowu_xinxi_liebiao = array();
$wenjian_shuliang = count($wenjian_shuzu['name']);
$wenjianjia_biaoshi = isset($_POST['wenjianjia_biaoshi']) ? $_POST['wenjianjia_biaoshi'] : '';
if (!empty($wenjianjia_biaoshi)) {
    $shangchuan_mulu = 'tupian/' . $wenjianjia_biaoshi . '/';
    if (!is_dir($shangchuan_mulu)) {
        mkdir($shangchuan_mulu, 0755, true);
    }
    for ($i = 0; $i < $wenjian_shuliang; $i++) {
        $cuowu_daima = $wenjian_shuzu['error'][$i];
        if ($cuowu_daima !== UPLOAD_ERR_OK) {
            switch ($cuowu_daima) {
                case UPLOAD_ERR_INI_SIZE:
                    $cuowu_xinxi = '文件"' . $wenjian_shuzu['name'][$i] . '"大小超过服务器限制';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $cuowu_xinxi = '文件"' . $wenjian_shuzu['name'][$i] . '"大小超过表单限制';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $cuowu_xinxi = '文件"' . $wenjian_shuzu['name'][$i] . '"只有部分被上传';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $cuowu_xinxi = '文件"' . $wenjian_shuzu['name'][$i] . '"没有被上传';
                    break;
                default:
                    $cuowu_xinxi = '文件"' . $wenjian_shuzu['name'][$i] . '"上传失败，错误代码：' . $cuowu_daima;
            }
            $cuowu_xinxi_liebiao[] = $cuowu_xinxi;
            $shibai_shuliang++;
            continue;
        }
        $yuanmingcheng = $wenjian_shuzu['name'][$i];
        $linshi_lujing = $wenjian_shuzu['tmp_name'][$i];
        $kuozhanming = strtolower(pathinfo($yuanmingcheng, PATHINFO_EXTENSION));
        $yunxu_kuozhanming = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (!in_array($kuozhanming, $yunxu_kuozhanming)) {
            $cuowu_xinxi = '文件"' . $yuanmingcheng . '"格式不支持，只允许 JPG、JPEG、PNG、GIF、WEBP 格式';
            $cuowu_xinxi_liebiao[] = $cuowu_xinxi;
            $shibai_shuliang++;
            continue;
        }
        $zuida_daxiao = 10 * 1024 * 1024;
        if ($wenjian_shuzu['size'][$i] > $zuida_daxiao) {
            $cuowu_xinxi = '文件"' . $yuanmingcheng . '"大小超过 10MB 限制';
            $cuowu_xinxi_liebiao[] = $cuowu_xinxi;
            $shibai_shuliang++;
            continue;
        }
        $xin_mingcheng = time() . '_' . rand(1000, 9999) . '.' . $kuozhanming;
        $baocun_lujing = $shangchuan_mulu . $xin_mingcheng;
        if (move_uploaded_file($linshi_lujing, $baocun_lujing)) {
            $chenggong_shuliang++;
        } else {
            $cuowu_xinxi = '文件"' . $yuanmingcheng . '"移动失败，请检查目录权限';
            $cuowu_xinxi_liebiao[] = $cuowu_xinxi;
            $shibai_shuliang++;
        }
    }
} else {
    $shibai_shuliang = $wenjian_shuliang;
    $cuowu_xinxi_liebiao[] = '未指定有效的文件夹标识符';
}
$fanhui_xinxi = '';
if ($chenggong_shuliang > 0) {
    $fanhui_xinxi .= '成功上传 ' . $chenggong_shuliang . ' 张图片！';
}
if ($shibai_shuliang > 0) {
    if ($chenggong_shuliang > 0) {
        $fanhui_xinxi .= '<br>';
    }
    $fanhui_xinxi .= $shibai_shuliang . ' 张图片上传失败';
    if (!empty($cuowu_xinxi_liebiao)) {
        $fanhui_xinxi .= '<br><small>' . implode('<br>', $cuowu_xinxi_liebiao) . '</small>';
    }
}
$zhengti_chenggong = ($chenggong_shuliang > 0) ? true : false;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['shangchuan_jieguo'] = array(
    'chenggong' => $zhengti_chenggong,
    'xinxi' => $fanhui_xinxi
);
session_write_close();
header('Location: index.php');
exit;
?>