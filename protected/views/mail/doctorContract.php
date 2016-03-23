
<h2>专家信息</h2>

<table class="detail-view" id="yw0">
    <tbody>
        <tr class="even"><th>医生编号: </th><td><?php echo $data->id; ?></td></tr>
        <tr class="even"><th>医生姓名: </th><td><?php echo $data->name; ?></td></tr>
        <tr class="even"><th>电话号码: </th><td><?php echo $data->mobile; ?></td></tr>
        <tr class="even"><th>原来医生擅长疾病: </th><td><?php echo $data->oldPreferredPatient; ?></td></tr>
        <tr class="even"><th>现在医生擅长疾病: </th><td><?php echo $data->preferredPatient; ?></td></tr>
        <tr class="even"><th>创建/修改时间: </th><td><?php echo $data->dateUpdate; ?></td></tr>
    </tbody>
</table>

