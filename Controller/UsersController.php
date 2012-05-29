<?php
/*
    This file is part of Authake.

    Author: Jérôme Combaz (jakecake/velay.greta.fr)
    Contributors:

    Authake is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Authake is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

App::uses('CakeEmail', 'Network/Email');

class UsersController extends AuthakeAppController {
    var $uses     = array(
    		'Authake.User', 
    		'Authake.Rule',
    		'Authake.Group'
    		);
  var $components = array(
  		'Authake.Filter',
  		'Email'
  		);
 // var $layout = 'authake';
  
  
  /**
   * copied from user controller
   */
  
  /**
   * User registration
   */
  function register() {
  	if(Configure::read('Authake.registration') == false){
  		$this->redirect('/');
  	}
  	if (!empty($this->request->data)) {
  
  		$this->User->recursive = 0;
  		/* If settings say we should use only email info instead of username/email, skip this */
  		if(Configure::read('Authake.useEmailAsUsername') == false){
  			$exist = $this->User->findByLogin($this->request->data['User']['login']);
  			if (!empty($exist)) {
  				$this->Session->setFlash(__d('authake', 'This login is already used!'), 'error', array('plugin' => 'Authake'));
  				return;
  			}
  
  			$exist = $this->User->findByEmail($this->request->data['User']['email']);
  			if (!empty($exist)) {
  				$this->Session->setFlash(__d('authake', 'This email is already registred!'), 'error', array('plugin' => 'Authake'));
  				return;
  			}
  
  			$pwd = $this->__makePassword($this->request->data['User']['password1'], $this->request->data['User']['password2']);
  			if (!$pwd) return;  // password is invalid...
  			$this->request->data['User']['password'] = $pwd;
  
  			$this->request->data['User']['emailcheckcode'] = md5(time()*rand());
  			$this->User->create();
  			//add default group if there is such thing
  			if(Configure::read('Authake.defaultGroup') != null && Configure::read('Authake.defaultGroup') != false){
  				$groups = $this->Group->find('all', array('fields'=>array('Group.id'), 'conditions'=>array('Group.id'=>Configure::read('Authake.defaultGroup'))));
  				foreach($groups as $group){
  					$this->request->data['Group']['Group'][] = $group['Group']['id'];
  				}
  			}
  			//
  			if ($this->User->save($this->request->data)) {
  
  				// send a mail to finish the registration
  				$email = new CakeEmail();
  				$email->to($this->request->data['User']['email']);
  				$email->subject(sprintf(__d('authake', 'Your registration confirmation at %s '), Configure::read('Authake.service', 'Authentication')));
  
  				$email->viewVars(array('service' => Configure::read('Authake.service'), 'code'=> $this->request->data['User']['emailcheckcode']));
  				$email->replyTo(Configure::read('Authake.systemReplyTo'));
  				$email->from(Configure::read('Authake.systemEmail'));
  				$email->emailFormat('html');
  				//$this->Email->charset = 'utf-8';
  				$email->template('Authake.register');
  				//Set the code into template
  				//$this->set('code', $this->request->data['User']['emailcheckcode']);
  				//$this->set('service', Configure::read('Authake.service'));
  
  				if ($email->send()) {
  					$this->Session->setFlash(__d('authake', 'You will receive an email with a code in order to finish the registration...'), 'info', array('plugin' => 'Authake'));
  				} else {
  					$this->Session->setFlash(sprintf(__d('authake', 'Failed to send the confirmation email. Please contact the administrator at %s'), Configure::read('Authake.systemReplyTo')), 'error', array('plugin' => 'Authake'));
  				}
  				$this->redirect(array('plugin'=>'authake','controller'=>'user','action'=>'login'));
  			} else {
  				$this->Session->setFlash(__d('authake', 'The registration failed!'), 'error', array('plugin' => 'Authake'));
  			}
  		}
  	}
  }
  
  /**
   * Login functionality
   */
  function login(){
  	if ($this->Authake->isLogged()) {
  		$this->Session->setFlash(__d('authake', 'You are already logged in!'), 'info', array('plugin' => 'Authake'));
  		$this->redirect(Configure::read('Authake.loggedAction'));
  	}
  
  	if (!empty($this->request->data) ) {
  		$login  = $this->request->data['User']['login'];
  		$password = $this->request->data['User']['password'];
  
  		if(Configure::read('Authake.useEmailAsUsername') == false){
  			$user = $this->User->findByLogin($login);
  		} else {
  			$user = $this->User->findByEmail($login);
  		}
  
  		if (empty($user)) {
  			$this->Session->setFlash(__d('authake', 'Invalid login or password!'), 'error', array('plugin' => 'Authake'));
  			return;
  		}
  
  		// check for locked account
  		if ($user['User']['id'] != 1 and $user['User']['disable']) {
  			$this->Session->setFlash(__d('authake', 'Your account is disabled!'), 'error', array('plugin' => 'Authake'));
  			$this->redirect('/');
  		}
  
  		// check for expired account
  		$exp = $user['User']['expire_account'];
  		if ($user['User']['id'] != 1 and $exp != '0000-00-00' and $exp != null and strtotime($exp) < time()) {
  			$this->Session->setFlash(__d('authake', 'Your account has expired!'), 'error', array('plugin' => 'Authake'));
  			$this->redirect('/');
  		}
  
  		// check for not confirmed email
  		if ($user['User']['emailcheckcode'] != '') {
  			$this->Session->setFlash(__d('authake', 'You registration has not been confirmed!'), 'warning', array('plugin' => 'Authake'));
  			$this->redirect(array('action'=>'verify'));
  		}
  
  		$userdata = $this->User->getLoginData($login, $password);
  
  		if (empty($userdata)) {
  			$this->Session->setFlash(__d('authake', 'Invalid login or password!'), 'error', array('plugin' => 'Authake'));
  			return;
  		} else {
  			if($user['User']['passwordchangecode'] != ''){
  				//clear password change code (if there is any)
  				$this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
  				$user['User']['passwordchangecode'] = '';
  				$this->User->save($user);
  			}
  
  			$this->Authake->login($userdata['User']);
  			$this->Session->setFlash(__d('authake', 'You are logged in as ').$userdata['User']['login'], 'success' , array('plugin'=>'authake'));
  			if (($next = $this->Authake->getPreviousUrl()) !== null) {
  				$this->redirect($next);
  			} else {
  				$this->redirect(Configure::read('Authake.loggedAction'));
  			}
  		}
  	}
  }
  
  /**
   * Confirm the email change if needed
   */
  function verify($code = null) {
  	if(Configure::read('Authake.registration') == false){
  		$this->redirect('/');
  	}
  
  	if($code != null){
  		$this->request->data['User']['code'] = $code;
  	}
  	if (!empty($this->request->data)) {
  		$this->User->recursive = 0;
  		$user = $this->User->find('first', array('conditions'=>array('emailcheckcode'=>$this->request->data['User']['code'])));
  
  		if (empty($user)) { // bad code or email
  			$this->Session->setFlash(__d('authake', 'Bad identification data!'), 'error', array('plugin' => 'Authake'));
  		} else {
  			$user['User']['emailcheckcode'] = '';
  			$this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
  			$this->User->save($user);
  
  			if($this->Authake->getUserId() == null){ //User need to be redirected to login
  				$this->Session->setFlash(__d('authake', 'The confirmation code has been accepted. You may log in now!'), 'success', array('plugin' => 'Authake'));
  				$this->redirect(array('action'=>'login'));
  			} else {
  				$this->Session->setFlash(__d('authake', 'The confirmation code has been accepted. Thank you!'), 'success', array('plugin' => 'Authake'));
  				$this->redirect(array('action'=>'index'));
  			}
  		}
  	}
  }
  
  /**
   * Function which allow user to change his password if he request it
   */
  function pass($code = null){
  	if($this->Authake->getUserId() > 0){
  		$this->Session->setFlash(__d('authake', 'You are already logged in. Change your password in your profile!'), 'warning', array('plugin' => 'Authake'));
  		$this->redirect(array('action'=>'index'));
  	}
  	$this->User->recursive = 0;
  	if (!empty($this->request->data)) {
  		$user = $this->User->find('first', array('conditions'=>array('passwordchangecode'=>$this->request->data['User']['passwordchangecode'])));
  		if (!empty($user)) {
  			$pwd = $this->__makePassword($this->request->data['User']['password1'], $this->request->data['User']['password2']);
  			if (!$pwd) return;  // password is invalid...
  
  			$user['User']['password'] = $pwd;
  			$user['User']['passwordchangecode'] = '';
  			$this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
  			if($this->User->save($user)){
  				//
  				$this->Session->setFlash(__d('authake', 'Your password has been changed!. You may log in now!'), 'success', array('plugin' => 'Authake'));
  				$this->redirect(array('action'=>'login'));
  			} else {
  				$this->Session->setFlash(__d('authake', 'Error while saving your password!'), 'error', array('plugin' => 'Authake'));
  			}
  		}
  	}
  	if($code != null){
  		$this->request->data['User']['passwordchangecode'] = $code;
  	}
  }
  
  
  
  function lost_password() {
  	if(Configure::read('Authake.registration') == false){
  		$this->redirect('/');
  	}
  	$this->User->recursive = 0;
  
  	if (!empty($this->request->data)) {
  		$loginoremail = $this->request->data['User']['loginoremail'];
  		if ($loginoremail) {
  			$user = $this->User->findByLogin($loginoremail);
  		}
  		if (empty($user)) {
  			$user = $this->User->findByEmail($loginoremail);
  		}
  		if (!empty($user)) { // ok, login or email is ok
  			//Prevent sending more than 11 e-mail
  			if($user['User']['passwordchangecode'] != ''){
  				$this->Session->setFlash(__d('authake', "You already requested password change. Please check your e-mail and use the code which we've sent"), 'error', array('plugin' => 'Authake'));
  				$this->redirect(array('action'=>'lost_password'));
  			}
  			$md5 = $user['User']['passwordchangecode'] = md5(time()*rand().$user['User']['email']);
  
  			$this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
  			if ($this->User->save($user)) {
  				// send a mail with code to change the pw
  				$this->Email->to = $user['User']['email'];
  				$this->Email->subject = sprintf(__d('authake', 'Your password change request at %s '), Configure::read('Authake.service', 'Authentication'));
  				$this->Email->replyTo = Configure::read('Authake.systemReplyTo');
  				$this->Email->from = Configure::read('Authake.systemEmail');
  				$this->Email->sendAs = 'html';
  				$this->Email->charset = 'utf-8';
  				$this->Email->template = 'Authake.lost_password';
  				//Set the code into template
  				$this->set('code', $user['User']['passwordchangecode']);
  				$this->set('service', Configure::read('Authake.service'));
  
  				if ($this->Email->send() ) {
  					$this->Session->setFlash(__d('authake', 'If data provided is correct, you should receive a mail with instructions to change your password...'), 'warning', array('plugin' => 'Authake'));
  				} else {
  					$this->Session->setFlash(sprintf(__d('authake', 'Failed to send a email to change your password. Please contact the administrator at %s'), Configure::read('Authake.systemReplyTo')), 'error', array('plugin' => 'Authake'));
  				}
  			} else {
  				$this->Session->setFlash(sprintf(__d('authake', 'Failed to change your password. Please contact the administrator at %s'), Configure::read('Authake.systemReplyTo')), 'error', array('plugin' => 'Authake'));
  			}
  		} else {
  			$this->Session->setFlash(__d('authake', 'If data provided is correct, you should receive a mail with instructions to change your password...'), 'warning', array('plugin' => 'Authake'));
  		}
  		$this->redirect(array('action'=>'lost_password'));
  	}
  }
  
  function logout()
  {
  	if ($this->Authake->isLogged()) {
  		$this->Authake->logout();
  		$this->Session->setFlash(__d('authake', 'You are logged out!'), 'info', array('plugin' => 'Authake'));
  	}
  	$this->redirect('/');
  }
  
  /*
   * end
   */
  var $paginate = array(
                        'limit' => 10,
                        'order' => array(
                                         'User.login' => 'asc'
                                        )
                       );
  
  //var $scaffold;
    
        
    function index($tableonly = false) {
        $this->User->recursive = 1;
        $filter = $this->Filter->process($this);
        $this->set('users', $this->paginate(null, $filter));
        $this->set('tableonly', $tableonly);
    }

    function view($id = null, $viewactions = null) {
        $this->User->recursive = 1;     // to select user, groups and rules
        if (!$id) {
            $this->Session->setFlash(__d('authake', 'Invalid User'));
            $this->redirect(array('action'=>'index'));
        }
        
        $this->set('user', $this->User->read(null, $id));
        $groups = $this->User->getGroups($id);
        $this->set('rules', $this->Rule->getRules($groups));
        if ($viewactions === 'actions') {
            $this->set('actions', $this->Authake->getActionsPermissions($groups));
        }
    }

    function add() {
        if (!empty($this->request->data)) {
            
            // only an admin can make an admin
            if (in_array(1, $this->request->data['Group']['Group']) and !in_array(1, $this->Authake->getGroupIds())) {
                $this->Session->setFlash(__d('authake', 'You cannot add a user in administrators group'), 'warning');
                $this->redirect(array('action'=>'index'));
            }
            
            $p = $this->request->data['User']['password'];
            $this->request->data['User']['password'] = $this->__makePassword($p, $p);
            
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__d('authake', 'The User has been saved'), 'success');
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__d('authake', 'The User could not be saved. Please, try again.'), 'error');
            }
        }
        
        $this->request->data['User']['password'] = '';        
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
    }

    function edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__d('authake', 'Invalid User'));
            $this->redirect(array('action'=>'index'));
        }
        
        $user = $this->User->read(null, $id);
        
        // check if user allow to edit (only an admin can edit an admin)
        $gr = Set::extract($user, "Group.{n}.id");
        
        if (in_array(1, $gr) and !in_array(1, $this->Authake->getGroupIds())) {
            $this->Session->setFlash(__d('authake', 'You cannot edit a user in administrators group'), 'warning');
            $this->redirect(array('action'=>'index'));
        }
        
        if (!empty($this->request->data)) {
            // only Admin (id 1) can modify its profile (for security reasons)
            if ($id == 1 && $this->Authake->getUserId() != 1) {
                $this->Session->setFlash(__d('authake', 'Only the admin can change its profile!'), 'warning');
                $this->redirect(array('action'=>'index'));
            }
            
            // only an admin can make an admin
            if($this->request->data['Group']['Group'] == ''){
              $this->request->data['Group']['Group'] = array();
            }
            
            if (
                isset($this->request->data['Group']['Group']) and
                in_array(1, $this->request->data['Group']['Group']) and
                !in_array(1, $this->Authake->getGroupIds())
                ) {
                $this->Session->setFlash(__d('authake', 'You cannot add a user in administrators group'), 'warning');
                $this->redirect(array('action'=>'index'));
            }

            // check if pwd changed
            if ($p = $this->request->data['User']['password'])
                $this->request->data['User']['password'] = $this->__makePassword($p, $p);
            else
                unset($this->request->data['User']['password']);

            if (empty($this->request->data['Group']))
                $this->request->data['Group']['Group'] = array();      // delete user-group relation if selection empty

            unset($this->request->data['User']['login']);    // never change the login

            // save user
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__d('authake', 'The User has been saved'), 'success');
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__d('authake', 'The User could not be saved. Please, try again.'), 'error');
            }
        }
        
    // show edit form
        $this->request->data = $user;
        $this->request->data['User']['password'] = '';

        // find groups
        $groups = $this->User->Group->find('list');
        unset($groups[0]);  // remove group 0 (everybody)
        $this->set(compact('groups'));
    }

    function delete($id = null) {
        // check if user in admins group
        $user = $this->User->read(null, $id);
        
        if (!$id || $id == 1) {
            $this->Session->setFlash(__d('authake', 'Invalid id for User'), 'error');
            $this->redirect(array('action'=>'index'));
        }
        
        // check if user allow to edit (only an admin can edit an admin)
        $gr = Set::extract($user, "Group.{n}.id");
        
        if (in_array(1, $gr) and !in_array(1, $this->Authake->getGroupIds())) {
            $this->Session->setFlash(__d('authake', 'You cannot delete a user in administrators group'), 'warning');
            $this->redirect(array('action'=>'index'));
        }

        if ($this->User->delete($id)) {
            $this->Session->setFlash(__d('authake', 'User deleted'), 'success');
            $this->redirect(array('action'=>'index'));
        }
    }
    
//    function login()
  //  {
    	//$this->redirect("/");
  //  }


}

?>