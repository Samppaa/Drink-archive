<?php

  class UsersController extends BaseController{

    public static function index(){
   	  View::make('suunnitelmat/users.html');
    }
    
    public static function login(){
   	  View::make('suunnitelmat/login.html');
    }
    
    public static function register(){
   	  View::make('suunnitelmat/register.html');
    }
    
    public static function viewUser(){
   	  View::make('suunnitelmat/user.html');
    }
    
    public static function editUser(){
   	  View::make('suunnitelmat/edit_user.html');
    }
  }
