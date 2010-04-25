<h3><?php echo sprintf(__('You requested a password change at %s', true),  $service);?></h3>
<p><?php echo __('Following the link below you can change your password:', true);?></p>
<p><?php
$url = $html->url(array('plugin'=>'authake', 'controller'=>'user', 'action'=>'pass', $code), true);
echo $html->link(__('Click here to change your password', true), $url);?>
</p>
<p><?php echo sprintf(__('Verification code: %s', true), $code);?></p>
<p><?php echo __("If you don't request this change, no action is required. Your password will remain the same until you don't activate this code.", true);?></p>
<p><?php echo __('Best regards', true);?><br/><?php echo $service;?></p>