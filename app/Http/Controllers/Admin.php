<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use \Firebase\JWT\JWT;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecrypException;

use App\M_Admin;

class Admin extends Controller
{
    //
    public function tambahAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'nama' => 'required',
            'email' => 'required | unique:tbl_user',
            'password' => 'required',
            'token' => 'required'
        ]);
        if($validator -> fails()){
            return response () ->json([
                'status' => 'gagal',
                'message' => $validator -> messages()
            ]); 
        }

        $token = $request -> token;
        $tokenDb = M_Admin::where('token',$token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            
            if($decoded_array['extime'] > time()){
                if(M_Admin::create([
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
            }else{
                return response () ->json([
                    'status' => 'gagal',
                    'message' => 'token kadaluarsa'
                ]); 
            }
        }else{
            return response () ->json([
                'status' => 'gagal',
                'message' => 'token tidak valid'
            ]); 
        }
    }

    public function loginAdmin(Request $request){
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

        $cek = M_Admin::where('email', $request->email)->count();
        $admin = M_Admin::where('email', $request->email)->get();

        if($cek > 0){
            foreach($admin as $adm){
                if($request->password == decrypt($adm->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        "extime" => time()+(60*120),
                        "id_admin" => $adm->id_user
                    );
                    $jwt = JWT::encode($data,$key); //appkey digunakan untuk membuat token dari jwt

                    M_Admin::where('id_user', $adm->id_user)->update(
                        [
                            'token' => $jwt
                        ]
                    );

                    return response () ->json([
                        'status' => 'berhasil',
                        'message' => 'berhasil login',
                        'token' => $jwt
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

    public function hapusAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'id_user' => 'required',
            'token' => 'required'
        ]);
        if($validator -> fails()){
            return response () ->json([
                'status' => 'gagal',
                'message' => $validator -> messages()
            ]); 
        }

        $token = $request -> token;
        $tokenDb = M_Admin::where('token',$token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            
            if($decoded_array['extime'] > time()){
                if(M_Admin::where('id_user', $request->id_user)->delete()){
                    return response () ->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil dihapus'
                    ]); 
                }else{  
                    return response () ->json([
                        'status' => 'gagal',
                        'message' => 'data gagal dihapus'
                    ]); 
                }
            }else{
                return response () ->json([
                    'status' => 'gagal',
                    'message' => 'token kadaluarsa'
                ]); 
            }
        }else{
            return response () ->json([
                'status' => 'gagal',
                'message' => 'token tidak valid'
            ]); 
        }
    }

    public function listAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'token' => 'required'
        ]);
        if($validator -> fails()){
            return response () ->json([
                'status' => 'gagal',
                'message' => $validator -> messages()
            ]); 
        }

        $token = $request -> token;
        $tokenDb = M_Admin::where('token',$token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            
            if($decoded_array['extime'] > time()){
                $admin = M_Admin::get();
                $data = array();
                foreach($admin as $adm){
                    $data[] = array(
                        'nama' => $adm->nama,
                        'email' => $adm->email,
                        'id_user' => $adm->id_user
                    );
                }
                return response () ->json([
                    'status' => 'berhasil',
                    'message' => 'data berhasil diambil',
                    'data' => $data
                ]); 

            }else{
                return response () ->json([
                    'status' => 'gagal',
                    'message' => 'token kadaluarsa'
                ]); 
                
            }
        }else{
            return response () ->json([
                'status' => 'gagal',
                'message' => 'token tidak valid'
            ]); 
        }
    }

    // public function ubahPasswordAdmin(Request $request){
    //     $validator = Validator::make($request -> all(), [
    //         'password' => 'required | unique:tbl_user,password,'.$request->id_user.',id_user',
    //         'id_user' => 'required'
    //     ]);
    //     if($validator -> fails()){
    //         return response () ->json([
    //             'status' => 'gagal',
    //             'message' => $validator -> messages()
    //         ]); 
    //     }

    //     $token = $request -> token;
    //     $tokenDb = M_Admin::where('token',$token)->count();
    //     if($tokenDb > 0){
    //         $key = env('APP_KEY');
    //         $decoded = JWT::decode($token, $key, array('HS256'));
    //         $decoded_array = (array) $decoded;
            
    //         if($decoded_array['extime'] > time()){
    //             if(M_Admin::where('id_user', $request->id_user)->update([
    //                 'password' => $request-> password
    //             ])){
    //                 return response () ->json([
    //                     'status' => 'berhasil',
    //                     'message' => 'data berhasil diubah'
    //                 ]); 
    //             }else{  
    //                 return response () ->json([
    //                     'status' => 'gagal',
    //                     'message' => 'data gagal diubah'
    //                 ]); 
    //             }
    //         }else{
    //             return response () ->json([
    //                 'status' => 'gagal',
    //                 'message' => 'token kadaluarsa'
    //             ]); 
    //         }
    //     }else{
    //         return response () ->json([
    //             'status' => 'gagal',
    //             'message' => 'token tidak valid'
    //         ]); 
    //     }
    // }
}
