<?php
$salesOrder = $data->salesOrder;
$booking = $data->booking;
?>

<h2>支付详情</h2>

<table class="detail-view" id="yw0">
    <tbody>
        <tr class="even"><th>支付单编号: </th><td><?php echo $salesOrder->id; ?></td></tr>
        <tr class="even"><th>支付单单号: </th><td><?php echo $salesOrder->refNo; ?></td></tr>
        <tr class="even"><th>支付单标题: </th><td><?php echo $salesOrder->subject; ?></td></tr>
        <tr class="even"><th>支付单内容: </th><td><?php echo $salesOrder->description; ?></td></tr>
        <tr class="even"><th>支付金额: </th><td><?php echo $salesOrder->finalAmount; ?></td></tr>
        <tr class="even"><th>支付状态: </th><td><?php echo $salesOrder->isPaidText; ?></td></tr>
        <tr class="even"><th>支付类型: </th><td><?php echo $salesOrder->orderType; ?></td></tr>
        <tr class="even"><th>支付时间: </th><td><?php echo $salesOrder->dateClose; ?></td></tr>
    </tbody>
</table>

<br/>

<h2>预约详情</h2>

<table class="detail-view" id="yw0">
    <tbody>
        <tr class="even"><th>预约: </th><td><?php echo $booking->expertBooked; ?></td></tr>
        <tr class="odd"><th>患者姓名: </th><td><span class="null"><?php echo $booking->patientName; ?></span></td></tr>
        <tr class="even"><th>患者手机: </th><td><?php echo $booking->mobile; ?></td></tr>
        <tr class="odd"><th>疾病诊断: </th><td><?php echo $booking->diseaseName; ?></td></tr>
        <tr class="odd"><th>病情: </th><td><?php echo $booking->diseaesDetail; ?></td></tr>
        <tr class="even"><th>创建日期: </th><td><?php echo $booking->dataCreated; ?></td></tr>
    </tbody>
</table>