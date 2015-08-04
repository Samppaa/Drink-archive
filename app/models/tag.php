<?php
  class Tag extends BaseModel{
      public $id, $word;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      public static function findByDrinkId($id)
      {
          $tagIds = Tag_Drink_Connection::findByDrinkId($id);
          $tags = array();
          foreach ($tagIds as $tagId) {
              $query = DB::connection()->prepare('SELECT * FROM Tags WHERE id = :tag_id LIMIT 1');
              $query->execute(array('tag_id' => $tagId->tag_id));
              $row = $query->fetch();
              if($row) {
                $tags[] = new Tag(array(
                    'id' => $row['id'],
                    'word' => $row['word'] ));
              }
          }
          
          return $tags;
      }
      
      public function save()
      {
          $query = DB::connection()->prepare('INSERT INTO Tags (word) VALUES (:word) RETURNING id');
          $query->execute(array('word' => $this->word));
          $row = $query->fetch();
          $this->id = $row['id'];
      }
  }
