<?php

namespace App\Http\Controllers;
use App\User;
use App\Antrian;
use App\Poli;
use App\Dokter;
use App\Operasi;
use App\Jadwalpoli;
use App\Referensi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;





class AksesvclaimController extends Controller
{
   
    
    public function akses (request $request)
    
    {
        
        $validator = Validator::make($request->all(), [
            'nomorkartu' => 'required|string|max:13|min:13', 
        ]);
       
        $tanggal = $request->tanggalperiksa;

        // return $tanggal;    
        $day = date('D', strtotime($tanggal));

            $dayList = array(
                'Sun' => 'Minggu',
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu'
            );
            $hari= $dayList[$day];

            //ngambil hari ini
            $dt1 = Carbon::now();
           
            
            
            
            //get namahari
            $gethari=Jadwalpoli::where('kodepoli',$request->kodepoli)
            ->wherenamahari($hari)
            ->wherestatus(1)
            ->first();

        // $kuota=$gethari->kuota;

            //ngambil nama hari
            $gethari2=Jadwalpoli::where('kodepoli',$request->kodepoli)
            ->wherenamahari($hari)
            ->wherestatus(1)
            ->first('namahari');

            //get max antrian 
            $max1=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)
            ->where('kodepoli', $request->kodepoli)
            ->max('NOMOR');

            //get antrian di hari yg sama
            $harisama=Antrian::where('nomorkartu','=',$request->nomorkartu)
            ->wheretanggalperiksa($request->tanggalperiksa)
            ->first('tanggalperiksa');
            
            //get tanggal sekarang
            $dt1 = Carbon::now();
            $dt= $dt1->toDateString();

            //get nama poly

            $namapoli2 = Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('namapoli')
            ->first();

            //get nama dokter

            $namadokter = Dokter::where('kodedokter', '=', $request->kodedokter)
            ->select('namadokter')
            ->first();


            $data = "13359";
            $secretKey = "0vLCB9FAD2";
            // Computes the timestamp
            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
           // Computes the signature by hashing the salt with the secret key as the key
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
 
            $encodedSignature = base64_encode($signature);

            $Xconsid=$data;
            $Xtimestamp=$tStamp;
         $Xsignature=$encodedSignature;

         
         $anak='00020738340991';
            $anak2='2021-08-04';

            $poli='anak';


                $vcpoli=Http::withHeaders([
                    'X-cons-id'=>$Xconsid,
                    'X-timestamp' => $Xtimestamp,
                    'X-signature' => $Xsignature
                ])
                
                
                ->get('https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest//Peserta/nokartu/'.$anak.'/tglSEP/'.$anak2)
                // ->get('https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/referensi/faskes/'.$anak.'/'.$anak2)
                // ->get('https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/referensi/poli/'.$anak)
           
                ->json();
            
                $vcpoli2=Http::withHeaders([
                    'X-cons-id'=>$Xconsid,
                    'X-timestamp' => $Xtimestamp,
                    'X-signature' => $Xsignature
                ])

                 ->get('https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/referensi/poli/'.$poli)
           
                ->json();

        // $hasil= $vcpoli['metaData'] ['code'];

        return $vcpoli2;

       

        

           
    }
    
}
