<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'peizhi.php';
yanzheng_denglu();
function shanchu_wenjianjia_digui($lujing) {
    if (is_dir($lujing)) {
        $wenjian_liebiao = scandir($lujing);
        foreach ($wenjian_liebiao as $wenjian) {
            if ($wenjian === '.' || $wenjian === '..') {
                continue;
            }
            $quan_lujing = $lujing . '/' . $wenjian;
            if (is_dir($quan_lujing)) {
                shanchu_wenjianjia_digui($quan_lujing);
            } else {
                unlink($quan_lujing);
            }
        }
        rmdir($lujing);
    }
}
$tishi_xinxi = null;
$tishi_leixing = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caozuo_leixing = isset($_POST['caozuo']) ? $_POST['caozuo'] : '';
    if ($caozuo_leixing === 'tianjia_wenjianjia') {
        $wenjianjia_mingcheng = isset($_POST['wenjianjia_mingcheng']) ? trim($_POST['wenjianjia_mingcheng']) : '';
        $wenjianjia_miaoshu = isset($_POST['wenjianjia_miaoshu']) ? trim($_POST['wenjianjia_miaoshu']) : '';
        $wenjianjia_biaoshi = isset($_POST['wenjianjia_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['wenjianjia_biaoshi']) : '';
        if (!empty($wenjianjia_mingcheng) && !empty($wenjianjia_biaoshi)) {
            $yicunzai = false;
            foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj) {
                if ($wj['biaoshi'] === $wenjianjia_biaoshi) {
                    $yicunzai = true;
                    break;
                }
            }
            if (!$yicunzai) {
                $peizhi_shuju['wenjianjia_liebiao'][] = array(
                    'mingcheng' => $wenjianjia_mingcheng,
                    'biaoshi' => $wenjianjia_biaoshi,
                    'miaoshu' => $wenjianjia_miaoshu
                );
                $xin_lianjie_biaoshi = $wenjianjia_biaoshi;
                $xu_hao = 1;
                while (true) {
                    $yicunzai_biaoshi = false;
                    foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
                        if ($lj['biaoshi'] === $xin_lianjie_biaoshi) {
                            $yicunzai_biaoshi = true;
                            break;
                        }
                    }
                    if (!$yicunzai_biaoshi) break;
                    $xin_lianjie_biaoshi = $wenjianjia_biaoshi . '_' . $xu_hao;
                    $xu_hao++;
                }
                $peizhi_shuju['lianjie_peizhi'][] = array(
                    'mingcheng' => $wenjianjia_mingcheng . '随机链接',
                    'biaoshi' => $xin_lianjie_biaoshi,
                    'wenjianjia_biaoshi' => array($wenjianjia_biaoshi),
                    'zidong_chuangjian' => true
                );
                $wenjianjia_lujing = 'tupian/' . $wenjianjia_biaoshi;
                if (!is_dir($wenjianjia_lujing)) {
                    mkdir($wenjianjia_lujing, 0755, true);
                }
                baocun_peizhi($peizhi_shuju);
                $tishi_xinxi = '文件夹添加成功！已自动创建对应链接。';
                $tishi_leixing = 'chenggong';
            } else {
                $tishi_xinxi = '文件夹标识符已存在，请更换！';
                $tishi_leixing = 'shibai';
            }
        } else {
            $tishi_xinxi = '请填写完整的文件夹信息！';
            $tishi_leixing = 'shibai';
        }
    } elseif ($caozuo_leixing === 'xiugai_wenjianjia') {
        $jiu_biaoshi = isset($_POST['jiu_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['jiu_biaoshi']) : '';
        $xin_mingcheng = isset($_POST['xin_mingcheng']) ? trim($_POST['xin_mingcheng']) : '';
        $xin_biaoshi = isset($_POST['xin_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['xin_biaoshi']) : '';
        $xin_miaoshu = isset($_POST['xin_miaoshu']) ? trim($_POST['xin_miaoshu']) : '';
        if (!empty($jiu_biaoshi) && !empty($xin_mingcheng) && !empty($xin_biaoshi)) {
            if ($jiu_biaoshi !== $xin_biaoshi) {
                foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj) {
                    if ($wj['biaoshi'] === $xin_biaoshi) {
                        $tishi_xinxi = '新标识符已存在！';
                        $tishi_leixing = 'shibai';
                        goto jieshu_xiugai;
                    }
                }
            }
            $zhao_dao = false;
            foreach ($peizhi_shuju['wenjianjia_liebiao'] as &$wenjianjia) {
                if ($wenjianjia['biaoshi'] === $jiu_biaoshi) {
                    if ($jiu_biaoshi !== $xin_biaoshi) {
                        $jiu_lujing = 'tupian/' . $jiu_biaoshi;
                        $xin_lujing = 'tupian/' . $xin_biaoshi;
                        if (is_dir($jiu_lujing)) {
                            rename($jiu_lujing, $xin_lujing);
                        }
                        foreach ($peizhi_shuju['lianjie_peizhi'] as &$lianjie) {
                            foreach ($lianjie['wenjianjia_biaoshi'] as $jian => $wj_bs) {
                                if ($wj_bs === $jiu_biaoshi) {
                                    $lianjie['wenjianjia_biaoshi'][$jian] = $xin_biaoshi;
                                }
                            }
                        }
                        unset($lianjie);
                    }
                    $wenjianjia['mingcheng'] = $xin_mingcheng;
                    $wenjianjia['biaoshi'] = $xin_biaoshi;
                    $wenjianjia['miaoshu'] = $xin_miaoshu;
                    $zhao_dao = true;
                    break;
                }
            }
            unset($wenjianjia);
            if ($zhao_dao) {
                foreach ($peizhi_shuju['lianjie_peizhi'] as &$lianjie) {
                    if (isset($lianjie['zidong_chuangjian']) && $lianjie['zidong_chuangjian'] && 
                        count($lianjie['wenjianjia_biaoshi']) === 1 && 
                        $lianjie['wenjianjia_biaoshi'][0] === $xin_biaoshi) {
                        $lianjie['mingcheng'] = $xin_mingcheng . '随机链接';
                    }
                }
                unset($lianjie);
            }
            baocun_peizhi($peizhi_shuju);
            $tishi_xinxi = '文件夹信息修改成功！';
            $tishi_leixing = 'chenggong';
            jieshu_xiugai:
        } else {
            $tishi_xinxi = '请填写完整的修改信息！';
            $tishi_leixing = 'shibai';
        }
    } elseif ($caozuo_leixing === 'shanchu_wenjianjia') {
        $wenjianjia_biaoshi = isset($_POST['wenjianjia_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['wenjianjia_biaoshi']) : '';
        if (!empty($wenjianjia_biaoshi)) {
            $shanchu_lianjie_ming = array();
            foreach ($peizhi_shuju['lianjie_peizhi'] as $lianjie) {
                if (in_array($wenjianjia_biaoshi, $lianjie['wenjianjia_biaoshi'])) {
                    $shanchu_lianjie_ming[] = $lianjie['mingcheng'];
                }
            }
            $xin_wj_liebiao = array();
            foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj) {
                if ($wj['biaoshi'] !== $wenjianjia_biaoshi) {
                    $xin_wj_liebiao[] = $wj;
                }
            }
            $peizhi_shuju['wenjianjia_liebiao'] = $xin_wj_liebiao;
            $xin_lj_liebiao = array();
            foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
                if (!in_array($wenjianjia_biaoshi, $lj['wenjianjia_biaoshi'])) {
                    $xin_lj_liebiao[] = $lj;
                }
            }
            $peizhi_shuju['lianjie_peizhi'] = $xin_lj_liebiao;
            baocun_peizhi($peizhi_shuju);
            $wenjianjia_lujing = 'tupian/' . $wenjianjia_biaoshi;
            shanchu_wenjianjia_digui($wenjianjia_lujing);
            if (!empty($shanchu_lianjie_ming)) {
                $tishi_xinxi = '文件夹已物理删除！同时清理了相关链接：' . implode('、', $shanchu_lianjie_ming);
            } else {
                $tishi_xinxi = '文件夹及其内容已完全清除！';
            }
            $tishi_leixing = 'chenggong';
        }
    } elseif ($caozuo_leixing === 'tianjia_lianjie') {
        $lianjie_mingcheng = isset($_POST['lianjie_mingcheng']) ? trim($_POST['lianjie_mingcheng']) : '';
        $lianjie_biaoshi = isset($_POST['lianjie_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['lianjie_biaoshi']) : '';
        $wj_bs_shuzu = isset($_POST['wenjianjia_biaoshi_shuzu']) ? $_POST['wenjianjia_biaoshi_shuzu'] : array();
        if (!empty($lianjie_mingcheng) && !empty($lianjie_biaoshi) && !empty($wj_bs_shuzu)) {
            $yicunzai = false;
            foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
                if ($lj['biaoshi'] === $lianjie_biaoshi) {
                    $yicunzai = true;
                    break;
                }
            }
            if (!$yicunzai) {
                $peizhi_shuju['lianjie_peizhi'][] = array(
                    'mingcheng' => $lianjie_mingcheng,
                    'biaoshi' => $lianjie_biaoshi,
                    'wenjianjia_biaoshi' => $wj_bs_shuzu
                );
                baocun_peizhi($peizhi_shuju);
                $tishi_xinxi = '混合链接添加成功！';
                $tishi_leixing = 'chenggong';
            } else {
                $tishi_xinxi = '该链接标识符已被占用！';
                $tishi_leixing = 'shibai';
            }
        } else {
            $tishi_xinxi = '请完善链接信息！';
            $tishi_leixing = 'shibai';
        }
    } elseif ($caozuo_leixing === 'shanchu_lianjie') {
        $lianjie_biaoshi = isset($_POST['lianjie_biaoshi']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['lianjie_biaoshi']) : '';
        $mu_biao_lianjie = null;
        foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
            if ($lj['biaoshi'] === $lianjie_biaoshi) {
                $mu_biao_lianjie = $lj;
                break;
            }
        }
        if ($mu_biao_lianjie && isset($mu_biao_lianjie['zidong_chuangjian']) && $mu_biao_lianjie['zidong_chuangjian']) {
            $tishi_xinxi = '默认生成的链接不可单独删除，请直接删除对应文件夹。';
            $tishi_leixing = 'shibai';
        } else {
            $xin_lj_liebiao = array();
            foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
                if ($lj['biaoshi'] !== $lianjie_biaoshi) {
                    $xin_lj_liebiao[] = $lj;
                }
            }
            $peizhi_shuju['lianjie_peizhi'] = $xin_lj_liebiao;
            baocun_peizhi($peizhi_shuju);
            $tishi_xinxi = '链接已成功移除！';
            $tishi_leixing = 'chenggong';
        }
    }
}
$xieyi = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$zhuji = $_SERVER['HTTP_HOST'];
$dangqian_mulu = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/');
$jichu_url = $xieyi . '://' . $zhuji . $dangqian_mulu;
$shanchu_yuce_shuju = array();
foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj) {
    $yingxiang_lianjie = array();
    foreach ($peizhi_shuju['lianjie_peizhi'] as $lj) {
        if (in_array($wj['biaoshi'], $lj['wenjianjia_biaoshi'])) {
            $yingxiang_lianjie[] = $lj['mingcheng'];
        }
    }
    $shanchu_yuce_shuju[$wj['biaoshi']] = $yingxiang_lianjie;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="rongqi" id="id-rongqi" data-wenjianjia-shanchu-yuce='<?php echo htmlspecialchars(json_encode($shanchu_yuce_shuju, JSON_UNESCAPED_UNICODE)); ?>'>
        <?php if ($tishi_xinxi): ?>
            <div class="tishi <?php echo $tishi_leixing; ?>">
                <?php echo htmlspecialchars($tishi_xinxi); ?>
            </div>
        <?php endif; ?>
        <div class="guanli-quyu">
            <h2 class="quyu-biaoti">文件夹管理</h2>
            <form method="POST" class="tianjia-biaodan">
                <input type="hidden" name="caozuo" value="tianjia_wenjianjia">
                <div class="biaodan-xiang">
                    <label for="id-wenjianjia-mingcheng">文件夹名称：</label>
                    <input type="text" id="id-wenjianjia-mingcheng" name="wenjianjia_mingcheng" required class="wenben-shuru">
                </div>
                <div class="biaodan-xiang">
                    <label for="id-wenjianjia-biaoshi">文件夹标识符：</label>
                    <input type="text" id="id-wenjianjia-biaoshi" name="wenjianjia_biaoshi" required class="wenben-shuru" placeholder="仅限字母、数字、下划线">
                </div>
                <div class="biaodan-xiang">
                    <label for="id-wenjianjia-miaoshu">描述（可选）：</label>
                    <input type="text" id="id-wenjianjia-miaoshu" name="wenjianjia_miaoshu" class="wenben-shuru">
                </div>
                <button type="submit" class="tijiao-anniu">添加文件夹</button>
            </form>
            <div class="liebiao-rongqi">
                <h3>现有文件夹</h3>
                <div id="id-xiugai-wenjianjia-biaodan" style="display:none; margin-bottom: 20px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">
                    <h4 style="margin-bottom: 10px;">修改文件夹信息</h4>
                    <form method="POST">
                        <input type="hidden" name="caozuo" value="xiugai_wenjianjia">
                        <input type="hidden" id="id-jiu-biaoshi" name="jiu_biaoshi" value="">
                        <div class="biaodan-xiang">
                            <label for="id-xin-mingcheng">文件夹名称：</label>
                            <input type="text" id="id-xin-mingcheng" name="xin_mingcheng" required class="wenben-shuru">
                        </div>
                        <div class="biaodan-xiang">
                            <label for="id-xin-biaoshi">文件夹标识符：</label>
                            <input type="text" id="id-xin-biaoshi" name="xin_biaoshi" required class="wenben-shuru">
                        </div>
                        <div class="biaodan-xiang">
                            <label for="id-xin-miaoshu">描述（可选）：</label>
                            <input type="text" id="id-xin-miaoshu" name="xin_miaoshu" class="wenben-shuru">
                        </div>
                        <button type="submit" class="tijiao-anniu">确认修改</button>
                        <button type="button" onclick="quxiao_xiugai()" class="quxiao-anniu" style="margin-left: 10px;">取消</button>
                    </form>
                </div>
                <?php foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj): ?>
                    <div class="liebiao-xiang">
                        <div class="liebiao-neirong">
                            <strong><?php echo htmlspecialchars($wj['mingcheng']); ?></strong>
                            <span class="biaoshi">(<?php echo htmlspecialchars($wj['biaoshi']); ?>)</span>
                            <?php if (!empty($wj['miaoshu'])): ?>
                                <p class="miaoshu"><?php echo htmlspecialchars($wj['miaoshu']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="caozuo-anniu-zu">
                            <button type="button" onclick="xiugai_wenjianjia_mingcheng('<?php echo htmlspecialchars($wj['biaoshi']); ?>', '<?php echo htmlspecialchars($wj['mingcheng']); ?>', '<?php echo isset($wj['miaoshu']) ? htmlspecialchars($wj['miaoshu']) : ''; ?>')" class="bianji-anniu">修改</button>
                            <form method="POST" style="display:inline;" onsubmit="return queren_shanchu_wenjianjia('<?php echo htmlspecialchars($wj['biaoshi']); ?>', '<?php echo htmlspecialchars($wj['mingcheng']); ?>')">
                                <input type="hidden" name="caozuo" value="shanchu_wenjianjia">
                                <input type="hidden" name="wenjianjia_biaoshi" value="<?php echo htmlspecialchars($wj['biaoshi']); ?>">
                                <button type="submit" class="shanchu-anniu">删除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="guanli-quyu">
            <h2 class="quyu-biaoti">链接管理</h2>
            <form method="POST" class="tianjia-biaodan" onsubmit="return yanzheng_hunhe_lianjie()">
                <input type="hidden" name="caozuo" value="tianjia_lianjie">
                <div class="biaodan-xiang">
                    <label for="id-lianjie-mingcheng">链接名称：</label>
                    <input type="text" id="id-lianjie-mingcheng" name="lianjie_mingcheng" required class="wenben-shuru" placeholder="例如：动漫风景混合">
                </div>
                <div class="biaodan-xiang">
                    <label for="id-lianjie-biaoshi">链接标识符：</label>
                    <input type="text" id="id-lianjie-biaoshi" name="lianjie_biaoshi" required class="wenben-shuru" placeholder="仅限字母、数字、下划线">
                </div>
                <div class="biaodan-xiang">
                    <label>选择关联文件夹（至少选 2 个）：</label>
                    <div class="duoxuan-rongqi">
                        <?php foreach ($peizhi_shuju['wenjianjia_liebiao'] as $wj): ?>
                            <label class="duoxuan-xiang">
                                <input type="checkbox" name="wenjianjia_biaoshi_shuzu[]" value="<?php echo htmlspecialchars($wj['biaoshi']); ?>">
                                <?php echo htmlspecialchars($wj['mingcheng']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="tijiao-anniu">创建混合链接</button>
            </form>
            <div class="liebiao-rongqi">
                <h3>现有链接</h3>
                <?php foreach ($peizhi_shuju['lianjie_peizhi'] as $lj): ?>
                    <div class="liebiao-xiang">
                        <div class="liebiao-neirong">
                            <strong><?php echo htmlspecialchars($lj['mingcheng']); ?></strong>
                            <span class="biaoshi"><?php echo htmlspecialchars($lj['biaoshi']); ?></span>
                            <?php if (isset($lj['zidong_chuangjian']) && $lj['zidong_chuangjian']): ?>
                                <span class="zidong-biaoji" title="随文件夹自动生成">🔒 自动生成</span>
                            <?php endif; ?>
                            <p class="lian-jie-dizhi">
                                地址：<code><?php echo $jichu_url . '/suiji.php?id=' . htmlspecialchars($lj['biaoshi']); ?></code>
                                <button type="button" onclick="fuzhi_lianjie('<?php echo $jichu_url . '/suiji.php?id=' . htmlspecialchars($lj['biaoshi']); ?>')" class="fuzhi-xiao-anniu">复制</button>
                            </p>
                        </div>
                        <div class="caozuo-anniu-zu">
                            <?php if (isset($lj['zidong_chuangjian']) && $lj['zidong_chuangjian']): ?>
                                <button type="button" class="shanchu-anniu" style="opacity:0.5; cursor:not-allowed;" title="请删除关联文件夹来移除此链接">删除</button>
                            <?php else: ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('确定要移除此混合链接吗？')">
                                    <input type="hidden" name="caozuo" value="shanchu_lianjie">
                                    <input type="hidden" name="lianjie_biaoshi" value="<?php echo htmlspecialchars($lj['biaoshi']); ?>">
                                    <button type="submit" class="shanchu-anniu">删除</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="daohang-anniu-zu">
        <a href="index.php" class="daohang-anniu">🖼️ 图片管理</a>
        <a href="guanli.php" class="daohang-anniu dangqian">📁 文件夹管理</a>
        <a href="tuichu.php" class="daohang-anniu" style="background-color: #95a5a6; box-shadow: 0 4px 8px rgba(149, 165, 166, 0.4);">🚪 退出登录</a>
    </div>

    <footer class="yejiao" id="id-yejiao">
        <p>© <a href="https://iudog.com" target="_blank">iudog</a> 版权所有，<a href="https://github.com/iudog/iudog-randimg" target="_blank">本项目github仓库</a></p>
    </footer>

    <script src="JavaScript/guanli.js"></script>
</body>
</html>