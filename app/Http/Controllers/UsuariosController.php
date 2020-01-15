<?php

namespace App\Http\Controllers;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class UsuariosController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showAll(User::get());
    }

    public function login_usuario(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', config('services.passport.login_endpoint'),[
                'form_params'=>[
                    'grant_type'=>'password',
                    'client_id'=>config('services.passport.client_id'),
                    'client_secret'=>config('services.passport.client_secret'),
                    'username'=>$request->username,
                    'password'=>$request->password
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
           if($e->getCode()==400){
            return $this->errorResponse('Error. Usuario y/o Password incorrecto.',$e->getCode());
           }else if($e->getCode()==401){
                return $this->errorResponse('Error. Usuario y/o Password incorrecto.',$e->getCode());
           }

           return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.',$e->getCode());
        }
    }

    public function logout_usuario()
    {
        //elimina todos los tokens asociados a el usuario logueado con ese token
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });

        return $this->successResponse('Sesión finalizada con éxito.',200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
