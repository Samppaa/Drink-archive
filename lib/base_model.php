<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      foreach($attributes as $attribute => $value){
        if(property_exists($this, $attribute)){
          $this->{$attribute} = $value;
        }
      }
    }
    
    protected function validate_string_length_less_than($string, $length) {
          if(strlen($string) < $length) {
              return false;
          }
          return true;
      }
      
    protected function validate_string_length_greater_than($string, $length) {
          if(strlen($string) > $length) {
              return false;
          }
          return true;
    }

    public function errors(){
      $errors = array();

      foreach($this->validators as $validator){
          $errors = array_merge($errors, $this->{$validator}());
      }

      return $errors;
    }

  }
