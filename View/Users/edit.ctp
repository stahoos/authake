<div id="authake">
    <div class="actions menuheader">
	<ul>
	    <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage users'), array('action'=>'index'));?></li>
	</ul>
    </div>
    <div class="users form">
    <?php echo $this->Form->create('User');?>
	    <fieldset>
	    <legend><?php echo __d('authake', 'Modify user'); echo " ".$this->request->data['User']['login']; ?></legend>
 	  	    <?php
		    echo $this->Form->input('id');
		    echo $this->Form->input('Group', array('label'=>__d('authake', 'In groups<br/>Press \'Control\' for multi-selection'), 'style'=>'width: 15em;'));
		    echo $this->Form->input('email', array('label'=>__d('authake', 'Email'), 'size'=>'40'));
		    echo $this->Form->input('emailcheckcode', array('label'=>__d('authake', 'Email check code')));
		    echo $this->Form->input('passwordchangecode', array('label'=>__d('authake', 'Password change code')));
		    echo $this->Form->input('password', array('label'=>__d('authake', 'New password (visible!)'), 'type'=>'text', 'value' => '', 'size'=>'12'));
    
		    echo $this->Form->label(__d('authake', 'Disable account'));
		    echo $this->Form->checkbox('disable');
     
		    echo $this->Form->input('expire_account', array('label'=>__d('authake', 'Account expiry date')));
	    ?>
	    </fieldset>
    <?php echo $this->Form->end(__d('authake', 'Modify'));?>
    </div>

    <div class="actions">
	<ul>
	    <li class="icon info"><?php echo $this->Html->link(__d('authake', 'View user'), array('action'=>'view', $this->Form->value('User.id')));?></li>
	    <li class="icon cross"><?php echo $this->Html->link(__d('authake', 'Delete'), array('action'=>'delete', $this->Form->value('User.id')), null, sprintf(__d('authake', 'Are you sure you want to delete # %s?'), $this->Form->value('User.login'))); ?></li>
	</ul>
    </div>
</div>
