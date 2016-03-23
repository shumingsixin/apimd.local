<?php
// $model ContactEnquiry
?>
<p>
    日期：
    <?php
    $now = new DateTime();
    echo $now->format('Y-m-d H:i:s');
    ?>
</p>
<p>
    <span>患者姓名：</span>
    <span><strong><?php echo $model->getName(); ?></strong></span>
</p>
<span>患者年龄：</span>
<span><strong><?php echo $model->getAge(); ?></strong>岁</span>
<p>
    <span>联系电话：</span>
    <span><strong><?php echo $model->getMobile(); ?></strong></span>
</p>
<p>
    <span>选择科室：</span>
    <span><strong><?php echo $model->getFacultyName(); ?></strong></span>
</p>
<p>
    <span>病情描述：</span>
    <span><strong><?php echo $model->getPatientCondition(); ?></strong></span>
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