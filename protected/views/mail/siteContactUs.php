<?php
// $model Contactus
?>
<p>
    日期：
    <?php
    $now = new DateTime();
    echo $now->format('Y-m-d H:i:s');
    ?>
</p>
<p>
    <span>联系电话：</span>
    <span><strong><?php echo $model->mobile; ?></strong></span>
</p>
<p>
    <span>咨询内容：</span><br />
    <span><strong><?php echo Yii::app()->format->formatNtext($model->message); ?></strong></span>
</p>
<br />
<p>
    <span>来自于：</span>
    <span><?php
        if ($model->access_agent == 'app') {
            echo 'App';
        } elseif ($model->access_agent == 'wechat' || $model->access_agent == 'weixin') {
            echo '微信';
        } else {
            echo '网站';
        }
        ?></span>
</p>
<br />
<p>请尽快回复患者</p>