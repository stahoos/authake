<div id="authake">
<?php if (!$tableonly) { echo $this->element('gotoadminpage'); } ?>
<div class="users index">
<?php if (!$tableonly) { ?>
<h2><?php echo __d('authake', 'Users');?></h2>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New User'), array('action'=>'add')); ?></li>
    </ul>
</div>
<?php } ?>
<p class="paging_count">
<?php
echo $this->Paginator->counter(array(
'format' => __d('authake', 'There are %current% users on this system. Page %page%/%pages%')
));
?></p>
<?php echo $this->Form->create('User', array('class'=>'filter'));?>
<fieldset>
<table class="listing" cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('login');?></th>
    <th><?php echo $this->Paginator->sort('email');?></th>
    <th><?php echo 'Group';?></th>
	<th><?php echo $this->Paginator->sort('Email check', 'emailcheckcode');?></th>
	<th><?php echo $this->Paginator->sort('Change Pwd','passwordchangecode');?></th>
    <th><?php echo $this->Paginator->sort('created');?></th>
    <th><?php echo $this->Paginator->sort(__d('authake', 'Disabled'), 'disable');?></th>
	<th class="actions"><?php echo __d('authake', 'Actions');?></th>
</tr>
<tr class="table-filter">
    <td width="3%"><?php echo $this->Form->input('User.id', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="10%"><?php echo $this->Form->input('User.login', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="20%"><?php echo $this->Form->input('User.email', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="12%">&nbsp;</td>
    <td><?php echo $this->Form->input('User.emailcheckcode', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td><?php echo $this->Form->input('User.passwordchangecode', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="5%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
    <td class="actions">
	<?php echo $this->Form->submit(__d('authake', 'filter'), array('div'=>false));?>
    </td>
</tr>
<?php
$i = 0;
foreach ($users as $user):
	$class = '';
	if ($i++ % 2 == 0) {
		$class = 'altrow';
	}

    // check if user account enables
    $exp = $user['User']['expire_account'];

    if ($user['User']['disable'] or ($exp != '0000-00-00' and $this->Time->fromString($exp) < time()))
        $class = " class=\"{$class} disabled\"";
    else
        $class = " class=\"{$class}\"";
        
?>
	<tr<?php echo $class;?>>
        <td>
            <?php echo $user['User']['id']; ?>
        </td>
		<td>
			<?php echo $this->Html->link($user['User']['login'], array('action'=>'view', $user['User']['id'])); ?>&nbsp;
		</td>
		<td>
			<?php $email = $user['User']['email']; echo "<a href=\"mailto:{$email}\">{$email}</a>"; ?>&nbsp;
		</td>
        <td>
            <?php //pr($user['Group']);
            $gr = (count($user['Group'])) ? array() : array(__d('authake', 'Guest'));     // Specify Guest group if lonely group
            foreach($user['Group'] as $k=>$group)
                $gr[] = $this->Html->link(($group['name']), array('controller'=>'groups', 'action'=>'view', $group['id']));
            
            echo implode('<br/>', $gr); ?>&nbsp;
        </td>
		<td>
            <?php
                    if ($user['User']['emailcheckcode'] != '')
                        echo $this->Html->image("/authake/img/icons/error.png", array('title' => __d('authake', 'Needed')));

                    ?>&nbsp;
		</td>
		<td>
			<?php
                    if ($user['User']['passwordchangecode'] != '')
                        echo $this->Html->image("/authake/img/icons/error.png", array('title' => __d('authake', 'Requested')));
                    ?>&nbsp;
		</td>
        <td>
            <?php echo $this->Time->format('d/m/Y', $user['User']['created']); ?>&nbsp;
        </td>
        <td>
    <?php
        if ($user['User']['disable']) echo $this->Htmlbis->image("/authake/img/icons/lock_delete.png", array('title' => __d('authake', 'Account disabled')));

        $exp = $user['User']['expire_account'];
        if ($exp != '0000-00-00' and $this->Time->fromString($exp) < time()) echo $this->Htmlbis->image("/authake/img/icons/clock_delete.png", array('title' => __d('authake', 'Account expired')));
    ?>&nbsp;
        </td>
		<td class="actions">
            <?php echo $this->Htmlbis->iconlink('information', __d('authake', 'View'), array('action'=>'view', $user['User']['id'])); ?>
			<?php echo $this->Htmlbis->iconlink('pencil', __d('authake', 'Edit'), array('action'=>'edit', $user['User']['id'])); ?>
			<?php echo $this->Htmlbis->iconlink('cross', __d('authake', 'Delete'), array('action'=>'delete', $user['User']['id']), null, sprintf(__d('authake', 'Are you sure you want to delete user \'%s\'?'), $user['User']['login'])); ?>
		&nbsp;
		</td>
	</tr>
<?php endforeach; ?>
</table>
</fieldset>
<?php echo $this->Form->end();?>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__d('authake', 'previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__d('authake', 'next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
</div>
<?php if (!$tableonly) { ?>
<div class="actions">
	<ul>
        <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage groups'), array('controller'=> 'groups', 'action'=>'index')); ?> </li>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('controller'=> 'rules', 'action'=>'index')); ?> </li>
	</ul>
</div>
<?php } ?>
</div>
