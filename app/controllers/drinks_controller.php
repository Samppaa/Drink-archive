<?php
require 'app/models/drink.php';
  class DrinksController extends BaseController{

    public static function index(){
        $drinks = Drink::all();
        //View::make('suunnitelmat/drinks.html');
        View::make('drink/index.html', array('drinks' => $drinks));
    }
    
    public static function store()
    {
        $params = $_POST;
        
        // Author väliaikainen, jatkossa otetaan aktiivisesta käyttäjästä
        
        $drink = new Drink(array(
            'name' => $params['name'],
            'description' => $params['description'],
            'author' => User::find(0),
            'time_added' => date("Y-m-d"),
            'type' => $params['type'],
            'waiting_acceptance' => 1,
            'ingredients' => $params['ingredient']
        ));
        
        $drink->save();
        Redirect::to('/drinks');
    }
    
    public static function add_drink(){
        //View::make('suunnitelmat/add_drink.html'); 
        View::make('drink/new.html');
    }
    
    public static function view_drink(){
        View::make('suunnitelmat/drink.html');
    }
  }
