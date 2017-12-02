<?php
// yleiset osoitteet
$routes->get('/', function() {
    MainController::paasivu();
});
$routes->get('/tyhjennavalinnat', function() {
    AiheController::tyhjennaKaikkiAiheValinnat();
});
$routes->get('/hiekkalaatikko', function() {
    MainController::hiekkalaatikko();
});

// kirjautuminen
$routes->post('/login', function() {
    UserController::handle_login();
});
$routes->get('/login', function() {
    UserController::login();
});
$routes->post('/login', function() {
    UserController::handle_login();
});
$routes->post('/logout', function(){
  UserController::logout();
});


// aiheisiin liittyvät osoitteet
$routes->get('/aiheet', function() {
    AiheController::listaaKaikkiAiheet();
});
$routes->post('/aihelisays', function() {
    AiheController::suoritaAiheenLisays();
});
$routes->get('/aihelisays', function() {
    AiheController::naytaAiheenLisays();
});
$routes->get('/aihemuokkaus/:aihe_id', function($aihe_id) {
    AiheController::naytaAiheenmuokkaus($aihe_id);
});
$routes->get('/aihe/:aihe_id', function($aihe_id) {
    AiheController::naytaAihe($aihe_id);
});
$routes->post('/aihemuokkaus/:aihe_id', function($aihe_id) {
    AiheController::suoritaAiheenMuokkaus($aihe_id);
});
$routes->get('/aihe/:aihe_id/poista', function($aihe_id) {
    AiheController::poista($aihe_id);
});

$routes->get('/aihevalinta/:aihe_id', function($aihe_id){
    AiheController::suoritaAihevalinta($aihe_id);
});


// kategorioihin liittyvät osoitteet
$routes->get('/kategoria/:kategoria_id', function($kategoria_id) {
    KategoriaController::naytaKategoria($kategoria_id);
});
$routes->post('/kategorialisays', function() {
    KategoriaController::suoritaKategorianLisays();
});
$routes->get('/kategorialisays', function() {
    KategoriaController::naytaKategorianLisays();
});
$routes->get('/kategoriat', function() {
    KategoriaController::listaaKaikkiKategoriat();
});
$routes->post('/kategoriamuokkaus/:kategoria_id', function($kategoria_id) {
    KategoriaController::suoritaKategorianMuokkaus($kategoria_id);
});
$routes->get('/kategoriamuokkaus/:kategoria_id', function($kategoria_id) {
    KategoriaController::naytaKategorianMuokkaus($kategoria_id);
});
$routes->get('/kategoria/:kategoria_id/poista', function($kategoria_id) {
    KategoriaController::poista($kategoria_id);
});


