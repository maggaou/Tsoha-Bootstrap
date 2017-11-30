<?php

class MainController extends BaseController {

    public static function paasivu() {
        $kayttaja = self::get_user_logged_in();
        View::make('home.html', array(
            'valittu' => Aihe::aiheitaValittuYhteensa(),
            'kayttaja' => $kayttaja
        ));
    }

    public static function hiekkalaatikko() {
        // tämä metodi on sovelluksen testausta varten!
    }
    
    public static function tyhjennaAiheValinnat() {
        
    }

}
