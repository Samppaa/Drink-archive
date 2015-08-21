<?php
require 'app/models/drink.php';
/**
 * Tämä luokka vastaa juomiin liittyvistä toimenpiteistä kuten lisäämisestä, poistamisesta ja muokkaamisesta. Luokka kutsuu juoma mallin metodeja.
 * @author Samuli Lehtonen
 */
  class DrinksController extends BaseController{

    /**
     * Näyttää näkymän jossa on kaikki juomat
     */
    public static function index(){
        $drinks = Drink::all();
        View::make('drink/index.html', array('drinks' => $drinks));
    }
    
    /**
     * Esittää juoman muokkausnäkymän mikäli käyttäjä on kirjautunut sisään.
     * @param $id
     */
    public static function edit($id) {
        if(self::check_logged_in(true))
        {
            $drink = Drink::find($id);
            View::make('drink/new.html', array('editing' => true, 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients)));
        }
    }
    
    /**
     * Luo juoma olion annetuista parametreista
     * @param $params
     * @return Drink
     */
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
    
    /**
     * Päivittää juoman käyttäjän antamilla tiedoilla, kutsutaan POST pyynnöllä.
     * @param $id
     */
    public static function update($id) {
        if(self::check_logged_in(true)) {
            $params = $_POST;
            $drinkOld = Drink::find($id);
            $drink = self::createDrinkFromParams($params);
            $drink->id = $id;
            $drink->waiting_acceptance = $drinkOld->waiting_acceptance;
            $drink->setEditing(true);
            if(count($drink->errors()) == 0) {
                $drink->update();
                Redirect::to('/drink/' . $drink->id, array('message' => "Drink edited succesfully!"));
            }
            else
            {
                View::make('drink/new.html', array('errors' => $drink->errors(), 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients), 'editing' => true));
            }
        }
    }
    
    /**
     * Tuhoaa juoman, kutsutaan POST pyynnöllä.
     * @param $id
     */
    public static function destroy($id) {
        if(self::check_logged_in(true))
        {
            $drink = new Drink(array('id' => $id));
            $drink->destroy();
            Redirect::to('/drinks', array('message' => 'Drink removed succesfully!'));
        }
    }
    
    /**
     * Tallentaa juoman tietokantaan, kutsutaan POST pyynnöllä.
     */
    public static function store()
    {
        if(self::check_logged_in(true)) {
            $params = $_POST;
            $drink = self::createDrinkFromParams($params);
            
            if(self::get_user_logged_in()->hasRightToAddDrinksDirectly()) {
                $drink->waiting_acceptance = 0;
            }

            if(count($drink->errors()) == 0) {
                $drink->save();
                if(self::get_user_logged_in()->hasRightToAddDrinksDirectly()) {
                    Redirect::to('/drinks', array('message' => 'Drink added!'));
                }
                else 
                {
                    Redirect::to('/drinks', array('message' => 'Drink added but admin has to accept it before it appears.'));
                }
            }
            else
            {
                View::make('drink/new.html', array('errors' => $drink->errors(), 'attributes' => $drink, 'ingredientsCount' => count($drink->ingredients)));
            }
        }
    }
    
    /**
     * Hyväksyy juoman niin että kaikki käyttäjät näkevät sen
     * @param type $id
     */
    public static function acceptDrink($id) {
        if(self::check_logged_in(false) && self::is_admin()) {
            $drink = Drink::find($id);
            $drink->acceptDrink();
            Redirect::to('/', array('message' => 'Drink accepted! Now all users are able see it.'));
        }
    }
    
    /**
     * Esittää näkymän jossa näkyy juomat tiedot
     * @param $id
     */
    public static function show($id) {
        $drink = Drink::find($id);
        View::make('drink/view.html', array('drink' => $drink));
    }
    
    /**
     * Esittää näkymän jossa voi lisätä juoman mikäli käyttäjä on kirjautunut sisään
     */
    public static function add_drink(){
        if(self::check_logged_in(true))
        {
            View::make('drink/new.html', array('editing' => false));
        }
    }


  }
