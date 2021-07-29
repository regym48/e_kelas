<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use \Firebase\JWT\JWT;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecrypException;

use App\M_Admin;
use App\M_Materi;
use App\M_Peserta;

class Konten extends Controller
{
    //
    public function tambahKonten(Request $request){
        $validator = Validator::make($request -> all(), [
            'judul' => 'required | unique:tbl_konten',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required'
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
                if(M_Materi::create([
                    'judul' => $request-> judul,
                    'keterangan' => $request-> keterangan,
                    'link_thumbnail' => $request-> link_thumbnail,
                    'link_video' => $request-> link_video
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

    public function ubahKonten(Request $request){
        $validator = Validator::make($request -> all(), [
            'judul' => 'required | unique:tbl_konten,judul,'.$request->id_konten.',id_konten',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required',
            'id_konten' => 'required'
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
                if(M_Materi::where('id_konten', $request->id_konten)->update([
                    'judul' => $request-> judul,
                    'keterangan' => $request-> keterangan,
                    'link_thumbnail' => $request-> link_thumbnail,
                    'link_video' => $request-> link_video
                ])){
                    return response () ->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil diubah'
                    ]); 
                }else{  
                    return response () ->json([
                        'status' => 'gagal',
                        'message' => 'data gagal diubah'
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

    public function hapusKonten(Request $request){
        $validator = Validator::make($request -> all(), [
            'id_konten' => 'required',
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
                if(M_Materi::where('id_konten', $request->id_konten)->delete()){
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

    public function listKonten(Request $request){
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
                $konten = M_Materi::get();
                $data = array();
                foreach($konten as $kntn){
                    $data[] = array(
                        'id_konten' => $kntn->id_konten,
                        'judul' => $kntn->judul,
                        'keterangan' => $kntn->keterangan,
                        'link_thumbnail' => $kntn->link_thumbnail,
                        'link_video' => $kntn->link_video,
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

    public function listKontenPeserta(Request $request){
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
        $tokenDb = M_Peserta::where('token',$token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            
            if($decoded_array['extime'] > time()){
                $konten = M_Materi::get();
                $data = array();
                foreach($konten as $kntn){
                    $data[] = array(
                        'id_konten' => $kntn->id_konten,
                        'judul' => $kntn->judul,
                        'keterangan' => $kntn->keterangan,
                        'link_thumbnail' => $kntn->link_thumbnail,
                        'link_video' => $kntn->link_video,
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
}
