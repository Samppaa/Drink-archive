<?php
  class Drink extends BaseModel{
      public $id, $name, $description, $author, $time_added, $type, $waiting_acceptance, $ingredients, $amounts;
      
      public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_description', 'validate_type', 'validate_ingredients');
      }
      
      public function validate_ingredients() {
          $errors = array();
          foreach ($this->ingredients as $ingredient)
          {
              $errors = array_merge($errors, $ingredient->errors());
          }
          return array_unique($errors);
      }
      
      public function validate_name() {
          $errors = array();
          
          if(empty($this->name)) {
              $errors[] = 'Drink name can\'t be empty!';
          }
          
          if(!$this->validate_string_length_less_than($this->name, 3)) {
              $errors[] = 'Drink name has to be at least 3 characters!';
          }
          
          if(!$this->validate_string_length_greater_than($this->name, 25)) {
              $errors[] = 'Drink name can\'t be longer than 25 characters';
          }
          
          return $errors;
      }
      
      public function validate_description() {
          $errors = array();
          if(!$this->validate_string_length_greater_than($this->description, 255)) {
              $errors[] = 'The maximum length of description is 255 characters';
          }
          
          return $errors;
      }
      
      public function validate_type() {
          $errors = array();
          if(empty($this->type)) {
              $errors[] = 'The type of drink can\'t be empty';
          }
          return $errors;
      }
      
      private static function newDrinkFromRow($row)
      {
          $drink = new Drink(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'author' => User::find($row['author']),
                'time_added' => $row['time_added'],
                'type' => $row['type'],
                'waiting_acceptance' => $row['waiting_acceptance'],
                'ingredients' => Ingredient::findByDrinkId($row['id'])));
          return $drink;
      }
      
      private function destroyIngredients() {
          $query = DB::connection()->prepare('DELETE FROM Drink_Ingredients WHERE drink_id=:drink_id');
          $query->execute(array('drink_id' => $this->id));
      }
      
      public function destroy($id) {
          $this->destroyIngredients();
          $query = DB::connection()->prepare('DELETE FROM Drinks WHERE id=:id');
          $query->execute(array('id' => $this->id));
      }
      
      public static function all() {
          $query = DB::connection()->prepare('SELECT * FROM Drinks');
          $query->execute();
          $rows = $query->fetchAll();
          $drinks = array();
          
          foreach($rows as $row){
              $drinks[] = Drink::newDrinkFromRow($row);
          }
          
         return $drinks; 
      }
      
      public static function find($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Drinks WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          
          if($row) {
              $drink = Drink::newDrinkFromRow($row);
              return $drink;
          }
      }
      
      public function update() {
          $query = DB::connection()->prepare('UPDATE Drinks SET name=:name, description=:description, author=:author, time_added=:time_added, type=:type, waiting_acceptance=:waiting_acceptance WHERE id=:id');
          $query->execute(array('name' => $this->name, 'description' => $this->description, 'author' => $this->author->id, 'time_added' => $this->time_added, 'type' => $this->type, 'waiting_acceptance' =>$this->waiting_acceptance, 'id' => $this->id));
          $this->updateIngredients();
      }
      
      private function updateIngredients()
      {
          $this->destroyIngredients();
          $this->saveIngredients();
      }
      
      private function saveIngredients()
      {
          for($i = 0; $i < count($this->ingredients); $i++)
          {        
              $ingredient = $this->ingredients[$i];
              $ingredientAmount = $this->amounts[$i];
              $ingredient->save();
              
              // Tallennetaan juoman ja ainesosien yhteydet
              $ingredientConnection = new Drink_Ingredient_Connection(array(
                  'ingredient_id' => $ingredient->id,
                  'drink_id' => $this->id,
                  'amount' => $ingredientAmount ));
              $ingredientConnection->save();
          }
      }
      
      public function save() {
          // Author ID huom
          $query = DB::connection()->prepare('INSERT INTO Drinks (name, description, author, time_added, type, waiting_acceptance) VALUES (:name, :description, :author, :time_added, :type, :waiting_acceptance) RETURNING id');
          $query->execute(array('name' => $this->name, 'description' => $this->description, 'author' => $this->author->id, 'time_added' => $this->time_added, 'type' => $this->type, 'waiting_acceptance' =>$this->waiting_acceptance));
          $row = $query->fetch();
          $this->id = $row['id'];
          
          $this->saveIngredients();
      }
  }
