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
$routes->get('/aihemuokkaus/:aihe_id', function($aihe_id) {
    AiheController::aihemuokkaus($aihe_id);
});
$routes->get('/aiheet', function() {
    AiheController::listAll();
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
$routes->get('/aihe/:aihe_id', function($aihe_id) {
    AiheController::aihe($aihe_id);
});
