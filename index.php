<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once 'peizhi.php';
yanzheng_denglu();
$dangqian_wenjianjia_biaoshi = isset($_GET['wenjianjia']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['wenjianjia']) : '';
$youxiao_wenjianjia = false;
if (!empty($dangqian_wenjianjia_biaoshi)) {
    foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wenjianjia) {
        if ($wenjianjia['biaoshi'] === $dangqian_wenjianjia_biaoshi) {
            $youxiao_wenjianjia = true;
            break;
        }
    }
}
$tupian_mulu_lujing = 'tupian/' . $dangqian_wenjianjia_biaoshi . '/';
$wenjian_liebiao = array();
if ($youxiao_wenjianjia && is_dir($tupian_mulu_lujing)) {
    $wenjian_liebiao = glob($tupian_mulu_lujing . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
}
$shangchuan_jieguo = isset($_SESSION['shangchuan_jieguo']) ? $_SESSION['shangchuan_jieguo'] : null;
unset($_SESSION['shangchuan_jieguo']);
$suoyou_lianjie_liebiao = huode_suoyou_lianjie($peizhi_shuju);
$xiangguan_lianjie_liebiao = array();
foreach ($suoyou_lianjie_liebiao as $lianjie) {
    if (in_array($dangqian_wenjianjia_biaoshi, $lianjie['wenjianjia_biaoshi'])) {
        $xiangguan_lianjie_liebiao[] = $lianjie;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>随机图床</title>
    <link rel="stylesheet" href="style.css">
</head>
<body><!-- 是的你没看错，所有变量都是拼音命名 -->
    <div class="rongqi"><!-- 我英语不好，我恨透了英语 -->
        <?php if ($shangchuan_jieguo): ?>
            <div class="tishi <?php echo $shangchuan_jieguo['chenggong'] ? 'chenggong' : 'shibai'; ?>">
                <?php echo htmlspecialchars($shangchuan_jieguo['xinxi']); ?>
            </div>
        <?php endif; ?>
        <div class="zhuyao-neirong">
            <aside class="zuoce-bianlan">
                <div class="wenjianjia-xuanze-quyu">
                    <h2 class="quyu-biaoti">选择文件夹</h2>
                    <?php if (empty($peizhi_shuju['wenjianjia_liebiao'])): ?>
                        <div class="tishi xinxi" style="margin-top: 15px;">
                            💡 请先前往<a href="guanli.php" style="color: #3498db; text-decoration: underline;">管理后台</a>创建文件夹
                        </div>
                    <?php else: ?>
                        <div class="wenjianjia-anniu-zu">
                            <?php foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wenjianjia): ?>
                                <a href="?wenjianjia=<?php echo urlencode($wenjianjia['biaoshi']); ?>" 
                                   class="wenjianjia-anniu <?php echo $dangqian_wenjianjia_biaoshi === $wenjianjia['biaoshi'] ? 'dangqian' : ''; ?>">
                                    <?php echo htmlspecialchars($wenjianjia['mingcheng']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="shangchuan-quyu">
                    <h2 class="quyu-biaoti">上传图片</h2>
                    <?php if (empty($peizhi_shuju['wenjianjia_liebiao'])): ?>
                        <div class="tishi xinxi" style="margin-top: 15px;">
                            ⚠️ 请先创建文件夹后再上传图片
                        </div>
                    <?php elseif (empty($dangqian_wenjianjia_biaoshi)): ?>
                        <div class="tishi xinxi" style="margin-top: 15px;">
                            💡 请先在上方选择一个文件夹后再上传图片
                        </div>
                    <?php else: ?>
                        <form action="shangchuan.php" method="POST" enctype="multipart/form-data" class="shangchuan-biaodan">
                            <input type="hidden" name="wenjianjia_biaoshi" value="<?php echo htmlspecialchars($dangqian_wenjianjia_biaoshi); ?>">
                            <input type="file" name="tupian_wenjian[]" id="id-tupian-wenjian" accept="image/*" multiple required class="wenjian-shuru">
                            <button type="submit" class="tijiao-anniu">上传图片</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="lianjie-quyu">
                    <h2 class="quyu-biaoti">随机图片链接</h2>
                    <?php if (!empty($xiangguan_lianjie_liebiao)): ?>
                        <?php foreach ($xiangguan_lianjie_liebiao as $lianjie): ?>
                            <?php 
                            $dangqian_lianjie = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/') . '/suiji.php?id=' . urlencode($lianjie['biaoshi']); 
                            ?>
                            <div class="lianjie-xiang" data-lianjie-url="<?php echo htmlspecialchars($dangqian_lianjie, ENT_QUOTES, 'UTF-8'); ?>">
                                <h3 class="lianjie-mingcheng"><?php echo htmlspecialchars($lianjie['mingcheng']); ?></h3>
                                <div class="lianjie-neirong">
                                    <p class="lianjie-tishi">链接地址：</p>
                                    <code class="lianjie-daima"><?php echo htmlspecialchars($dangqian_lianjie, ENT_QUOTES, 'UTF-8'); ?></code>
                                    <button type="button" class="fuzhi-anniu">复制链接</button>
                                </div>
                                <p class="lianjie-miaoshu">
                                    关联文件夹：
                                    <?php 
                                    $guanlian_wenjianjia = huode_wenjianjia_by_lianjie($peizhi_shuju, $lianjie['biaoshi']);
                                    $wenjianjia_mingcheng_liebiao = array();
                                    foreach ($guanlian_wenjianjia as $wj) {
                                        $wenjianjia_mingcheng_liebiao[] = $wj['mingcheng'];
                                    }
                                    echo implode(' + ', $wenjianjia_mingcheng_liebiao);
                                    ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="wutupian-tishi">暂无可用的链接，请在管理后台添加</p>
                    <?php endif; ?>
                </div>
            </aside>
            <main class="youce-yulan">
                <h2 class="quyu-biaoti">图片预览 <span style="font-size: 0.8em; color: #666; font-weight: normal;">（右键图片可复制单个该图片链接）</span></h2>
                <div class="tupian-liebiao">
                    <?php if (!empty($wenjian_liebiao)): ?>
                        <?php foreach ($wenjian_liebiao as $tupian_lujing): ?>
                            <div class="tupian-xiang">
                                <img src="<?php echo htmlspecialchars($tupian_lujing); ?>" alt="上传的图片" class="yulan-tupian">
                                <p class="tupian-mingcheng"><?php echo htmlspecialchars(basename($tupian_lujing)); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="wutupian-tishi">该文件夹下暂无图片</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <div class="daohang-anniu-zu">
        <a href="index.php" class="daohang-anniu dangqian">🖼️ 图片管理</a>
        <a href="guanli.php" class="daohang-anniu">📁 文件夹管理</a>
        <a href="tuichu.php" class="daohang-anniu" style="background-color: #95a5a6; box-shadow: 0 4px 8px rgba(149, 165, 166, 0.4);">🚪 退出登录</a>
    </div>

    <footer class="yejiao" id="id-yejiao">
        <p>© <a href="https://iudog.com" target="_blank">iudog</a> 版权所有，<a href="https://github.com/iudog/iudog-randimg" target="_blank">本项目github仓库</a></p>
    </footer>
    
    <script src="JavaScript/index.js"></script>
</body>
</html>