<?php
// $model IMedicalRecordBooking
$faculty = $model->getFaculty();
$mr = $model->getMedicalRecord();
?>

<h3>预约详情：</h3>
<table cellpadding="8" border="1">
    <tbody>
        <tr><td>预约号：</td><td><?php echo $model->getRefNumber(); ?></td></tr>
        <tr><td>创建日期：</td><td><?php echo $model->getDateCreated(); ?></td></tr>
        <tr><td>科室：</td><td><b><?php echo $faculty->getName(); ?></b></td></tr>
        <tr><td>预约日期：</td><td><b><?php echo $model->getApptDate() . '&nbsp;' . $model->getBufferDays(); ?></b></td></tr>
        <tr><td>手机：</td><td><b><?php echo $model->getContactMobile(); ?></b></td></tr>
        <tr><td>邮箱：</td><td><?php echo $model->getContactEmail(); ?></td></tr>
        <tr><td>微信：</td><td><?php echo $model->getContactWeixin(); ?></td></tr>
    </tbody>
</table>
<br />
<h3>患者信息：</h3>
<table cellpadding="8" border="1">
    <tbody>
        <tr><td>姓名：</td><td><?php echo $mr->getPatientName(); ?></td></tr>
        <tr><td>年龄：</td><td><?php echo $mr->getPatientAge(); ?></td></tr>
        <tr><td>性别：</td><td><?php echo $mr->getPatientGender(); ?></td></tr>
        <tr><td>所在地区：</td><td><?php echo $mr->getPlaceFrom('&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td></tr>
        <tr><td>职业：</td><td><?php echo $mr->getOccupation(); ?></td></tr>
        <tr><td>病情：</td><td><?php echo $mr->getPatientCondition(true); ?></td></tr>
        <tr><td>药物过敏：</td><td><?php echo $mr->getDrugAllergy(true); ?></td></tr>
        <tr><td>手术史：</td><td><?php echo $mr->getSurgeryHistory(true); ?></td></tr>
        <tr><td>用药史：</td><td><?php echo $mr->getDrugHistory(true); ?></td></tr>
        <tr><td>既往病史：</td><td><?php echo $mr->getDiseaseHistory(true); ?></td></tr>
    </tbody>
</table>