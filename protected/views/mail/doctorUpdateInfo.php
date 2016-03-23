
<h2>专家修改后的信息</h2>

<table class="detail-view" id="yw0">
    <tbody>
        <tr class="even"><th>医生编号: </th><td><?php echo $data->getId(); ?></td></tr>
        <tr class="even"><th>医生姓名: </th><td><?php echo $data->getName(); ?></td></tr>
        <tr class="even"><th>电话号码: </th><td><?php echo $data->getMobile(); ?></td></tr>
        <tr class="even"><th>医生性别: </th><td><?php echo $data->getGender(); ?></td></tr>
        <tr class="even"><th>所在医院: </th><td><?php echo $data->getHospitalName(); ?></td></tr>
        <tr class="even"><th>所属科室: </th><td><?php echo $data->getHpDeptName(); ?></td></tr>
        <tr class="even"><th>临床职称: </th><td><?php echo $data->getClinicalTitle(); ?></td></tr>   
        <tr class="even"><th>学术职称: </th><td><?php echo $data->getAcademictitle(); ?></td></tr>   
        <tr class="even"><th>所在省: </th><td><?php echo $data->getStateName(); ?></td></tr>
        <tr class="even"><th>所在城市: </th><td><?php echo $data->getCityName(); ?></td></tr>
        <tr class="even"><th>创建/修改时间: </th><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>
        <tr class="even"><th>提示 </th><td><?php echo '希望能快速审核!' ?></td></tr>
    </tbody>
</table>

