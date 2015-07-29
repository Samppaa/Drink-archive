<?php

  class DrinksController extends BaseController{

    public static function index(){
        View::make('suunnitelmat/drinks.html');
    }
    
    public static function add_drink(){
        View::make('suunnitelmat/add_drink.html');
    }
    
    public static function view_drink(){
        View::make('suunnitelmat/drink.html');
    }
  }
