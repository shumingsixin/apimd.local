<?php
// $model stdClass
//$this->headerUTF8();
//$files = $model->getFiles();
$files = array();
?>

<h3>预约详情：</h3>
<table cellpadding="8" border="1">
    <tbody>
        <tr><td>预约号：</td><td><?php echo $model->refNo; ?></td></tr>
        <tr><td>创建日期：</td><td><?php echo $model->dateCreated; ?></td></tr>        
        <tr><td>所选专家：</td><td><b><?php echo $model->expertBooked; ?></b></td></tr>   
        <tr><td>所选医院：</td><td><b><?php echo $model->hospitalName; ?></b></td></tr>  
        <tr><td>所选科室：</td><td><b><?php echo $model->hpDeptName; ?></b></td></tr>           
        <tr><td>患者姓名：</td><td><b><?php echo $model->patientName; ?></b></td></tr>    
        <tr><td>手机：</td><td><b><?php echo $model->mobile; ?></b></td></tr>
        <tr><td>疾病诊断：</td><td><b><?php echo $model->diseaseName; ?></b></td></tr>
        <tr><td>病情：</td><td><b><?php echo $model->diseaseDetail; ?></b></td></tr>
    </tbody>
</table>

<?php
if (arrayNotEmpty($files)):
    ?>
    <h3>上传文件：</h3>
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
/*
  else:
  echo "没有上传文件";
 * 
 */
endif;
?>
<br />