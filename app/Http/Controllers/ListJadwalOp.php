<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Antrian;
use App\Poli;
use App\Operasi;
use App\Jadwalpoli;
use App\Referensi;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class ListJadwalOp extends Controller
{
    //
    public function Listjadwaoperasi(request $request)
    {
        
        $tanggalawal=$request->tanggalawal;
        $tanggalakhir=$request->tanggalakhir;
        $poli=$request->kodepoli;
        
        $data=Operasi::whereBetween('tanggaloperasi', [$tanggalawal, $tanggalakhir])->get();
     //    $poli = Poli::where('kodepoli', '=', $request->kodepoli)
     //    ->select('kodepoli')
     //    ->first();
 
 // $tanggal= $tanggalawal > $tanggalakhir;
 
 // return response()->json([
 //     "message"=> $tanggal
 // ]);
 
 
    if ( !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tanggalawal) ) {
        return response()->json([
            "response"=>([
                
                "metadata"=>([
                    "message"=>"format tanggal salah",
                    "code"=>200
                ])
            ])
        ]);
    }elseif (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tanggalakhir) ){
        
        return response()->json([
            "response"=>([
                
                "metadata"=>([
                    "message"=>"format tanggal salah",
                    "code"=>200
                ])
            ])
        ]);
    } elseif($tanggalawal > $tanggalakhir){
             return response()->json([
                 "response"=>([
                     
                     "metadata"=>([
                         "message"=>"tanggal akhir harus lebih besar dari tanggal awal",
                         "code"=>200
                     ])
                 ])
             ]);
    }else{
        return response()->json([
            "response"=>([
                "list"=>$data,
                "metadata"=>([
                    "message"=>"ok",
                    "code"=>200
                ])
            ])
        ]);
        
    }

           




   

    }

}
