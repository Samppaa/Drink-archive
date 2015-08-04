<?php
  class User extends BaseModel{
      public $id, $name, $password, $type;

      public function __construct($attributes){
        parent::__construct($attributes);
      }
      
      public static function find($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Users WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          if($row) {
              $user = new User(array(
                  'id' => $row['id'],
                  'name' => $row['name'],
                  'password' => $row['password'],
                  'type' => $row['type'] ));
              
              return $user;
          }
      }
  }
