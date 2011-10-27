<div id="authake">
    <div class="actions menuheader">
	<ul>
	    <li class="icon user"><?php echo $this->Html->link(__('Manage users'), array('action'=>'index'));?></li>
	</ul>
    </div>
    <div class="users form">
    <?php echo $this->Form->create('User');?>
	    <fieldset>
		    <legend><?php __('Create a new user');?></legend>
	    <?php
		    echo $this->Form->input('login', array('label'=>__('Login')));
		    echo $this->Form->input('password', array('label'=>__('Password'), 'size'=>'12'));
		    echo $this->Form->input('email', array('label'=>__('Email'), 'size'=>'40'));
		    echo $this->Form->input('Group', array('label'=>__('In groups<br/>Press \'Control\' for multi-selection'), 'style'=>'width: 15em;'));
	    echo $this->Form->label(__('Disable account'));
	    echo $this->Form->checkbox('disable');
	    
	    ?>
	    </fieldset>
    <?php echo $this->Form->end(__('Create'));?>
    </div>
</div>