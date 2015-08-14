<?php

  class UsersController extends BaseController{

    public static function handle_login() {
        $params = $_POST;
        self::handle_login_private($params['username'], $params['password']);
    }
    
    private static function handle_login_private($username, $password) {
        $user = User::authenticate($username, $password);
        
        if(!$user){
            View::make('user/login.html', array('error' => 'Wrong password or username', 'username' => $username));
        }else{
            $_SESSION['user'] = $user->id;
            Redirect::to('/', array('message' => 'Welcome ' . $user->name . '!'));
        }
    }
    
    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'You have logged out succesfully!'));
    }
    
    public static function show($id) {
        $user = User::find($id);
        View::make('user/user.html', array('user' => $user));
    }
      
    public static function index(){
   	  View::make('user/users.html', array('users' => User::all()));
    }
    
    public static function login(){
        if(!self::check_logged_in(false)) {
   	    View::make('user/login.html');
        }
        else {
            Redirect::to("/");
        }
    }
    
    public static function create_user() {
        $params = $_POST;
        $attributes = array(
            'name' => $params['username'],
            'password' => $params['password'],
            'password2' => $params['password2'],
            'type' => 0
        );
        $user = new User($attributes);
        if(count($user->errors()) == 0) {
            $user->save();
            self::handle_login_private($user->name, $user->password);
        }
        else
        {
            View::make('user/register.html', array('errors' => $user->errors(), 'attributes' => $user));
        }
    }
    
    public static function register(){
        if(!self::check_logged_in(false)) {
   	    View::make('user/register.html');
        }
        else
        {
            Redirect::to("/index.php");
        }
    }
    
    public static function viewUser(){
   	  View::make('suunnitelmat/user.html');
    }
    
    public static function editUser(){
   	  View::make('suunnitelmat/edit_user.html');
    }
  }
