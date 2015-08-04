<?php

  $routes->get('/drink/:id', function($id) {
    DrinksController::show($id);
  });

  $routes->post('/drink', function() {
    DrinksController::store();
  });

  $routes->get('/', function() {
    DrinksController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/drinks', function() {
      DrinksController::index();
  });
  
  $routes->get('/users', function() {
      UsersController::index();
  });
  
  $routes->get('/login', function() {
      UsersController::login();
  });
  

  $routes->get('/drink/new', function() {
      DrinksController::add_drink();
  });
  
  $routes->get('/add_drink', function() {
      DrinksController::add_drink();
  });

  $routes->get('/register', function() {
      UsersController::register();
  });
  
  $routes->get('/user', function() {
      UsersController::viewUser();
  }); 
  
   $routes->get('/drink', function() {
       DrinksController::view_drink();
  });
  
   $routes->get('/edit_user', function() {
       UsersController::editUser();
  });