<?php

  class Ingredient extends BaseModel {
      // Amount voi olla tyhjä, sillä sitä tarvitaan vain kun haetaan drinkkeihin liittyviä ainesosia
      public $id, $name, $amount;
      public function __construct($attributes) {
          parent::__construct($attributes);
      }
      
      public static function all() {
          $query = DB::connection()->prepare('SELECT * FROM Ingredients');
          $query->execute();
          $rows = $query->fetchAll();
          $ingredients = array();
          
          foreach($rows as $row)
          {
              $ingredients[] = new Ingredient(array(
                  'id' => $row['id'],
                  'name' => $row['name'] ));
          }
          
          return $ingredients;
      }
      
      public function save()
      {
          // Aluksi tulee tarkistaa onko jo olemassa
          $query = DB::connection()->prepare('INSERT INTO Ingredients (name) VALUES (:name) RETURNING id');
          $query->execute(array('name' => $this->name));
          $row = $query->fetch();
          $this->id = $row['id'];
      }
      
      public static function findById($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Ingredients WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          if($row) {
              $ingredient = new Ingredient(array(
                  'id' => $row['id'],
                  'name' => $row['name'] ));
              return $ingredient;
          }
      }
      
      public static function findByDrinkId($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Drink_Ingredients WHERE drink_id = :id');
          $query->execute(array('id' => $id));
          $rows = $query->fetchAll();
          $ingredients = array();
          
          foreach($rows as $row)
          {
              $newIngredient = self::findById($row['ingredient_id']);
              $newIngredient->amount = $row['amount'];
              $ingredients[] = $newIngredient;
          }
          
          return $ingredients;
      }
      
  }
