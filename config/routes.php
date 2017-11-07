<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

    $routes->get('/login', function() {
    HelloWorldController::login();
  });
    $routes->get('/aihemuokkaus', function() {
    HelloWorldController::aihemuokkaus();
  });
    $routes->get('/aiheet', function() {
    HelloWorldController::aihelistaus();
  });
    $routes->get('/kategoriamuokkaus', function() {
    HelloWorldController::kategoriamuokkaus();
  });
    $routes->get('/kategoria', function() {
    HelloWorldController::kategoria();
  });
    $routes->get('/aihelisays', function() {
    HelloWorldController::aihelisays();
  });
    $routes->get('/kategorialisays', function() {
    HelloWorldController::kategorialisays();
  });
    $routes->get('/kategorialistaus', function() {
    HelloWorldController::kategorialistaus();
  });
    $routes->get('/aihe', function() {
    HelloWorldController::aihe();
  });