<div id="authake">
<?php echo $this->element('gotohomepage'); ?>
<div class="mypassword form">
<?php echo $this->Form->create(null, array('action'=>'lost_password'));?>
<fieldset class="mypassword">
    <?php echo $this->Form->input('loginoremail', array('label'=>__d('authake', 'Login or email'), 'size'=>'40'));?>
</fieldset>
<?php echo $this->Form->end(__d('authake', 'Request for password'))  ?>
</div>
</div>