<?php
/**
 * Tämä luokka kuvaa monesta moneen yhteyttä juoman ja sen ainesosien välillä.
 * @author Samuli Lehtonen
 */
  class Drink_Ingredient_Connection extends BaseModel{
      public $ingredient_id, $drink_id, $amount;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      /**
       * Tallentaa yhteyden ainesosan ja juoman välillä tietokantaan.
       */
      public function save()
      {
          $query = DB::connection()->prepare('INSERT INTO Drink_Ingredients (ingredient_id, drink_id, amount) VALUES (:ingredient_id, :drink_id, :amount)');
          $query->execute(array('ingredient_id' => $this->ingredient_id, 'drink_id' => $this->drink_id, 'amount' => $this->amount));
      }
  }
