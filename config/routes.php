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
    AiheController::naytaAihemuokkaus($aihe_id);
});
$routes->post('/aihemuokkaus/:aihe_id', function($aihe_id) {
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
$routes->post('/aihelisays', function() {
    AiheController::aihelisays();
});
$routes->get('/aihelisays', function() {
    AiheController::naytaAihelisays();
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
$routes->get('/aihe/:aihe_id/poista', function($aihe_id) {
    AiheController::poista($aihe_id);
});
