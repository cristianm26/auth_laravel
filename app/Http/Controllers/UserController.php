<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class UserController extends Controller
{
    //
    public function register(Request $request){

        $user = User::where('email', $request['email'])->first();

        if ($user){
            $response['status']=0;
            $response['message'] = 'El correo electrónico ya existe';
            $response['code']=409;
        }else{
            $user = User::create([
                'name' => $request->name,
                'email'=>  $request->email,
                'password'=> bcrypt($request->password),
            ]);
    
            $response['status']=1;
            $response['message'] = 'Usuario Registrado Correctamente';
            $response['code']=200;
        }
        return response()->json($response);
    }

    public function login(Request $request){
            $credentials = $request->only('email', 'password');
            try {
               if (!JWTAuth::attempt($credentials)) {
                   # code...
                   $response['status']=0;
                   $response['data']=null;
                   $response['message'] = 'El correo o la contraseña es Incorrecto';
                   $response['code']=401;
                   return response()->json($response);
               }
            } catch (JWTException $e) {
                //throw $th;
                $response['data']=null;
                $response['code']=500;
                $response['message'] = 'No se pudo crear el token';
                return response()->json($response);
            }

            $user = auth()->user();
            $data['token'] = auth() ->claims([
                'user_id' => $user->id,
                'email'=> $user->email
            ])->attempt($credentials);

            $response['status']=1;
            $response['data']=$data;
            $response['message'] = 'El inicio de sesion es correcto';
            $response['code']=200;
            return response()->json($response);
    }
}
