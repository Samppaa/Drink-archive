<?php
  class Tag_Drink_Connection extends BaseModel{
      public $tag_id, $drink_id;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      public function save()
      {
          $query = DB::connection()->prepare('INSERT INTO Drink_Tags (tag_id, drink_id) VALUES (:tag_id, :drink_id)');
          $query->execute(array('tag_id' => $this->tag_id, 'drink_id' => $this->drink_id));
      }
      
      public static function findByDrinkId($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Drink_Tags WHERE drink_id = :drink_id');
          $query->execute(array('drink_id' => $id));
          $rows = $query->fetchAll();
          $tags = array();
          
          if($rows) {
              foreach ($rows as $row) {
                  $tags[] = new Tag_Drink_Connection(array(
                          'drink_id' => $row['drink_id'],
                          'tag_id' => $row['tag_id']
                          ));
              }
          }
          
          return $tags;
      }
  }
