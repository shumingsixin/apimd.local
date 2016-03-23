<?php //$model User. ?>
<table style="width:100%;height:100%;background-color: #FAF8F8;border-collapse:collapse;font-family: 'Microsoft YaHei';">
    <thead>
        <tr style="background-color:#66CDCC;color:#fff;font:italic 18px 'Microsoft YaHei';height:40px;text-align: left;text-indent:10px;border-bottom:1px solid #fff;"><th><i>Guidesky 伴客旅行</i></th></tr>
    </thead>
    <tbody>
        <tr style="height:40px;text-indent:20px;color:#000">
            <td><strong>亲爱的<?php echo $model->getNickname();?>，</strong></td>
        </tr>
        <tr style="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
            <td>如果您要更改伴客账户密码，请点击&nbsp;<a style="color:#66CDCC;" href="<?php echo $actionLink; ?>">此链接重置密码</a></td>
        </tr>
        <tr style="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
            <td>如果链接无法点击，请复制并粘贴以下地址到浏览器：</td>
        </tr>
        <tr style="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
            <td><?php echo $actionLink; ?></td>
        </tr>
        <tr style="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
            <td>注：本连接将在<?php echo $expiry; ?>小时后失效</td>
        </tr>
        <tr style="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
            <td>如果您有任何问题，请及时<a href="<?php echo $this->createAbsoluteUrl('site/contact');?>">联系我们</a></td>
        </tr>
    </tbody>
</table>