<?php
//$booking IBooking.
//$user User.
$siteUrl = $this->createAbsoluteUrl('/');
$bookingUrl = $this->createAbsoluteUrl('booking/view', array('refNo' => $booking->getReferenceNumber()));
$faqUrl = $this->createAbsoluteUrl('site/page', array('view' => 'faq'));
$rulesUrl = $this->createAbsoluteUrl('site/page', array('view' => 'rules'));
?>
<div style="width:600px;">
    <table cellpadding="10" style="width:100%;height:100%;background-color: #FAF8F8;border-collapse:collapse;font-family: 'Microsoft YaHei';">
        <thead>
            <tr style="background-color:#66CDCC;color:#fff;font-size:18px;text-align: left;border-bottom:1px solid #fff;">
                <th colspan="2"><strong><i>Guidesky 伴客旅行</i></strong></th>
            </tr>
        </thead>
        <tbody>
            <tr xstyle="height:40px;text-indent:20px;color:#000">
                <td colspan="2"><strong>亲爱的<?php echo $user->getNickname(); ?>，</strong></td>
            </tr>
            <tr xstyle="height:40px;text-indent:20px;color:#000;font:normal 13px 'Microsoft YaHei',sans-serif">
                <td colspan="2">恭喜您，您在<a href="<?php echo $siteUrl; ?>">伴客</a>网站上的交易成功 。您的预定金已成功支付给伴客网站，我们将代您保管预定金，并在旅行结束后支付给伴客。</td>
            </tr>
            <tr>
                <td colspan="2">预订详情如下：</td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">交易单号：</td><td><?php echo $tranRefNo; ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">伴游项目：</td><td><?php echo $trip->getTitle(); ?></td>
            </tr>
            <?php
            $priceItem = $booking->getPriceItem();
            if (empty($priceItem) === false) {
                echo '<tr>';
                echo '<td style="width:20%;text-align: right;">款项名称：</td><td>' . $priceItem . '</td>';
                echo '</tr>';
            }
            ?>
            <tr>
                <td style="width:20%;text-align: right;">出行日期：</td><td><?php echo $booking->getStartDateText(); ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">开始时间：</td><td><?php echo $booking->getStartTimeText(); ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">成人：</td><td><?php echo $booking->getHeadcountText('无'); ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">儿童：</td><td><?php echo $booking->getHeadcountChildText('无'); ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">金额：</td><td><?php echo $booking->getTotalPriceText(); ?></td>
            </tr>
            <tr>
                <td style="width:20%;text-align: right;">备注：</td><td><?php echo $booking->getRequest(); ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="<?php echo $bookingUrl; ?>" style="display:inline-block;padding:8px 40px;margin:0;border:1px solid #f63;color:#fff;background-color: #f63;text-align: center;text-decoration: none;">查看预订详情</a>
                </td>
            </tr>

            <tr>
                <td colspan="2">伴客旅行友情提醒您，请提前计划您的时间，安排您所提供旅行项目，与游客保持沟通交流，并确保您有游客在旅行当天的联系方式。您的热心和友好将是游客在陌生城市里最大的依靠和最美的回忆。 如有任何疑问，欢迎参考我们的<a href="<?php echo $rulesUrl; ?>">使用规则</a>以及<a href="<?php echo $faqUrl; ?>">常见问题</a>。</td>
            </tr> 
            <tr>
                <td colspan="2"> 预祝您有一个愉快的伴游体验！期待您再次使用伴客网站！</td>
            </tr>           
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="2">您真诚的：</td>
            </tr> 
            <tr>
                <td colspan="2">伴客旅行</td>
            </tr>
            <tr>
                <td colspan="2">www.guidesky.com</td>
            </tr>
        </tbody>
    </table>
</div>