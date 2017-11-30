<?php

class UserController extends BaseController {

    public static function login() {
        View::make('kirjautuminen.html');
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
        } else {
            $_SESSION['user'] = $user->user_id;

            Redirect::to('/aiheet', array('viesti' => 'Tervetuloa takaisin ' . $user->name . '!'));
        }
    }

    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/login', array('message' => 'Olet kirjautunut ulos!'));
    }

}
