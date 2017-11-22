<?php

class HelloWorldController extends BaseController {

    public static function index() {
        View::make('home.html');
    }

    public static function login() {
        // Testaa koodiasi täällä
        View::make('kirjautuminen.html');
    }

    public static function kategoria() {
        // Testaa koodiasi täällä
        View::make('kategoriasivu.html');
    }

    public static function kategoriamuokkaus() {
        // Testaa koodiasi täällä
        View::make('kategoria_muokkaus.html');
    }

    public static function kategorialisays() {
        // Testaa koodiasi täällä
        View::make('kategoria_lisays.html');
    }

    public static function kategorialistaus() {
        // Testaa koodiasi täällä
        View::make('kategoria_listaus.html');
    }

    public static function sandbox() {
        $user = User::find(1);
        Kint::dump($user);
    }

}
