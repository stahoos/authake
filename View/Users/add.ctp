<div id="authake">
    <div class="actions menuheader">
	<ul>
	    <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage users'), array('action'=>'index'));?></li>
	</ul>
    </div>
    <div class="users form">
    <?php echo $this->Form->create('User');?>
	    <fieldset>
		    <legend><?php echo __d('authake', 'Create a new user');?></legend>
	    <?php
		    echo $this->Form->input('login', array('label'=>__d('authake', 'Login')));
		    echo $this->Form->input('password', array('label'=>__d('authake', 'Password'), 'size'=>'12'));
		    echo $this->Form->input('email', array('label'=>__d('authake', 'Email'), 'size'=>'40'));
		    echo $this->Form->input('Group', array('label'=>__d('authake', 'In groups<br/>Press \'Control\' for multi-selection'), 'style'=>'width: 15em;'));
	    echo $this->Form->label(__d('authake', 'Disable account'));
	    echo $this->Form->checkbox('disable');
	    
	    ?>
	    </fieldset>
    <?php echo $this->Form->end(__d('authake', 'Create'));?>
    </div>
</div>