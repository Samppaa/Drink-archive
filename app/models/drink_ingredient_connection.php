<?php
  class Drink_Ingredient_Connection extends BaseModel{
      public $ingredient_id, $drink_id, $amount;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
  }
