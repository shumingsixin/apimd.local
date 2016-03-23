<?php
// $model IBooking.
//$this->headerUTF8();
$files = $model->getFiles();
?>

<h3>预约详情：</h3>
<table cellpadding="8" border="1">
    <tbody>
        <tr><td>预约号：</td><td><?php echo $model->getRefNumber(); ?></td></tr>
        <tr><td>创建日期：</td><td><?php echo $model->getCreateDate(); ?></td></tr>
        <tr><td>预约分类：</td><td><?php echo $model->getBookingTypeText(); ?></td></tr>
        <?php
        switch ($model->getBookingType()) {
            case '1':
                ?>
                <tr><td>所选科室：</td><td><b><?php echo $model->getFacultyName(); ?></b></td></tr>   
                <?php
                break;
            case '2':
                ?>
                <tr><td>所选医生：</td><td><b><?php echo $model->getDoctorName(); ?></b></td></tr>   
                <?php
                break;
            case '3':
                ?>
                <tr><td>专家团队：</td><td><b><?php echo $model->getExpertTeamName(); ?></b></td></tr>   
                <?php
                break;
            case '4':
                ?>
                <tr><td>所选医院：</td><td><b><?php echo $model->getHospitalName(); ?></b></td></tr>   
                <tr><td>所选科室：</td><td><b><?php echo $model->getDeptName(); ?></b></td></tr>   
                <?php
                break;
            default:
                break;
        }
        ?>
        <tr><td>称呼：</td><td><b><?php echo $model->getContactName(); ?></b></td></tr>    
        <tr><td>手机：</td><td><b><?php echo $model->getMobile(); ?></b></td></tr>
        <tr><td>病情：</td><td><b><?php echo $model->getPatientCondition(); ?></b></td></tr>
    </tbody>
</table>
<h3>上传文件：</h3>
<?php
if (arrayNotEmpty($files)):
    ?>
    <ul>
        <?php
        foreach ($files as $key => $file):
            ?>
            <li><b><a target="blank" href="<?php echo $file->url; ?>"><?php echo "文件 - " . ($key + 1); ?></a></b></li>
            <?php
        endforeach;
        ?>
    </ul>
    <?php
else:
    echo "没有上传文件";
endif;
?>
<br />