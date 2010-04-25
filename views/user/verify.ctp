<div id="authake">
<?php echo $this->element('gotohomepage'); ?>
<div class="change_password form">
<?php echo $form->create(null, array('action'=>'verify'));?>
	<fieldset>
 		<legend><?php __('Confirmation');?></legend>
	<?php
		echo $form->input('code', array('size'=>'40', 'title'=>__('Please insert the code which you received in your e-mail.', true)));
	?>
	</fieldset>
<?php echo $form->end(__('Confirm', true));?>
</div>
</div>