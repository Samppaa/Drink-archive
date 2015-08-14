<?php

  class Ingredient extends BaseModel {
      // Amount voi olla tyhjä, sillä sitä tarvitaan vain kun haetaan drinkkeihin liittyviä ainesosia
      public $id, $name, $amount;
      public function __construct($attributes) {
          parent::__construct($attributes);
          $this->validators = array('validate_name', 'validate_amount');
      }
      
      public function validate_name() {
          $errors = array();
          
          if(!$this->validate_string_length_less_than($this->name, 3)) {
              $errors[] = 'Ainesosan nimen tulee olla vähintään 3 merkkiä!';
          }
          
          if(!$this->validate_string_length_greater_than($this->name, 30)) {
              $errors[] = 'Ainesosan nimen tulee olla enintään 30 merkkiä!';
          }
          
          return $errors;
      }
      
      public function validate_amount() {
          $errors = array();
          
          if(!$this->validate_string_length_less_than($this->amount, 1)) {
              $errors[] = 'Ainesosan määrän tulee olla vähintään 1 merkkiä!';
          }
          
          if(!$this->validate_string_length_greater_than($this->amount, 10)) {
              $errors[] = 'Ainesosan määrän tulee olla enintään 30 merkkiä!';
          }
          
          return $errors;
      }
      
      public static function all() {
          $query = DB::connection()->prepare('SELECT * FROM Ingredients');
          $query->execute();
          $rows = $query->fetchAll();
          $ingredients = array();
          
          foreach($rows as $row)
          {
              $ingredients[] = $this->ingredientFromRow($row);
          }
          
          return $ingredients;
      }
      
      private function ingredientFromRow($row) {
          $ingredient = new Ingredient(array(
                  'id' => $row['id'],
                  'name' => $row['name'] ));
          return $ingredient;
      }
      
      
      public function save()
      {
          $ingredientT = self::findByName($this->name);
          
          // Tarkastetaan onko saman niminen jo olemassa, jos on niin otetaan se eikä luoda uutta
          if(!$ingredientT)
          {
            $query = DB::connection()->prepare('INSERT INTO Ingredients (name) VALUES (:name) RETURNING id');
            $query->execute(array('name' => $this->name));
            $row = $query->fetch();
            $this->id = $row['id'];
          }
          else {
            $this->id = $ingredientT->id;
            $this->name = $ingredientT->name;
          }
      }
      
      public static function findById($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Ingredients WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          if($row) {
              return self::ingredientFromRow($row);
          }
          return null;
      }
      
      public static function findByName($name) {
          $query = DB::connection()->prepare('SELECT * FROM Ingredients WHERE name = :name LIMIT 1');
          $query->execute(array('name' => $name));
          $row = $query->fetch();
          if($row) {
              return self::ingredientFromRow($row);
          }
          return null;
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
