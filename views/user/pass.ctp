<div id="authake">
<?php echo $this->element('gotohomepage'); ?>
<div class="confirmregister form">
<?php echo $form->create(null, array('action'=>'pass'));?>
	<fieldset>
 		<legend><?php __('Change your password');?></legend>
	<?php
		echo $form->input('passwordchangecode', array('label'=>__('Code', true), 'size'=>'40'));
		echo $form->input('password1', array('size'=>'40', 'type'=>'password'));
		echo $form->input('password2', array('size'=>'40', 'type'=>'password'));
	?>
	</fieldset>
<?php echo $form->end(__('Confirm', true));?>
</div>
</div>