Authake is (another) solution to manage users and groups and their rights in a CakePHP platform, as well as their registration, email confirmation and password changing requests. It’s composed by a component, a plugin, and a helper.

For install instructions and feedback, please go to Authake home page: http://conseil-recherche-innovation.net/authake

Hints:

//add this to your config/database.php to make authake work.
The idea behind this is that it would be possible to have 1 Authake instalation which handle multiple apps.

    var $authake = array(
        'driver' => 'mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => '', //username for the db
        'password' => '',  //password for the db
        'database' => 'authake', //or any other where you have imported the authake.sql file
        'prefix' => '',
    );