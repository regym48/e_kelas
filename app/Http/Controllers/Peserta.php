<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use \Firebase\JWT\JWT;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecrypException;

use App\M_Peserta;

class Peserta extends Controller
{
    //
    public function registrasiPeserta(Request $request){
        $validator = Validator::make($request -> all(), [
            'nama' => 'required',
            'email' => 'required | unique:tbl_peserta',
            'password' => 'required | confirmed',
            'password_confirmation' => 'required'
        ]);
        if($validator -> fails()){
            return response () ->json([
                'status' => 'gagal',
                'message' => $validator -> messages()
            ]); 
        }

        if(M_Peserta::create([
            'nama' => $request-> nama,
            'email' => $request-> email,
            'password' => encrypt($request-> password)
        ])){
            return response () ->json([
                'status' => 'berhasil',
                'message' => 'data berhasil disimpan'
            ]); 
        }else{  
            return response () ->json([
                'status' => 'gagal',
                'message' => 'data gagal disimpan'
            ]); 
        }
    }

    public function loginPeserta(Request $request){
        $validator = Validator::make($request -> all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if($validator -> fails()){
            return response () ->json([
                'status' => 'gagal',
                'message' => $validator -> messages()
            ]); 
        }

        $cek = M_Peserta::where('email', $request->email)->count();
        $peserta = M_Peserta::where('email', $request->email)->get();

        if($cek > 0){
            foreach($peserta as $psrt){
                if($request->password == decrypt($psrt->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        "extime" => time()+(60*120),
                        "id_peserta" => $psrt->id_peserta
                    );
                    $jwt = JWT::encode($data,$key); //appkey digunakan untuk membuat token dari jwt

                    M_Peserta::where('id_peserta', $psrt->id_peserta)->update(
                        [
                            'token' => $jwt
                        ]
                    );

                    return response () ->json([
                        'status' => 'berhasil',
                        'message' => 'berhasil login',
                        'token' => $jwt,
                        'nama' => $psrt->nama
                    ]); 

                }else{
                    return response () ->json([
                        'status' => 'gagal',
                        'message' => 'password salah'
                    ]); 
                }
            }

        }else{
            return response () ->json([
                'status' => 'gagal',
                'message' => 'email tidak terdaftar'
            ]); 
        }
    }
}
