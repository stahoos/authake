<?php
/*
    This file is part of Authake.

    Author: Mutlu Tevfik Koçak (mtkocak.net)
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

App::uses('User','Authake.Model');

class UserTestCase extends CakeTestCase {
	
	public $fixtures = array('plugin.authake.user');
	public $User;
	
	public function setUp() 
	{
	parent::setUp();
	$this->User = ClassRegistry::init('Authake.User');
	}
	
	public function testObject() {
	         $this->assertTrue(is_object($this->User));
	}
	
	public function testLoginData()
    {
	
	$login='admin'; 
	$password='admin';
	
        $result = $this->User->find('first', array('conditions'=>array('login'=>$login, 'password'=>md5($password)),
'recursive'=>1,));


            if (!empty($result['Group'])) {
                foreach($result['Group'] as $group) {
                    $result['User']['group_ids'][] = $group['id'];
                    $result['User']['group_names'][] = $group['name'];
                }
            }

            
            unset($result['User']['password']); // not useful to save the encrypted password in session

	        $expected = 
	array('User'=> 
				array('id'=> '1',
					   'login'=> 'admin',
						'password' => '21232f297a57a5a743894a0e4a801fc3',
					  	'email'=> 'root',
					 	'emailcheckcode'=>'',
			 			'passwordchangecode'=> '',
			 			'disable'=> false ,
						'expire_account'=> null,
						'created'=>  '0000-00-00 00:00:00',
						'updated' => '2008-02-12 12:19:31',
			 'Group'=> 
				array( 
					0 => 
					array( 'id'=> '1',
					'name'=> 'Administrators' ) ) ));


	        $this->assertEquals($expected, $result);
       
    }
	

   
    }
?>