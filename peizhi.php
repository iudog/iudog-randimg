<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/html; charset=utf-8');
$peizhi_wenjian = 'peizhi.php_data.php';
$anquan_tou = "<?php die(); ?>\n";
$tupian_gen_mulu = 'tupian';
if (!is_dir($tupian_gen_mulu)) {
    mkdir($tupian_gen_mulu, 0755, true);
}
if (!file_exists($peizhi_wenjian)) {
    $moren_peizhi = array(
        'guanliyuan' => array(
            'zhanghao' => 'admin',
            'mima' => '123456'
        ),
        'wenjianjia_liebiao' => array(),
        'lianjie_peizhi' => array()
    );
    $json_shuju = json_encode($moren_peizhi, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($peizhi_wenjian, $anquan_tou . $json_shuju);
}
$peizhi_yuan_neirong = file_get_contents($peizhi_wenjian);
$peizhi_neirong = str_replace($anquan_tou, '', $peizhi_yuan_neirong);
$peizhi_shuju = json_decode($peizhi_neirong, true);
if (!$peizhi_shuju) {
    echo '配置文件损坏，请删除 peizhi.php_data.php 后重新访问';
    exit;
}
function baocun_peizhi($peizhi_shuju) {
    $peizhi_wenjian = 'peizhi.php_data.php';
    $anquan_tou = "<?php die(); ?>\n";
    $json_shuju = json_encode($peizhi_shuju, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($peizhi_wenjian, $anquan_tou . $json_shuju);
}
function yanzheng_denglu() {
    if (!isset($_SESSION['shifou_denglu']) || $_SESSION['shifou_denglu'] !== true) {
        header('Location: denglu.php');
        exit;
    }
}
function huode_suoyou_lianjie($peizhi_shuju) {
    return $peizhi_shuju['lianjie_peizhi'];
}
function huode_wenjianjia_by_lianjie($peizhi_shuju, $lianjie_biaoshi) {
    foreach ($peizhi_shuju['lianjie_peizhi'] as $lianjie) {
        if ($lianjie['biaoshi'] === $lianjie_biaoshi) {
            $wenjianjia_liebiao = array();
            foreach ($lianjie['wenjianjia_biaoshi'] as $wj_biaoshi) {
                foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wenjianjia) {
                    if ($wenjianjia['biaoshi'] === $wj_biaoshi) {
                        $wenjianjia_liebiao[] = $wenjianjia;
                        break;
                    }
                }
            }
            return $wenjianjia_liebiao;
        }
    }
    return array();
}
?>