<?php
//$user User.
//$trip Trip
//$siteUrl = $this->createAbsoluteUrl('/');
$siteUrl = 'www.guidesky.com';
$contactusUrl = $this->createAbsoluteUrl('site/contact');
$tripUrl = $this->createAbsoluteUrl('trip/view', array('id' => $trip->getId(), 'title' => $trip->getTitle()));
$loginUrl = $this->createAbsoluteUrl('user/login');
?>
<div style="width:600px;">
    <table cellpadding="10" style="width:100%;height:100%;background-color: #FAF8F8;border-collapse:collapse;font-family: 'Microsoft YaHei'; letter-spacing: 1px;">
        <thead>
            <tr style="background-color:#66CDCC;color:#fff;font-size:18px;text-align: left;border-bottom:1px solid #fff;">
                <th colspan="2"><strong><i>Guidesky 伴客旅行</i></strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2"><strong>亲爱的<?php echo $user->getNickname(); ?>，</strong></td>
            </tr>
            <tr>
                <td colspan="2">感谢您在伴客网站发布的伴游项目：</td>
            </tr>
            <tr>
                <td colspan="2" align="center"><?php echo '<strong><a href="' . $tripUrl . '">' . $trip->getTitle() . '</strong></a>'; ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    我们已收到您的申请，会在一个工作日内完成审核。您提交的伴游项目通过审核后，将被发布在伴客网站上。您可以随时登录伴客网，查看并修改您发起的伴游项目。
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <a href="<?php echo $loginUrl; ?>" style="display:inline-block;padding:8px 50px;margin:0;border:1px solid #f63;color:#fff;background-color: #f63;text-align: center;text-decoration: none;"><strong>登录伴客</strong></a>
                </td>
            </tr>
            <tr>
                <td colspan="2">我们感谢您的热心分享！希望通过伴客平台，您能找到更多志趣相投的朋友！</td>
            </tr>  
            <tr>
                <td colspan="2"><p style="font-size:0.9em;color:#777;"><i>此电子邮件为系统自动发送，请勿直接回复。如有任何问题，欢迎<a href="<?php echo $contactusUrl; ?>">联系我们</a>。</i></p></td>
            </tr> 
            <tr><td></td></tr>
            <tr>
                <td colspan="2">您真诚的：</td>
            </tr> 
            <tr>
                <td colspan="2">伴客旅行</td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $siteUrl; ?></td>
            </tr>
        </tbody>
    </table>
</div>