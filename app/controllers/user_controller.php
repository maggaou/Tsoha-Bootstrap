<?php

class UserController extends BaseController {

    public static function login() {
        View::make('kirjautuminen.html');
    }

    public static function naytaKayttaja() {
        $kayttaja = self::get_user_logged_in();
        View::make('kayttajasivu.html', array(
            'kayttaja' => $kayttaja
        ));
    }

    public static function handle_login() {
        $params = $_POST;

        $user = User::authenticate($params['username'], $params['password']);
        if ($user) {
            Kint::dump('Löydettiin käyttäjä: ' . $user->name . ':' . $user->password);
        }
        if (!$user) {
            View::make('kirjautuminen.html', array('username' => $params['username'],
                'virheet' => array('Virhe: tunnus tai salasana väärin')));
        }
        $_SESSION['user'] = $user->user_id;
        if ($user->aihe) {
            Redirect::to('/kayttaja', array('viesti' => 'Kirjautuminen onnistui'));
        }
        if (!$user->asema == 'vastuuhenkilö') {
            Redirect::to('/aiheet', array('viesti' => 'Tervetuloa valitsemaan itsellesi aihe ' . $user->name . '!'));
        } else {
            Redirect::to('/aiheet', array('viesti' => 'Olet kirjautunut sisään vastuuhenkilönä'));
        }
    }

    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/login', array('message' => 'Olet kirjautunut ulos!'));
    }

}
