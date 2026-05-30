function xianshi_qipao_tishi(xinxi) {
    var jiu_qipao = document.querySelector('.qipao-tishi');
    if (jiu_qipao) {
        document.body.removeChild(jiu_qipao);
    }
    var qipao = document.createElement('div');
    qipao.className = 'qipao-tishi';
    qipao.innerText = xinxi;
    document.body.appendChild(qipao);
    qipao.offsetHeight;
    qipao.classList.add('xianshi');
    setTimeout(function() {
        qipao.classList.remove('xianshi');
        setTimeout(function() {
            if (qipao.parentNode) {
                document.body.removeChild(qipao);
            }
        }, 300);
    }, 1500);
}
function fu_zhi_tong_yong_fang_fa(wen_ben) {
    var wen_ben_yu = document.createElement('textarea');
    wen_ben_yu.value = wen_ben;
    wen_ben_yu.style.position = 'fixed';
    wen_ben_yu.style.opacity = '0';
    document.body.appendChild(wen_ben_yu);
    wen_ben_yu.focus();
    wen_ben_yu.select();
    try {
        document.execCommand('copy');
        xianshi_qipao_tishi('复制成功 ✨');
    } catch (cuowu) {
        alert('自动复制失败，请手动复制：' + wen_ben);
    }
    document.body.removeChild(wen_ben_yu);
}
window.addEventListener('load', function() {
    var anniu_liebiao = document.querySelectorAll('.fuzhi-anniu');
    anniu_liebiao.forEach(function(anniu) {
        anniu.addEventListener('click', function() {
            var lianjie_xiang = this.closest('.lianjie-xiang');
            var lianjie = lianjie_xiang ? lianjie_xiang.getAttribute('data-lianjie-url') : '';
            if (!lianjie) {
                alert('链接数据缺失，请刷新页面重试');
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(lianjie).then(function() {
                    xianshi_qipao_tishi('复制成功 ✨');
                }).catch(function() {
                    fu_zhi_tong_yong_fang_fa(lianjie);
                });
            } else {
                fu_zhi_tong_yong_fang_fa(lianjie);
            }
        });
    });
});
