<?php
  class Drink_Ingredient_Connection extends BaseModel{
      public $ingredient_id, $drink_id, $amount;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      public function save()
      {
          $query = DB::connection()->prepare('INSERT INTO Drink_Ingredients (ingredient_id, drink_id, amount) VALUES (:ingredient_id, :drink_id, :amount)');
          $query->execute(array('ingredient_id' => $this->ingredient_id, 'drink_id' => $this->drink_id, 'amount' => $this->amount));
      }
  }
