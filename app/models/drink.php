<?php
  class Drink extends BaseModel{
      public $id, $name, $description, $author, $time_added, $type, $waiting_acceptance, $ingredients, $amounts, $tags;
      
      public function __construct($attributes){
        parent::__construct($attributes);
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
                'ingredients' => Ingredient::findByDrinkId($row['id']),
                'tags' => Tag::findByDrinkId($row['id'])));
          return $drink;
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
          // Aluksi haetaan juoma normaalisti
          $query = DB::connection()->prepare('SELECT * FROM Drinks WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          
          if($row) {
              $drink = Drink::newDrinkFromRow($row);
              return $drink;
          }
      }
      
      private function saveIngredients()
      {
          for($i = 0; $i < count($this->ingredients); $i++)
          {
              $ingredientName = $this->ingredients[$i];
              $ingredientAmount = $this->amounts[$i];
              
              $ingredient = new Ingredient(array(
                  'name' =>$ingredientName ));
              $ingredient->save();
              
              // Tallennetaan juoman ja ainesosien yhteydet
              $ingredientConnection = new Drink_Ingredient_Connection(array(
                  'ingredient_id' => $ingredient->id,
                  'drink_id' => $this->id,
                  'amount' => $ingredientAmount ));
              $ingredientConnection->save();
          }
      }
      
      private function saveTags()
      {
          foreach ($this->tags as $tagName) {
              $tag = new Tag(array(
                  'word' => $tagName ));
              $tag->save();
              $tagConnection = new Tag_Drink_Connection(array(
                  'tag_id' => $tag->id,
                  'drink_id' => $this->id));
              $tagConnection->save();
          }
      }
      
      public function save() {
          // Author ID huom
          $query = DB::connection()->prepare('INSERT INTO Drinks (name, description, author, time_added, type, waiting_acceptance) VALUES (:name, :description, :author, :time_added, :type, :waiting_acceptance) RETURNING id');
          $query->execute(array('name' => $this->name, 'description' => $this->description, 'author' => $this->author->id, 'time_added' => $this->time_added, 'type' => $this->type, 'waiting_acceptance' =>$this->waiting_acceptance));
          $row = $query->fetch();
          $this->id = $row['id'];
          
          $this->saveIngredients();
          $this->saveTags();
      }
  }
