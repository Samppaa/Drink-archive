<?php
/**
 * Tämä luokka vastaa käyttäjään liittyvistä toimenpiteistä kuten lisäämisestä, poistamisesta ja muokkaamisesta. Luokka kutsuu käyttäjä mallin metodeja.
 * @author Samuli Lehtonen
 */
  class UsersController extends BaseController{

    /**
     * Käsittelee kirjautumisen
     */
    public static function handle_login() {
        $params = $_POST;
        self::handle_login_private($params['username'], $params['password']);
    }
    
    private static function handle_login_private($username, $password) {
        $user = User::authenticate($username, $password);
        
        if(!$user){
            View::make('user/login.html', array('error' => 'Wrong password or username', 'username' => $username));
        }else{
            $_SESSION['user'] = $user->id;
            Redirect::to('/', array('message' => 'Welcome ' . $user->name . '!'));
        }
    }
    
    /**
     * Kirjaa käyttäjän ulos
     */
    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'You have logged out succesfully!'));
    }
    
    /**
     * Näyttää tietyn käyttäjän tiedot, määritetään id:llä
     * @param $id
     */
    public static function show($id) {
        $user = User::find($id);
        View::make('user/user.html', array('user' => $user));
    }
    
    /**
     * Näyttää sivun jossa näkyy kaikki käyttäjät
     */
    public static function index(){
   	  View::make('user/users.html', array('users' => User::all()));
    }
    
    /**
     * Näyttää kirjautumissivun
     */
    public static function login(){
        if(!self::check_logged_in(false)) {
   	    View::make('user/login.html');
        }
        else {
            Redirect::to("/");
        }
    }
    
    /**
     * Muuntaa käyttäjän tyypin tekstistä flagiksi
     * @param $text
     * @return type
     */
    private static function textToType($text) {
        if($text == "Admin") {
            return 2;
        }
        else if($text == "Right to add drinks") {
            return 1;
        }
        else {
            return 0;
        }
    }
    
    /**
     * Tuhoaa käyttäjän tietyllä id:llä
     * Vaatii kirjautumisen ja admin oikeudet
     * @param $id
     */
    public static function destroy($id) {
        // Ei ole mahdollista poistaa itseään
        if(self::check_logged_in(true) && self::is_admin() && self::get_user_logged_in()->id != $id)
        {
            $user = new User(array('id' => $id));
            $user->destroy();
            Redirect::to('/users', array('message' => 'User deleted succesfully!'));
        }
        else
        {
            Redirect::to('/users');
        }
    }
    
  
    /**
     * Päivittää käyttäjän tiedot kantaan, kutsutaan POST pyynnöllä
     * Vaatii kirjautumisen ja admin oikeudet
     * @param type $id
     */
    public static function update($id) {
        if(self::check_logged_in(true) && self::is_admin()) {
            $params = $_POST;
            $attributes = array(
                'name' => $params['username'],
                'password' => $params['password'],
                'password2' => $params['password'],
                'type' => self::textToType($params['type'])
            );
            
            $user = new User($attributes);
            $user->id = $id;
            $user->setEditing(true);
            if(count($user->errors()) == 0) {
                $user->update();
                Redirect::to('/user/' . $user->id, array('message' => "User edited succesfully"));
            }
            else
            {
                View::make('user/edit.html', array('errors' => $user->errors(), 'attributes' => $user));
            }
        }
    }
    
    /**
     * Luo käyttäjän annetuista parametreista, kutsutaan POST pyynnöllä
     */
    public static function create_user() {
        $params = $_POST;
        $attributes = array(
            'name' => $params['username'],
            'password' => $params['password'],
            'password2' => $params['password2'],
            'type' => 0
        );
        $user = new User($attributes);
        if(count($user->errors()) == 0) {
            $user->save();
            self::handle_login_private($user->name, $user->password);
        }
        else
        {
            View::make('user/register.html', array('errors' => $user->errors(), 'attributes' => $user));
        }
    }
    
    /**
     * Esittää rekiströitymisnäkymän mikäli käyttäjä ei ole kirjautunut sisään
     */
    public static function register(){
        if(!self::check_logged_in(false)) {
   	    View::make('user/register.html');
        }
        else
        {
            Redirect::to("/index.php");
        }
    }
    
    
    /**
     * Muokkaa käyttäjän tietoja
     * Vaatii kirjautumisen
     * @param $id
     */
    public static function editUser($id){
        if(self::check_logged_in(true) && self::is_admin()) {
          $user = User::find($id);
   	  View::make('user/edit.html', array('attributes' => $user));
        }
        else {
            Redirect::to('/user/' . $id);
        }
    }
  }
