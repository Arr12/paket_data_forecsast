<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\Roles;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return view('admin.pages.home');
        }else{
            return view('admin.layouts.login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::Attempt($data)) {
            $roles = Roles::where('id', auth()->user()->id)->get();
            if(count($roles) != 0){
                foreach ($roles as $key => $value) {
                    $name = $value->name;
                }
            }
            Session::put('roles', $name);
            return redirect(route('dashboard'));
        }else{
            Session::flash('error', 'Email atau Password Salah');
            return redirect('/');
        }
    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect('/');
    }
}
