function fuzhi_lianjie(lianjie) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(lianjie).then(function() {
            xianshi_qipao_tishi('复制成功 ✨');
        }).catch(function() {
            fu_zhi_tong_yong_fang_fa(lianjie);
        });
    } else {
        fu_zhi_tong_yong_fang_fa(lianjie);
    }
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
function queren_shanchu_wenjianjia(biaoshi, mingcheng) {
    var rongqi = document.getElementById('id-rongqi');
    var shanchu_yuce_shuju = JSON.parse(rongqi.getAttribute('data-wenjianjia-shanchu-yuce') || '{}');
    var yao_shanchu_lianjie = shanchu_yuce_shuju[biaoshi] || [];
    var tishi_neirong = '⚠️ 警告：删除文件夹将同时删除该文件夹内的所有图片文件，此操作不可恢复！\n\n';
    if (yao_shanchu_lianjie.length > 0) {
        tishi_neirong += '当前操作会联动删除以下链接：\n';
        yao_shanchu_lianjie.forEach(function(lianjie_ming, suo_yin) {
            tishi_neirong += (suo_yin + 1) + '. ' + lianjie_ming + '\n';
        });
        tishi_neirong += '\n';
    }
    tishi_neirong += '确定要继续删除文件夹「' + mingcheng + '」吗？';
    return confirm(tishi_neirong);
}
function xiugai_wenjianjia_mingcheng(biaoshi, dangqian_mingcheng, dangqian_miaoshu) {
    var xiugai_biaodan_rongqi = document.getElementById('id-xiugai-wenjianjia-biaodan');
    var jiu_biaoshi_shuru = document.getElementById('id-jiu-biaoshi');
    var xin_mingcheng_shuru = document.getElementById('id-xin-mingcheng');
    var xin_biaoshi_shuru = document.getElementById('id-xin-biaoshi');
    var xin_miaoshu_shuru = document.getElementById('id-xin-miaoshu');
    jiu_biaoshi_shuru.value = biaoshi;
    xin_mingcheng_shuru.value = dangqian_mingcheng;
    xin_biaoshi_shuru.value = biaoshi;
    xin_miaoshu_shuru.value = dangqian_miaoshu || '';
    xiugai_biaodan_rongqi.style.display = 'block';
    xin_mingcheng_shuru.focus();
}
function quxiao_xiugai() {
    var xiugai_biaodan_rongqi = document.getElementById('id-xiugai-wenjianjia-biaodan');
    xiugai_biaodan_rongqi.style.display = 'none';
}
function yanzheng_hunhe_lianjie() {
    var xuanzhong_shuzu = document.querySelectorAll('input[name="wenjianjia_biaoshi_shuzu[]"]:checked');
    if (xuanzhong_shuzu.length < 2) {
        alert('⚠️ 提示：手动创建的混合链接必须至少选择 2 个文件夹！');
        return false;
    }
    return true;
}