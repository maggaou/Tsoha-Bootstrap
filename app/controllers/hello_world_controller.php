<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  echo 'Tämä on etusivu!';
    }

    public static function login(){
      // Testaa koodiasi täällä
      View::make('kirjautuminen.html');
    }
    public static function aihemuokkaus(){
      // Testaa koodiasi täällä
      View::make('aihe_muokkaus.html');
    }
    public static function aihelistaus(){
      // Testaa koodiasi täällä
      View::make('aihe_listaus.html');
    }
    public static function kategoria(){
      // Testaa koodiasi täällä
      View::make('kategoriasivu.html');
    }
    public static function kategoriamuokkaus(){
      // Testaa koodiasi täällä
      View::make('kategoria_muokkaus.html');
    }
    public static function aihelisays(){
      // Testaa koodiasi täällä
      View::make('aihe_lisays.html');
    }
    public static function kategorialisays(){
      // Testaa koodiasi täällä
      View::make('kategoria_lisays.html');
    }
    public static function kategorialistaus(){
      // Testaa koodiasi täällä
      View::make('kategoria_listaus.html');
    }
    public static function aihe(){
      // Testaa koodiasi täällä
      View::make('aihesivu.html');
    }
  }
