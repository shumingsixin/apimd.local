<h1>预约详情 #<?php echo $model->getRefNo(); ?></h1>

<table class="detail-view" id="yw0">
    <tbody>
    <tr class="even"><th>编号</th><td><?php echo $model->getId(); ?></td></tr>
    <tr class="even"><th>单号</th><td><?php echo $model->getRefNo(); ?></td></tr>
    <tr class="even"><th>状态</th><td><?php echo $model->getBkStatus(); ?></td></tr>
    <tr class="even"><th>预约</th><td><?php echo $model->getExpertNameBooked(); ?></td></tr>
    <tr class="even"><th>医院</th><td><?php echo $model->getHospitalName(); ?></td></tr>
    <tr class="even"><th>科室</th><td><?php echo $model->getHpDeptName(); ?></td></tr>
    <tr class="odd"><th>患者姓名</th><td><span class="null"><?php echo $model->getContactName(); ?></span></td></tr>
    <tr class="even"><th>患者手机</th><td><?php echo $model->getMobile(); ?></td></tr>
    <tr class="odd"><th>疾病诊断</th><td><?php echo $model->getDiseaseName(); ?></td></tr>
    <tr class="odd"><th>病情</th><td><?php echo $model->getDiseaseDetail(); ?></td></tr>
    <tr class="even"><th>创建日期</th><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>
    </tbody>
</table>