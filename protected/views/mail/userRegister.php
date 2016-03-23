<?php
//$user User.
//$siteUrl = $this->createAbsoluteUrl('/');
$siteUrl = 'www.guidesky.com';
$contactusUrl = $this->createAbsoluteUrl('site/contact');
$faqUrl = $this->createAbsoluteUrl('site/page', array('view' => 'faq'));
$rulesUrl = $this->createAbsoluteUrl('site/page', array('view' => 'rules'));
$createTripUrl = $this->createAbsoluteUrl('trip/create');
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
                <td colspan="2">欢迎您注册成为伴客用户！</td>
            </tr>
            <tr>
                <td colspan="2">
                    伴客旅行是一家有理想的旅行新创企业。我们希望利用互联网的资源整合功能，搭建一个便捷而规范的旅行网络平台，让大家享受到非同一般的旅行体验。成功注册为伴客用户， 您可以：<br />
                    <ul><li>搜索您感兴趣的旅行项目</li>
                        <li>与发起该项目的伴客在线交流，量身打造您的旅程</li>
                        <li>在线预订，查询和管理您的预订信息</li>
                        <li>点评您参与的旅行项目</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    您还可以：<br />
                    <ul>
                        <li>在伴客平台发起一个富有创意的旅行项目</li>
                        <li>与感兴趣的报名者在线沟通，用您的热情为Ta设计独具创意的旅程</li>
                        <li>接受和管理您的订单</li>
                        <li>在享受与志同道合的朋友一起的旅程的同时，获取相应的报酬。</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    跃跃欲试了吗？立刻开始发起您的第一个伴游项目吧！<br /><br />
                    <a href="<?php echo $createTripUrl; ?>" style="display:inline-block;padding:8px 50px;margin:0;border:1px solid #f63;color:#fff;background-color: #f63;text-align: center;text-decoration: none;"><strong>发布伴游</strong></a> </td>
            </tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="2">敬请阅读我们的<a href="<?php echo $rulesUrl; ?>"><strong>使用规则</strong></a>及<a href="<?php echo $faqUrl; ?>"><strong>常见问题</strong></a>或<a href="<?php echo $loginUrl; ?>"><strong>登录</strong></a>伴客网站，了解更多信息。</td>
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