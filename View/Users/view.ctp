<div id="authake">
<div class="actions menuheader">
    <ul>
        <li class="icon user"><?php echo $this->Html->link(__d('authake', 'Manage users'), array('action'=>'index'));?></li>
    </ul>
</div>
<div class="users view">
<h2><?php  echo sprintf(__d('authake', 'User %s'), "<u>{$user['User']['login']}</u>"); ?></h2>
<?php if (empty($actions)) { ?>
    <dl><?php $i = 0; $class = ' class="altrow"';?>
<?php if ($user['User']['disable']) { ?>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo $this->Html->image("icons/error.png", array('title' => __d('authake', 'Warning'))); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?php
                    echo "<strong>".__d('authake', 'Account disabled')."</strong>";
            ?>
            &nbsp;
        </dd>
<?php } ?>        
		
        <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Login'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['login']." <em>(Id {$user['User']['id']})</em>"; ?>
			&nbsp;
		</dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'In groups'); ?></dt>
        <dd<?php echo $class;?>>
    <?php
        $gr = array();
        foreach ($user['Group'] as $group) {
            $class = '';
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }

            $gr[] = $this->Html->link($group['name'], array('controller'=> 'groups', 'action'=>'view', $group['id']));
        }
        
        if (empty($gr)) {
            $gr[] = __d('authake', 'No group');
        }
        
        echo implode( '&nbsp;&ndash;&nbsp;', $gr);
?>
            
        </dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php $email = $user['User']['email']; echo "<a href=\"mailto:{$email}\">{$email}</a>"; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Email check code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php $j = $user['User']['emailcheckcode'];
                    echo $j ? $j : printf(__d('authake', 'No email change requested'));
            ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Password change code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php $j = $user['User']['passwordchangecode'];
                    echo $j ? $j : printf(__d('authake', 'No password change requested'));
            ?>
			&nbsp;
		</dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Account expires on'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?php
                    $exp = $user['User']['expire_account'];
                    if ($exp != '0000-00-00')
                        echo $exp;
                    else
                        echo __d('authake', 'Never');
            ?>
            &nbsp;
        </dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Created on'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Time->format('d/m/Y H:i', $this->Time->fromString($user['User']['created'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __d('authake', 'Updated on'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Time->format('d/m/Y H:i', $this->Time->fromString($user['User']['updated'])); ?>
			&nbsp;
		</dd>
    </dl>

<?php } ?>

</div>

<div class="actions">
    <ul>
<?php if (!empty($actions)) { ?>
        <li class="icon user"><?php echo $this->Html->link(__d('authake', 'View user'), array('action'=>'view', $user['User']['id'])); ?></li>
<?php } ?>
        <li class="icon user_edit"><?php echo $this->Html->link(__d('authake', 'Edit user'), array('action'=>'edit', $user['User']['id'])); ?></li>
<?php if (empty($actions)) { ?>
        <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'View allowed & denied actions'), array('action'=>'view', "{$user['User']['id']}/actions")); ?></li>
        <li class="icon cross"><?php echo $this->Html->link(__d('authake', 'Delete user'), array('action'=>'delete', $user['User']['id']), null, sprintf(__d('authake', 'Are you sure you want to delete user \'%s\'?'), $user['User']['login'])); ?></li>
<?php } ?>
    </ul>
</div>

<?php if (!empty($actions)) { ?>

<div class="monitor_rules index">
<h3><?php echo __d('authake', 'Allowed & denied actions');?></h3>
<?php
    foreach($actions as $controller => $ruleslist) {
        echo "<div style=\"float: left; padding: 0 0.7em; margin: 0.5em; border-left: 1px solid #CCC;\"><h4>{$controller}</h4>";
        echo "<ul>";
        foreach($ruleslist as $k => $rule) {
            if ($rule['permission'] == "Allow")
                echo '<li class="icon accept"><p style="color: green">'.$rule['action'];
            else
                echo '<li class="icon delete"><p style="color: red">'.$rule['action'];
            echo '</p></li>';
        
        }
        echo "</ul></div>";
    }

?>
<p style="clear: both"></p>
</div>
    <div class="actions">
        <ul>
            <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('controller'=> 'rules', 'action'=>'index')); ?></li>
            <li class="icon accept"><?php echo $this->Html->link(__d('authake', 'Hide this view'), array('action'=>'view', $user['User']['id'])); ?></li>
        </ul>
    </div>
<?php } ?>

<div class="related">
    <h3><?php echo sprintf(__d('authake', 'Rules applied to user %s'), "{$user['User']['login']}");?></h3>
    <?php if (!empty($rules)):?>
    <p><em><?php echo __d('authake', 'Rules herited from guest group are greyed'); ?></em></p>
    <table class="listing" cellpadding = "0" cellspacing = "0">
    <tr>
        <th><?php echo __d('authake', 'Description'); ?></th>
        <th>&nbsp;</th>
        <th><?php echo __d('authake', 'Action'); ?></th>
        <th class="actions"><?php echo __d('authake', 'Actions');?></th>
    </tr>
    <?php
        $i = 0;
        foreach ($rules as $r):
                $rule = $r['Rule'];
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
            ?>
            <tr<?php echo $class;?><?php
            if ($rule['group_id'] != 0)
                echo " style=\"font-weight: bold;\"";
            else
                echo " style=\"color: #999;\"";
            ?>>
                <td><?php echo $rule['name'];?></td>
                <td><?php echo $this->Htmlbis->iconallowdeny($rule['permission']); ?></td>
                <td><?php
                 echo str_replace(' or ', '<br/>', $rule['action']);
                ?></td>
                <td class="actions">
                    <?php echo $this->Htmlbis->iconlink('information', __d('authake', 'View'), array('controller'=> 'rules', 'action'=>'view', $rule['id'])); ?>
                    <?php echo $this->Htmlbis->iconlink('pencil', __d('authake', 'Edit'), array('controller'=> 'rules', 'action'=>'edit', $rule['id'])); ?>
                </td>
            </tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>

    <div class="actions">
        <ul>
            <li class="icon add"><?php echo $this->Html->link(__d('authake', 'New Rule'), array('controller'=> 'rules', 'action'=>'add')); ?></li>
            <li class="icon lock"><?php echo $this->Html->link(__d('authake', 'Manage rules'), array('controller'=> 'rules', 'action'=>'index')); ?> </li>
            
        </ul>
    </div>
</div>
</div>