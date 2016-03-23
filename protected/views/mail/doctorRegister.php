<?php /**
 * $model IDoctor.
 */
?>
<?php
// IDoctorCert.
$certs = $model->getDoctorCerts();
?>
<h4>新的医生注册：</h4>
<p>
    日期：
    <?php
    $now = new DateTime();
    echo $now->format('Y-m-d H:i:s');
    ?>
</p>
<p>
    <span>姓名：</span>
    <span><strong><?php echo $model->name; ?></strong></span>
</p>
<p>
    <span>手机：</span>
    <span><strong><?php echo $model->mobile; ?></strong></span>
</p>
<p>
    <span>所属医院：</span>
    <span><strong><?php echo $model->hpName; ?></strong></span>
</p>
<p>
    <span>所属科室：</span>
    <span><strong><?php echo $model->hpDeptName; ?></strong></span>
</p>
<p>
    <span>临床职称：</span>
    <span><strong><?php echo $model->mTitle; ?></strong></span>
</p>
<p>
    <span>学术职称：</span>
    <span><strong><?php echo $model->aTitle; ?></strong></span>
</p>

<p>
    <span>所在城市：</span>
    <span><strong><?php echo $model->getCityName(); ?></strong></span><span></span>
</p>

<h4>医师资格证：</h4>
<?php
if (arrayNotEmpty($certs)):
    ?>
    <ul>
        <?php
        foreach ($certs as $key => $file):
            ?>
            <li><b><a target="blank" href="<?php echo $file->url; ?>"><?php echo "文件 - " . ($key + 1); ?></a></b></li>
            <?php
        endforeach;
        ?>
    </ul>
    <?php
else:
    echo "没有上传";
endif;
?>

<br />
<br />
<p>请尽快审核该医生</p>