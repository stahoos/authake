<div id="authake">
<div class="actions menuheader">
    <ul>
        <li class="icon group"><?php echo $this->Html->link(__d('authake', 'Manage groups'), array('action'=>'index'));?></li>
    </ul>
</div>
<div class="groups form">
<?php echo $this->Form->create('Group');?>
	<fieldset>
        <legend><?php echo __d('authake', 'Create a new group');?></legend>   
	<?php
		echo $this->Form->input('name', array('label'=>__d('authake', 'Name')));
        echo $this->Form->input('User', array('label'=>__d('authake', 'Users in this group<br/>Press \'Control\' for multi-selection'), 'style'=>'width: 15em;'));
	?>
	</fieldset>
<?php echo $this->Form->end(__d('authake', 'Create'));?>
</div>
</div>
