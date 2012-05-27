<div id="authake">
<div class="actions menuheader">
    <ul>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('action'=>'index'));?></li>
    </ul>
</div>
<div class="rules form">
<?php echo $this->Form->create('Rule');?>
	<fieldset>
 		<legend><?php echo __d('authake', 'Add Rule');?></legend>
	<?php
        echo $this->Form->input('name', array('label'=>__d('authake', 'Description'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'2'));
        echo $this->Form->input('group_id', array('label'=>__d('authake', 'Group'), 'empty'=>true));
        echo $this->Form->input('order', array('label'=>__d('authake', 'Order')));
        echo $this->Form->input('action', array('label'=>__d('authake', 'Action<br/>(perl regex)'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'3'));
        echo $this->Form->input('permission', array('label'=>__d('authake', 'Permission'), 'style'=>'width: 5em;'));
        echo $this->Form->input('forward', array('label'=>__d('authake', 'Forward action on deny')));
        echo $this->Form->input('message', array('label'=>__d('authake', 'Flash message on deny'), 'type'=>'textarea', 'cols'=>'50', 'rows'=>'2'));
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
</div>
