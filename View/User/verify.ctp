<div id="authake">
<?php echo $this->element('gotohomepage'); ?>
<div class="change_password form">
<?php echo $this->Form->create(null, array('action'=>'verify'));?>
	<fieldset>
 		<legend><?php echo __d('authake', 'Confirmation');?></legend>
	<?php
		echo $this->Form->input('code', array('size'=>'40', 'title'=>__d('authake', 'Please insert the code which you received in your e-mail.')));
	?>
	</fieldset>
<?php echo $this->Form->end(__d('authake', 'Confirm'));?>
</div>
</div>