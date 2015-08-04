<?php
  class Drink extends BaseModel{
      public $id, $name, $description, $author, $time_added, $type, $waiting_acceptance, $ingredients;
      
      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      public static function all() {
          $query = DB::connection()->prepare('SELECT * FROM Drinks');
          $query->execute();
          $rows = $query->fetchAll();
          $drinks = array();
          
          foreach($rows as $row){
              $drinks[] = new Drink(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'author' => User::find($row['author']),
                'time_added' => $row['time_added'],
                'type' => $row['type'],
                'waiting_acceptance' => $row['waiting_acceptance'],
                'ingredients' => Ingredient::findByDrinkId($row['id'])
            ));
          }
          
         return $drinks; 
      }
      
      public function save() {
          // Author ID huom
          $query = DB::connection()->prepare('INSERT INTO Drinks (name, description, author, time_added, type, waiting_acceptance) VALUES (:name, :description, :author, :time_added, :type, :waiting_acceptance) RETURNING id');
          $query->execute(array('name' => $this->name, 'description' => $this->description, 'author' => $this->author->id, 'time_added' => $this->time_added, 'type' => $this->type, 'waiting_acceptance' =>$this->waiting_acceptance));
          $row = $query->fetch();
          $this->id = $row['id'];
          
          // Tallennetaan ainesosat
          foreach ($this->ingredients as $ingredientName) {
              $ingredient = new Ingredient(array(
                  'name' =>$ingredientName ));
              $ingredient->save();
          }
          
          // Tallennetaan ainesosien ja drinkkin yhteydet
          
      }
  }
