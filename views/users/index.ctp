<div id="authake">
<? if (!$tableonly) { echo $this->element('gotoadminpage'); } ?>
<div class="users index">
<?php if (!$tableonly) { ?>
<h2><?php __('Users');?></h2>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $html->link(__('New User', true), array('action'=>'add')); ?></li>
    </ul>
</div>
<?php } ?>
<p class="paging_count">
<?php
echo $paginator->counter(array(
'format' => __('There are %current% users on this system. Page %page%/%pages%', true)
));
?></p>
<?php echo $form->create('User', array('class'=>'filter'));?>
<table class="listing" cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('login');?></th>
    <th><?php echo $paginator->sort('email');?></th>
    <th><?php echo 'Group';?></th>
	<th><?php echo $paginator->sort('Email check', 'emailcheckcode');?></th>
	<th><?php echo $paginator->sort('Change Pwd','passwordchangecode');?></th>
    <th><?php echo $paginator->sort('created');?></th>
    <th><?php echo $paginator->sort(__('Disabled', true), 'disable');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<tr class="table-filter">
    <td width="3%"><?php echo $form->input('User.id', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="10%"><?php echo $form->input('User.login', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="20%"><?php echo $form->input('User.email', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="12%">&nbsp;</td>
    <td><?php echo $form->input('User.emailcheckcode', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td><?php echo $form->input('User.passwordchangecode', array('div'=>false, 'type'=>'text', 'label'=>false));?></td>
    <td width="5%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
    <td class="actions">
	<?php echo $form->submit(__('filter', true), array('div'=>false));?>
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

    if ($user['User']['disable'] or ($exp != '0000-00-00' and $time->fromString($exp) < time()))
        $class = " class=\"{$class} disabled\"";
    else
        $class = " class=\"{$class}\"";
        
?>
	<tr<?php echo $class;?>>
        <td>
            <?php echo $user['User']['id']; ?>
        </td>
		<td>
			<?php echo $html->link($user['User']['login'], array('action'=>'view', $user['User']['id'])); ?>&nbsp;
		</td>
		<td>
			<?php $email = $user['User']['email']; echo "<a href=\"mailto:{$email}\">{$email}</a>"; ?>&nbsp;
		</td>
        <td>
            <?php //pr($user['Group']);
            $gr = (count($user['Group'])) ? array() : array(__('Guest', true));     // Specify Guest group if lonely group
            foreach($user['Group'] as $k=>$group)
                $gr[] = $html->link(__($group['name'], true), array('controller'=>'groups', 'action'=>'view', $group['id']));
            
            echo implode('<br/>', $gr); ?>&nbsp;
        </td>
		<td>
            <?php
                    if ($user['User']['emailcheckcode'] != '')
                        echo $html->image("/authake/img/icons/error.png", array('title' => __('Needed', true)));

                    ?>&nbsp;
		</td>
		<td>
			<?php
                    if ($user['User']['passwordchangecode'] != '')
                        echo $html->image("/authake/img/icons/error.png", array('title' => __('Requested', true)));
                    ?>&nbsp;
		</td>
        <td>
            <?php echo $time->format('d/m/Y', $user['User']['created']); ?>&nbsp;
        </td>
        <td>
    <?php
        if ($user['User']['disable']) echo $htmlbis->image("/authake/img/icons/lock_delete.png", array('title' => __('Account disabled', true)));

        $exp = $user['User']['expire_account'];
        if ($exp != '0000-00-00' and $time->fromString($exp) < time()) echo $htmlbis->image("/authake/img/icons/clock_delete.png", array('title' => __('Account expired', true)));
    ?>&nbsp;
        </td>
		<td class="actions">
            <?php echo $htmlbis->iconlink('information', __('View', true), array('action'=>'view', $user['User']['id'])); ?>
			<?php echo $htmlbis->iconlink('pencil', __('Edit', true), array('action'=>'edit', $user['User']['id'])); ?>
			<?php echo $htmlbis->iconlink('cross', __('Delete', true), array('action'=>'delete', $user['User']['id']), null, sprintf(__('Are you sure you want to delete user \'%s\'?', true), $user['User']['login'])); ?>
		&nbsp;
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $form->end();?>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php if (!$tableonly) { ?>
<div class="actions">
	<ul>
		<li class="icon group"><?php echo $html->link(__('Manage groups', true), array('controller'=>'groups', 'action'=>'index')); ?> </li>
        <li class="icon lock"><?php echo $html->link(__('Manage rules', true), array('controller'=> 'rules', 'action'=>'index')); ?> </li>
	</ul>
</div>
<?php } ?>
</div>