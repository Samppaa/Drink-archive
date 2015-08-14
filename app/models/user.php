<?php
  class User extends BaseModel{
      public $id, $name, $password, $password2, $type;

      public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_password');
      }
      
      public function validate_name() {
          $errors = array();
          
          if(!$this->validate_string_length_greater_than($this->name, 14)) {
              $errors[] = 'Username can\'t be longer than 14 characters';
          }
          
          if(!$this->validate_string_length_less_than($this->name, 3)) {
              $errors[] = 'Username has to be 3-14 characters long!';
          }

          $user = User::findByName($this->name);
          if($user) {
              $errors[] = 'That username is already taken!';
          }
          return $errors;
      }
      
      public function validate_password() {
          $errors = array();
          
          if($this->password != $this->password2) {
              $errors[] = 'Password doesn\'t match!';
          }
          
          if(!$this->validate_string_length_greater_than($this->password, 16)) {
              $errors[] = 'Password can\'t be longer than 16 characters';
          }
          
          if(!$this->validate_string_length_less_than($this->password, 3)) {
              $errors[] = 'Password has to be 8-16 characters long!';
          }
          
          return $errors;
      }
      
      private static function createUserFromRow($row)
      {
          $user = new User(array(
                  'id' => $row['id'],
                  'name' => $row['name'],
                  'password' => $row['password'],
                  'type' => $row['type'] ));
          return $user;
      }
      
      public static function authenticate($name, $password) {
          $query = DB::connection()->prepare('SELECT * FROM Users WHERE name = :name AND password = :password LIMIT 1');
          $query->execute(array('name' => $name, 'password' => $password));
          $row = $query->fetch();
          if($row){
              return self::createUserFromRow($row); 
          }else{
              return null;
          }
      }
      
      public function save() {
          $query = DB::connection()->prepare('INSERT INTO Users (name, password, type) VALUES (:name, :password, :type) RETURNING id');
          $query->execute(array('name' => $this->name, 'password' => $this->password, 'type' => $this->type));
          $row = $query->fetch();
          $this->id = $row['id'];
      }
      
      public static function all()
      {
         $query = DB::connection()->prepare('SELECT * FROM Users'); 
         $query->execute();
         $rows = $query->fetchAll();
         $users = array();
         foreach($rows as $row) {
             $users[] = self::createUserFromRow($row);
         }
         return $users;
      }
      
      public static function find($id)
      {
          $query = DB::connection()->prepare('SELECT * FROM Users WHERE id = :id LIMIT 1');
          $query->execute(array('id' => $id));
          $row = $query->fetch();
          if($row) {
              return self::createUserFromRow($row);
          }
          
          return null;
      }
      
      public static function findByName($name)
      {
          $query = DB::connection()->prepare('SELECT * FROM Users WHERE name = :name LIMIT 1');
          $query->execute(array('name' => $name));
          $row = $query->fetch();
          return $row;
      }
  }
