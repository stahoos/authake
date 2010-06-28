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


class UserController extends AuthakeAppController {
  var $name     = 'User';
  var $uses     = array('Authake.User', 'Authake.Rule', 'Authake.Group');
  var $components = array('Email');
  var $layout = 'authake';
  
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
            $this->Session->setFlash(__('Invalid User', true), 'error');
            $this->redirect('/');
        }

        $user = $this->User->read(null, $this->Authake->getUserId());

        if (!empty($this->data)) {
            if ($this->data['User']['password1'] != '') { // password changed
                if ($this->data['User']['password1'] != $this->data['User']['password2']) {
                    $this->Session->setFlash(__('The two passwords do not match!', true), 'error');
                } else {
                    $user['User']['password'] = md5($this->data['User']['password1']);
                    $this->Session->setFlash(__('Password changed!', true), 'success');
                }
            }
            $state = 0;
            if (Configure::read('Authake.passwordVerify') == true && $this->data['User']['email'] != $user['User']['email']) {
                    //Check if that email is not registered by another user
                    if($this->User->find('count', array('conditions'=>array('User.email LIKE'=>$this->data['User']['email'], 'User.id != '.$user['User']['id']))) > 0){
                      $this->Session->setFlash(__('This e-mail has beeen used by another user in the system. Please try with another one!', true), 'error');
                      $this->redirect(array('action'=>'index'));
                    }
                    
                    $user['User']['emailcheckcode'] = md5(rand().time().rand().$user['User']['email']);
                    $user['User']['email'] = $this->data['User']['email'];
                    // send a mail with code to change the pw
                    
                    $this->Email->to = $user['User']['email'];
                    $this->Email->subject = sprintf(__('Your e-mail change request at %s ', true), Configure::read('Authake.service', 'Authentication'));
                    $this->Email->replyTo = Configure::read('Authake.systemReplyTo');
                    $this->Email->from = Configure::read('Authake.systemEmail');
                    $this->Email->sendAs = 'html';
                    $this->Email->charset = 'utf-8';
                    $this->Email->template = 'verify'; 
                    //Set the code into template
                    $this->set('code', $user['User']['emailcheckcode']);
                    $this->set('service', Configure::read('Authake.service'));
                    if ($this->Email->send() ) {
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
                  $this->Session->setFlash(__('Your e-mail has been changed, you should receive a mail with instructions to confirm your new e-mail...', true), 'warning');
                  break;
                
                case 2:
                  $this->Session->setFlash(sprintf(__('Failed to send a email to change your password. Please contact the administrator at %s', true), Configure::read('Authake.systemReplyTo')), 'error');
                  break;
                default:
                  $this->Session->setFlash(__('The User profile has been saved', true), 'success');
              } 
            }
            if(Configure::read('Authake.passwordVerify') == true){
              $this->redirect(array('action'=>'index'));
            } else {
              $this->redirect(array('action'=>'messages'));
            }
        }

        //$this->data = null;
        $this->set(compact('user'));
    }
    
    /**
    * Confirm the email change if needed
    */
    function verify($code = null) {
        if(Configure::read('Authake.registration') == false){
          $this->redirect('/');
        }
        
        if($code != null){
          $this->data['User']['code'] = $code;
        }
        if (!empty($this->data)) {
            $this->User->recursive = 0;
                $user = $this->User->find('first', array('conditions'=>array('emailcheckcode'=>$this->data['User']['code'])));
                
                if (empty($user)) { // bad code or email
                    $this->Session->setFlash(__('Bad identification data!', true), 'error');
                } else {
                    $user['User']['emailcheckcode'] = '';
                    $this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
                    $this->User->save($user);
                    
                    if($this->Authake->getUserId() == null){ //User need to be redirected to login
                      $this->Session->setFlash(__('The confirmation code has been accepted. You may log in now!', true), 'success');
                      $this->redirect(array('action'=>'login'));
                    } else {
                      $this->Session->setFlash(__('The confirmation code has been accepted. Thank you!', true), 'success');
                      $this->redirect(array('action'=>'index'));
                    }
                }
        }
    }
    
    /**
     * User registration
    */
    function register() {
        if(Configure::read('Authake.registration') == false){
          $this->redirect('/');
        }
        if (!empty($this->data)) {
            
            $this->User->recursive = 0;
            $exist = $this->User->findByLogin($this->data['User']['login']);
            if (!empty($exist)) {
                $this->Session->setFlash(__('This login is already used!', true), 'error');
                return;
            }
            
            $exist = $this->User->findByEmail($this->data['User']['email']);
            if (!empty($exist)) {
                $this->Session->setFlash(__('This email is already registred!', true), 'error');
                return;
            }

            $pwd = $this->__makePassword($this->data['User']['password1'], $this->data['User']['password2']);
            if (!$pwd) return;  // password is invalid...
            $this->data['User']['password'] = $pwd;
            
            $this->data['User']['emailcheckcode'] = md5(time()*rand());
            $this->User->create();
            //add default group if there is such thing
            if(Configure::read('Authake.defaultGroup') != null && Configure::read('Authake.defaultGroup') != false){
              $groups = $this->Group->find('all', array('fields'=>array('Group.id'), 'conditions'=>array('Group.id'=>Configure::read('Authake.defaultGroup'))));
              foreach($groups as $group){
                $this->data['Group']['Group'][] = $group['Group']['id'];
              }
            }
            //
            if ($this->User->save($this->data)) {
            
                // send a mail to finish the registration
                $this->Email->to = $this->data['User']['email'];
                $this->Email->subject = sprintf(__('Your registration confirmation at %s ', true), Configure::read('Authake.service', 'Authentication'));
                $this->Email->replyTo = Configure::read('Authake.systemReplyTo');
                $this->Email->from = Configure::read('Authake.systemEmail');
                $this->Email->sendAs = 'html';
                $this->Email->charset = 'utf-8';
                $this->Email->template = 'register'; 
                //Set the code into template
                $this->set('code', $this->data['User']['emailcheckcode']);
                $this->set('service', Configure::read('Authake.service'));
                
                if ($this->Email->send()) {
                    $this->Session->setFlash(__('You will receive an email with a code in order to finish the registration...', true));
                } else {
                    $this->Session->setFlash(sprintf(__('Failed to send the confirmation email. Please contact the administrator at %s', true), Configure::read('Authake.systemReplyTo')), 'error');
                }
                $this->redirect('/login');
            } else {
                $this->Session->setFlash(__('The registration failed!', true), 'error');
            }
        }
    }
    
    /**
     * Function which allow user to change his password if he request it
    */
    function pass($code = null){
      if($this->Authake->getUserId() > 0){
        $this->Session->setFlash(__('You are already logged in. Change your password in your profile!', true), 'warning');
        $this->redirect(array('action'=>'index'));
      }
      $this->User->recursive = 0;
      if (!empty($this->data)) {
        $user = $this->User->find('first', array('conditions'=>array('passwordchangecode'=>$this->data['User']['passwordchangecode'])));
        if (!empty($user)) {
          $pwd = $this->__makePassword($this->data['User']['password1'], $this->data['User']['password2']);
          if (!$pwd) return;  // password is invalid...
          
          $user['User']['password'] = $pwd;
          $user['User']['passwordchangecode'] = '';
          $this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
          if($this->User->save($user)){
            //
            $this->Session->setFlash(__('Your password has been changed!. You may log in now!', true), 'success');
            $this->redirect(array('action'=>'login'));
          } else {
            $this->Session->setFlash(__('Error while saving your password!', true), 'error');
          }
        }
      }
      if($code != null){
        $this->data['User']['passwordchangecode'] = $code;
      }
    }
    
    /**
     * Login functionality
   */
    function login(){
        if ($this->Authake->isLogged()) {
            $this->Session->setFlash(__('You are already logged in!', true), 'info');
            $this->redirect('/');
        }
        
        if (!empty($this->data) ) {
            $login  = $this->data['User']['login'];
            $password = $this->data['User']['password'];
            
            $user = $this->User->findByLogin($login);
            
            if (empty($user)) {
                $this->Session->setFlash(__('Invalid login or password!', true), 'error');
                return;
            }
            
            // check for locked account
            if ($user['User']['id'] != 1 and $user['User']['disable']) {
                $this->Session->setFlash(__('Your account is disabled!', true), 'error');
                $this->redirect('/');
            }

            // check for expired account
            $exp = $user['User']['expire_account'];
            if ($user['User']['id'] != 1 and $exp != '0000-00-00' and $exp != null and strtotime($exp) < time()) {
                $this->Session->setFlash(__('Your account has expired!', true), 'error');
                $this->redirect('/');
            }
            
            // check for not confirmed email
            if ($user['User']['emailcheckcode'] != '') {
                $this->Session->setFlash(__('You registration has not been confirmed!', true), 'warning');
                $this->redirect(array('action'=>'verify'));
            }
            
            $userdata = $this->User->getLoginData($login, $password);
            
            if (empty($userdata)) {
                $this->Session->setFlash(__('Invalid login or password!', true), 'error');
                return;
            } else {
                if($user['User']['passwordchangecode'] != ''){
                  //clear password change code (if there is any)
                  $this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
                  $user['User']['passwordchangecode'] = '';
                  $this->User->save($user);
                }
                
                
                $next = $this->Authake->getPreviousUrl();
                $this->Authake->login($userdata['User']);
                $this->Session->setFlash(__('You are logged in as ', true).$userdata['User']['login'], 'success');
                $this->redirect($next !== null ? $next : '/');
            }
        }
    }
    
    function lost_password() {
        if(Configure::read('Authake.registration') == false){
          $this->redirect('/');
        }
        $this->User->recursive = 0;
        
        if (!empty($this->data)) {
            $loginoremail = $this->data['User']['loginoremail'];
            if ($loginoremail) {
                $user = $this->User->findByLogin($loginoremail);
            }
            if (empty($user)) {
                $user = $this->User->findByEmail($loginoremail);
            }
            if (!empty($user)) { // ok, login or email is ok
                //Prevent sending more than 11 e-mail
                if($user['User']['passwordchangecode'] != ''){
                  $this->Session->setFlash(__("You already requested password change. Please check your e-mail and use the code which we've sent", true), 'error');
                  $this->redirect(array('action'=>'lost_password'));
                }
                $md5 = $user['User']['passwordchangecode'] = md5(time()*rand().$user['User']['email']);
                
                $this->User->unbindModel(array('hasAndBelongsToMany'=>array('Group')), false);
                if ($this->User->save($user)) {
                    // send a mail with code to change the pw
                    $this->Email->to = $user['User']['email'];
                    $this->Email->subject = sprintf(__('Your password change request at %s ', true), Configure::read('Authake.service', 'Authentication'));
                    $this->Email->replyTo = Configure::read('Authake.systemReplyTo');
                    $this->Email->from = Configure::read('Authake.systemEmail');
                    $this->Email->sendAs = 'html';
                    $this->Email->charset = 'utf-8';
                    $this->Email->template = 'lost_password'; 
                    //Set the code into template
                    $this->set('code', $user['User']['passwordchangecode']);
                    $this->set('service', Configure::read('Authake.service'));
                     
                    if ($this->Email->send() ) {
                        $this->Session->setFlash(__('If data provided is correct, you should receive a mail with instructions to change your password...', true), 'warning');
                    } else {
                        $this->Session->setFlash(sprintf(__('Failed to send a email to change your password. Please contact the administrator at %s', true), Configure::read('Authake.systemReplyTo')), 'error');
                    }
                } else {
                    $this->Session->setFlash(sprintf(__('Failed to change your password. Please contact the administrator at %s', true), Configure::read('Authake.systemReplyTo')), 'error');
                }
            } else {
                $this->Session->setFlash(__('If data provided is correct, you should receive a mail with instructions to change your password...', true), 'warning');
            }
            $this->redirect(array('action'=>'lost_password'));
        }
    }

    function logout()
    {
        if ($this->Authake->isLogged()) {
            $this->Authake->logout();
            $this->Session->setFlash(__('You are logged out!', true), 'info');
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