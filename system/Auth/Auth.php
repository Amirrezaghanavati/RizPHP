<?php

namespace System\Auth;

use App\User;
use System\Session\Session;

class Auth
{


    private string $redirectTo = '/login';


    private function userMethod()
    {
        if (!Session::get('user')) {
            return redirect($this->redirectTo);
        }

        $user = User::find(Session::get('user'));
        if (!$user) {
            Session::remove('user');
            return redirect($this->redirectTo);
        }

        return $user;
    }

    private function checkMethod()
    {
        if (!Session::get('user')) {
            return redirect($this->redirectTo);
        }

        $user = User::find(Session::get('user'));
        if (!$user) {
            Session::remove('user');
            return redirect($this->redirectTo);
        }

        return true;
    }

    private function checkLoginMethod()
    {
        if (!Session::get('user')) {
            return false;
        }

        $user = User::find(Session::get('user'));
        if (!$user) {
            return false;
        }

        return true;
    }

    private function LoginByEmailMethod($email, $password)
    {
        $user = User::where('email', $email)->get();
        if ($user) {
            error('login', 'کاربر وجود ندارد.');
            return false;
        }
        if (password_verify($password, $user[0]->password) && $user[0]->is_active == 1) {
            Session::set('user', $user[0]->id);
            return true;
        } else {
            error('login', 'کلمه عبور اشتباه است.');
            return false;
        }
    }

    private function LoginByIdMethod($id)
    {
        $user = User::find($id);
        if (!$user) {
            error('login', 'کاربر وجود ندارد.');
            return false;
        } else {
            Session::set('user', $user->id);
            return true;
        }

    }

    private function logoutMethod()
    {
        Session::remove('user');
    }


    public function __call($name, $parameters)
    {
        return $this->methodCaller($name, $parameters);
    }

    public static function __callStatic($name, $parameters)
    {
        return (new self)->methodCaller($name, $parameters);

    }

    private function methodCaller($method, $arguments)
    {
        $suffix = 'Method';
        $methodName = $method . $suffix;
        return call_user_func_array([$this, $methodName], $arguments);
    }

}