<?php


  $routes->get('/drink/:id/edit', function($id){
    DrinksController::edit($id);
  });
  
  $routes->get('/login', function(){
      UsersController::login();
  });
  
  $routes->get('/logout', function(){
      UsersController::logout();
  });
  
  $routes->post('/login', function(){
      UsersController::handle_login();
  });
  
  $routes->post('/drink/:id/edit', function($id){
   DrinksController::update($id);
  });
  
  $routes->post('/drink/:id/destroy', function($id){
  DrinksController::destroy($id);
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
  

  $routes->get('/drink/new', function() {
      DrinksController::add_drink();
  });
  
  $routes->get('/drink/:id', function($id) {
    DrinksController::show($id);
  });
  
  $routes->get('/user/:id', function($id) {
    UsersController::show($id);
  });
  
  $routes->get('/add_drink', function() {
      DrinksController::add_drink();
  });

  $routes->get('/register', function() {
      UsersController::register();
  });
  
  $routes->post('/register', function() {
      UsersController::create_user();
  });
  

  
  $routes->get('/user/:id/edit', function($id) {
       UsersController::editUser($id);
  });
  
  $routes->post('/user/:id/edit', function($id) {
       UsersController::update($id);
  });
  
  $routes->post('/user/:id/destroy', function($id) {
       UsersController::destroy($id);
  });
  
   $routes->post('/drink/:id/accept', function($id) {
       DrinksController::acceptDrink($id);
  });