<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request,['id' => 'required|exists:customers']);
        $id = $request->get('id');
        $customer = Auth::loginUsingId($id);
        $token = $customer->createToken('test');
        return ['token' => $token->plainTextToken];
    }
}
