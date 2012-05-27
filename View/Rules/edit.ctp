<div id="authake">
<div class="actions menuheader">
    <ul>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('action'=>'index'));?></li>
    </ul>
</div>
<div class="rules form">
<?php echo $this->Form->create('Rule');?>
	<fieldset>
 		<legend><?php echo __d('authake', 'Modify rule');?></legend>
	<?php
	    echo $this->Form->input('id');
		echo $this->Form->input('name', array('label'=>__d('authake', 'Description'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'2'));
		echo $this->Form->input('group_id', array('label'=>__d('authake', 'Group'), 'empty'=>true));
		echo $this->Form->input('order', array('label'=>__d('authake', 'Order')));
        echo $this->Form->input('action', array('label'=>__d('authake', 'Action<br/>(perl regex)'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'5'));
        echo $this->Form->input('permission', array('label'=>__d('authake', 'Permission'), 'style'=>'width: 5em;'));
        echo $this->Form->input('forward', array('label'=>__d('authake', 'Forward action on error')));
        echo $this->Form->input('message', array('label'=>__d('authake', 'Flash message on deny'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'2'));
	?>
	</fieldset>
<?php echo $this->Form->end('Modify');?>
</div>
<div class="actions">
	<ul>
        <li class="icon info"><?php echo $this->Html->link(__d('authake', 'View rule'), array('action'=>'view', $this->Form->value('Rule.id')));?></li>
		<li class="icon cross"><?php echo $this->Html->link(__d('authake', 'Delete'), array('action'=>'delete', $this->Form->value('Rule.id')), null, sprintf(__d('authake', 'Are you sure you want to delete # %s?'), $this->Form->value('Rule.id'))); ?></li>
	</ul>
</div>
</div>