<?php
/**
 * Tämä luokka on malli käyttäjälle, ja sisältää kaikki käyttäjän käsittelyyn liittyvät metodit kuten poiston, muokkauksen ja lisäyksen.
 * Luokka sisältää myös käyttäjän authentikoinnin kirjautumista varten.
 * @author Samuli Lehtonen
 */
  class User extends BaseModel{
      public $id, $name, $password, $password2, $type, $editing;

      public function __construct($attributes){
        parent::__construct($attributes);
        $editing = false;
        $this->validators = array('validate_name', 'validate_password');
      }
      
     /**
       * Asettaa muokkaustilan päälle. Tätä käytetään validoinnista, jotta saman niminen käyttäjä voidaan päivittää kantaan
       * Tämä tulee tehdä aina ennen muokkausta
       * @param $editing
       */
      public function setEditing($editing) {
          $this->editing = $editing;
      }
      
      /**
       * Validointi metodit tallennusta varten
       */
      public function validate_name() {
          $errors = array();
          
          if(!$this->validate_string_length_greater_than($this->name, 14)) {
              $errors[] = 'Username can\'t be longer than 14 characters';
          }
          
          if(!$this->validate_string_length_less_than($this->name, 3)) {
              $errors[] = 'Username has to be 3-14 characters long!';
          }

          $user = User::findByName($this->name);
          if($user && !$this->editing) {
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
      
      public function hasRightToAddDrinksDirectly() {
          if($this->type > 0) {
              return true;
          }
          return false;
      }
      
      /**
       * Luo käyttäjän tietokannasta haetun rivin perusteella
       * @param $row
       * @returns User
       */
      private static function createUserFromRow($row)
      {
          $user = new User(array(
                  'id' => $row['id'],
                  'name' => $row['name'],
                  'password' => $row['password'],
                  'type' => $row['type'] ));
          return $user;
      }
      
      /**
       * Tunnistaa käyttäjän eli tarkistaa onko salasana ja nimi oikein
       * @param $name
       * @param $password
       * @returns User
       */
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
      
      /**
       * Tallentaa käyttäjän tietokantaan
       */
      public function save() {
          $query = DB::connection()->prepare('INSERT INTO Users (name, password, type) VALUES (:name, :password, :type) RETURNING id');
          $query->execute(array('name' => $this->name, 'password' => $this->password, 'type' => $this->type));
          $row = $query->fetch();
          $this->id = $row['id'];
      }
      
      /**
       * Hakee kaikki käyttäjät tietokannasta
       * @returns Users
       */
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
      
      /**
       * Hakee käyttäjän tietokannasta id:n perusteella
       * @param $id
       */
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
      
      /**
       * Päivittää käyttäjän tietokantaan
       */
      public function update() {
          $query = DB::connection()->prepare('UPDATE Users SET name=:name, password=:password, type=:type WHERE id=:id');
          $query->execute(array('name' => $this->name, 'password' => $this->password, 'id' => $this->id, 'type' => $this->type));
      }
      
      /**
       * Poistaa kaikki käyttäjän tekemät juomat
       */
      private function deleteUserDrinks() {
          $query = DB::connection()->prepare('SELECT * FROM Drinks WHERE author=:author');
          $query->execute(array('author' => $this->id));
          $rows = $query->fetchAll();
          foreach ($rows as $row) {
              $drink = new Drink(array('id' => $row['id']));
              $drink->destroy();
          }
      }
      
      /**
       * Poistaa käyttäjän tietokannasta
       */
      public function destroy() {
          $this->deleteUserDrinks();
          $query = DB::connection()->prepare('DELETE FROM Users WHERE id=:id');
          $query->execute(array('id' => $this->id));
      }
      
      /**
       * Etsii käyttäjän nimen perusteella
       * @param $name
       * @return row
       */
      public static function findByName($name)
      {
          $query = DB::connection()->prepare('SELECT * FROM Users WHERE name = :name LIMIT 1');
          $query->execute(array('name' => $name));
          $row = $query->fetch();
          return $row;
      }
  }
