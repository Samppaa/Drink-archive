<?php

  class BaseController{

    /**
     * Hakee sisäänkirjautuneen käyttäjän tiedot
     * @return User
     */
    public static function get_user_logged_in(){
        
        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user'];
            $user = User::find($user_id);

            return $user;
        }

        return null;
    }
    
    /**
     * Tarkastaa onko sisäänkirjautunut käyttäjä admin
     * @return isAdmin
     */
    public static function is_admin() {
        $user = self::get_user_logged_in();
        if(!$user) {
            return false;
        }
        
        if($user->type > 1) {
            return true;
        }
        
        return false;
    }
    

    /**
     * Tarkistaa onko käyttäjä kirjautunut sisään
     * @param $redirect
     * @return isLoggedIn
     */
    public static function check_logged_in($redirect){
      if(isset($_SESSION['user'])) {
          return true;
      }
      else
      {
        if($redirect) {
            Redirect::to("/login", array('message' => 'You need to be logged in to add a drink!'));
        }
        return false;
      }
    }

  }
