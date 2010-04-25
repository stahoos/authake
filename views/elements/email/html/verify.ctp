<h3><?php echo sprintf(__('Your e-mail verification at %s', true),  $service);?></h3>
<p><?php echo __('You changed your e-mail address in your profile.', true);?> <?php echo __('To ensure that this e-mail is valid, please follow this link:', true);?></p>
<p><?php
$url = $html->url(array('plugin'=>'authake', 'controller'=>'user', 'action'=>'verify', $code), true);
echo $html->link(__('Click here to verify', true), $url);?>
</p>
<p><?php echo sprintf(__('Verification code: %s', true), $code);?></p>
<p><?php echo __('Best regards', true);?><br/><?php echo $service;?></p>