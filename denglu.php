<?php
require_once 'peizhi.php';
if (isset($_SESSION['shifou_denglu']) && $_SESSION['shifou_denglu'] === true) {
    header('Location: index.php');
    exit;
}
$cuowu_xinxi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shuru_zhanghao = isset($_POST['zhanghao']) ? trim($_POST['zhanghao']) : '';
    $shuru_mima = isset($_POST['mima']) ? trim($_POST['mima']) : '';
    $zhengque_zhanghao = $peizhi_shuju['guanliyuan']['zhanghao'];
    $zhengque_mima = $peizhi_shuju['guanliyuan']['mima'];
    if ($shuru_zhanghao === $zhengque_zhanghao && $shuru_mima === $zhengque_mima) {
        $_SESSION['shifou_denglu'] = true;
        header('Location: index.php');
        exit;
    } else {
        $cuowu_xinxi = '账号或密码错误，请重新输入！';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .denglu-rongqi {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .denglu-biaoti {
            text-align: center;
            color: #ff1493;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="rongqi">
        <div class="denglu-rongqi">
            <h2 class="denglu-biaoti">🔒 系统登录</h2>
            <?php if ($cuowu_xinxi): ?>
                <div class="tishi shibai">
                    <?php echo htmlspecialchars($cuowu_xinxi); ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="shangchuan-biaodan">
                <div class="biaodan-xiang">
                    <label for="id-zhanghao">管理员账号：</label>
                    <input type="text" id="id-zhanghao" name="zhanghao" required class="wenben-shuru" placeholder="请输入账号">
                </div>
                <div class="biaodan-xiang">
                    <label for="id-mima">登录密码：</label>
                    <input type="password" id="id-mima" name="mima" required class="wenben-shuru" placeholder="请输入密码">
                </div>
                <button type="submit" class="tijiao-anniu" style="margin-top: 10px;">立即登录</button>
            </form>
        </div>
    </div>

    <footer class="yejiao" id="id-yejiao">
        <p>© <a href="https://iudog.com" target="_blank">iudog</a> 版权所有，<a href="https://github.com/iudog/iudog-randimg" target="_blank">本项目github仓库</a></p>
    </footer>
</body>
</html>