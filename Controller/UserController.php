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
class UserController extends AuthakeAppController {
	var $uses     = array('Authake.User', 'Authake.Rule', 'Authake.Group');
	var $components = array('Email');
	//var $layout = 'authake';

	//var $scaffold;

	function denied(){
		// display this view (/app/views/users/denied.ctp) when the user is denied
	}

	function message(){
		// display this view if you want to say something important to your users.
		// For example (your password was changed and you need to receive mail to
			// confirm it.)
		}

		/**
		* User profile
		*/
	function index() {
		if (!$this->Authake->getUserId()) {
			$this->Session->setFlash(__d('authake', 'Invalid User'), 'error', array('plugin' => 'Authake'));
			$this->redirect('/');
		}

		$this->User->recursive = 1;

		$user = $this->User->read(null, $this->Authake->getUserId());

		if (!empty($this->request->data)) {
			if ($this->request->data['User']['password1'] != '') { // password changed
				if ($this->request->data['User']['password1'] != $this->request->data['User']['password2']) {
					$this->Session->setFlash(__d('authake', 'The two passwords do not match!'), 'error', array('plugin' => 'Authake'));
				} else {
					$user['User']['password'] = md5($this->request->data['User']['password1']);
					$this->Session->setFlash(__d('authake', 'Password changed!'), 'success', array('plugin' => 'Authake'));
				}
			}
			$state = 0;
			if (Configure::read('Authake.passwordVerify') == true && $this->request->data['User']['email'] != $user['User']['email']) {
				//Check if that email is not registered by another user
				if($this->User->find('count', array('conditions'=>array('User.email LIKE'=>$this->request->data['User']['email'], 'User.id != '.$user['User']['id']))) > 0){
					$this->Session->setFlash(__d('authake', 'This e-mail has beeen used by another user in the system. Please try with another one!'), 'error', array('plugin' => 'Authake'));
					$this->redirect(array('action'=>'index'));
				}

				$user['User']['emailcheckcode'] = md5(rand().time().rand().$user['User']['email']);
				$user['User']['email'] = $this->request->data['User']['email'];
				// send a mail with code to change the pw
				$email = new CakeEmail();
				$email->to($user['User']['email']);
				$email->subject(sprintf(__d('authake', 'Your e-mail change request at %s '), Configure::read('Authake.service', 'Authentication')));
				$email->replyTo(Configure::read('Authake.systemReplyTo'));
				$email->from(Configure::read('Authake.systemEmail'));
				$email->emailFormat('html');
				//$this->Email->charset = 'utf-8';
				$email->template('Authake.verify');
				//Set the code into template
				$this->set('code', $user['User']['emailcheckcode']);
				$this->set('service', Configure::read('Authake.service'));
				if ($email->send() ) {
					$state = 1;
				} else {
					$state = 2;
				}
			}

			//Unbind HABTM relation for this save
			$this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);

			if($this->User->save($user['User'])){

				switch($state){
					case 1:
					$this->Session->setFlash(__d('authake', 'Your e-mail has been changed, you should receive a mail with instructions to confirm your new e-mail...'), 'warning', array('plugin' => 'Authake'));
					break;

					case 2:
					$this->Session->setFlash(sprintf(__d('authake', 'Failed to send a email to change your password. Please contact the administrator at %s'), Configure::read('Authake.systemReplyTo')), 'error', array('plugin' => 'Authake'));
					break;
					default:
					$this->Session->setFlash(__d('authake', 'The User profile has been saved'), 'success', array('plugin' => 'Authake'));
				}
			}
			if(Configure::read('Authake.passwordVerify') == true){
				$this->redirect(array('action'=>'index'));
			} else {
				$this->redirect(array('action'=>'messages'));
			}
		}

		//$this->request->data = null;
		$this->set(compact('user'));
	}


	function logout()
	{
		if ($this->Authake->isLogged()) {
			$this->Authake->logout();
			$this->Session->setFlash(__d('authake', 'You are logged out!'), 'info', array('plugin' => 'Authake'));
		}
		$this->redirect('/');
	}



function beforeFilter(){

	parent::beforeFilter();

	//Overwriting the authake layout with the default one
	if(Configure::read('Authake.useDefaultLayout') == true){
		$this->layout = 'default';
	}
}
}


?>