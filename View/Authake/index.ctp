<div id="authake">
<div class="actions menuheader">
    <ul>
        <li class="icon door_in"><?php echo $this->Html->link(__d('authake', 'Logout'), array('controller'=> 'user', 'action'=>'logout')); ?></li>
    </ul>
</div>
<div class="index">
<h2><?php echo __d('authake', 'Authake administration page');?></h2>

<div class="actions">
    <ul>
        <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage users'), array('controller'=> 'users', 'action'=>'index')); ?> </li>
        <li class="icon group"><?php echo $this->Html->link(__d('authake', 'Manage groups'), array('controller'=> 'groups', 'action'=>'index')); ?> </li>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('controller'=> 'rules', 'action'=>'index')); ?> </li>
    </ul>
</div>

<h3><?php echo __d('authake', 'Users');?></h3>
<?php echo $this->requestAction('authake/users/index/tableonly', array('return')); ?>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New user'), array('controller'=> 'users', 'action'=>'add')); ?></li>
    </ul>
</div>

<h3><?php echo __d('authake', 'Groups');?></h3>
<?php echo $this->requestAction('authake/groups/index/tableonly', array('return')); ?>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New group'), array('controller'=> 'groups', 'action'=>'add')); ?></li>
    </ul>
</div>

<h3><?php echo __d('authake', 'Rules');?></h3>
<?php echo $this->requestAction('authake/rules/index/tableonly', array('return')); ?>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New rule'), array('controller'=> 'rules', 'action'=>'add')); ?></li>
    </ul>
</div>

</div>
</div>