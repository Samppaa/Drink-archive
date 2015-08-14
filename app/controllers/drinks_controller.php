<?php
require 'app/models/drink.php';
  class DrinksController extends BaseController{

    public static function index(){
        $drinks = Drink::all();
        //View::make('suunnitelmat/drinks.html');
        View::make('drink/index.html', array('drinks' => $drinks));
    }
    
    public static function edit($id) {
        if(self::check_logged_in(true))
        {
            $drink = Drink::find($id);
            View::make('drink/new.html', array('editing' => true, 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients)));
        }
    }
    
    private static function createDrinkFromParams($params)
    {
        $ingredients = array();
        for($i = 0; $i < count($params['ingredient']); $i++)
        {
            $ingredientName = $params['ingredient'][$i];
            $ingredientAmount = $params['ingredientAmount'][$i];
            $ingredient = new Ingredient(array(
                  'name' =>$ingredientName,
                  'amount' =>$ingredientAmount));
            $ingredients[] = $ingredient;
        }
        
        $attributes = array(
            'name' => $params['name'],
            'description' => $params['description'],
            'author' => self::get_user_logged_in(),
            'time_added' => date("Y-m-d"),
            'type' => $params['type'],
            'waiting_acceptance' => 1,
            'ingredients' => $ingredients,
            'amounts' => $params['ingredientAmount']
        );
        
        $drink = new Drink($attributes);
        return $drink;
    }
    
    public static function update($id) {
        if(self::check_logged_in(true)) {
            $params = $_POST;
            $drink = self::createDrinkFromParams($params);
            $drink->id = $id;
            if(count($drink->errors()) == 0) {
                $drink->update();
                Redirect::to('/drink/' . $drink->id, array('message' => "Juoman muokkaus onnistui!"));
            }
            else
            {
                View::make('drink/new.html', array('errors' => $drink->errors(), 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients), 'editing' => true));
            }
        }
    }
    
    public static function destroy($id) {
        if(self::check_logged_in(true))
        {
            $drink = new Drink(array('id' => $id));
            $drink->destroy($id);
            Redirect::to('/drinks', array('message' => 'Juoma on poistettu onnistuneesti!'));
        }
    }
    
    
    public static function store()
    {
        if(self::check_logged_in(true)) {
            $params = $_POST;
            $drink = self::createDrinkFromParams($params);

            if(count($drink->errors()) == 0) {
                $drink->save();
                Redirect::to('/drinks');
            }
            else
            {
                View::make('drink/new.html', array('errors' => $drink->errors(), 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients)));
            }
        }
    }
    
    public static function show($id) {
        $drink = Drink::find($id);
        View::make('drink/view.html', array('drink' => $drink));
    }
    
    public static function add_drink(){
        if(self::check_logged_in(true))
        {
            View::make('drink/new.html', array('editing' => false));
        }
    }
    
    public static function view_drink(){
        View::make('suunnitelmat/drink.html');
    }
  }
