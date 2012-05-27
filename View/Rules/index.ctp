<div id="authake">
<?php if (!$tableonly) { echo $this->element('gotoadminpage'); } ?>
<div class="rules index">
<?php if (!$tableonly) { ?>

<h2><?php echo __d('authake', 'Rules');?></h2>
<div class="actions">
    <ul>
        <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New Rule'), array('action'=>'add')); ?></li>
    </ul>
</div>
<?php } ?>
<table class="listing" cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo __d('authake', 'Description');?></th>
	<th><?php echo __d('authake', 'Group');?></th>
    <th>&nbsp;</th>
	<th><?php echo __d('authake', 'Action');?></th>
	<th class="actions"><?php echo __d('authake', 'Actions');?></th>
    <th><?php echo __d('authake', 'Order');?></th>
</tr>
<?php
$i = 0;
$up = null;
foreach ($rules as $k => $rule):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Htmlbis->link($rule['Rule']['name'], array('action'=>'view', $rule['Rule']['id'])); ?>
		</td>
		<td>
			<?php
             
             $groupname = $rule['Group']['name'];
             
             if ($rule['Group']['id'])
                echo $this->Html->link($groupname, array('controller'=> 'groups', 'action'=>'view', $rule['Group']['id']));
            else
                echo $groupname;
            
            ?>
		</td>
        <td style="text-align: center;">
            <?php
            echo $this->Htmlbis->iconallowdeny($rule['Rule']['permission']);             
             ?>
        </td>
		<td>
			<?php
             echo str_replace(' or ', '<br/>', $rule['Rule']['action']);
              ?>
		</td>
		<td class="actions">
            <?php if ($rule['Rule']['id'] != 1) { ?>      
            <?php echo $this->Htmlbis->iconlink('information', __d('authake', 'View'), array('action'=>'view', $rule['Rule']['id'])); ?>
            <?php echo $this->Htmlbis->iconlink('pencil', __d('authake', 'Edit'), array('action'=>'edit', $rule['Rule']['id'])); ?>
			<?php echo $this->Htmlbis->iconlink('cross', __d('authake', 'Delete'), array('action'=>'delete', $rule['Rule']['id']), null, sprintf(__d('authake', 'Are you sure you want to delete the rule \'%s\'?'), $rule['Rule']['name'])); ?>
            <?php

            if ($up) {
                echo $this->Htmlbis->iconlink('arrow_up', __d('authake', 'Move up'), array('action'=>'up', $rule['Rule']['id'], $up));
            } else {
                echo $this->Htmlbis->iconlink('empty', '', array('action'=>''));
            }
            $up = $rule['Rule']['id'];
              
            $down = $rules[$k+1]['Rule']['id'];
            if ($down>1) {
                echo $this->Htmlbis->iconlink('arrow_down', __d('authake', 'Move down'), array('action'=>'up', $rule['Rule']['id'], $down));
            } else {
                echo $this->Htmlbis->iconlink('empty', '', array('action'=>''));
            }
              
        }
 ?>
		</td>
        <td>
            <?php if (($rule['Rule']['id']) != 1) echo $rule['Rule']['order']; ?>
        </td>
	</tr>
<?php endforeach; ?>
</table>
</div>
</div>
<?php if (!$tableonly) { ?>
<div class="actions">
	<ul>
        <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage users'), array('controller'=> 'users', 'action'=>'index')); ?> </li>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage groups'), array('controller'=> 'groups', 'action'=>'index')); ?> </li>
	</ul>
</div>
<?php } ?>