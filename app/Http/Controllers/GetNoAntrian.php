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



class GetNoAntrian extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Antrianbpjs(request $request)

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
        $hari = $dayList[$day];

        //ngambil hari ini
        $dt1 = Carbon::now();




        //get namahari
        $gethari = Jadwalpoli::where('kodepoli', $request->kodepoli)
            ->wherenamahari($hari)
            ->wherestatus(1)
            ->first();

        // $kuota=$gethari->kuota;

        //ngambil nama hari
        $gethari2 = Jadwalpoli::where('kodepoli', $request->kodepoli)
            ->wherenamahari($hari)
            ->wherestatus(1)
            ->first('namahari');

        //get max antrian 
        $max1 = Antrian::where('tanggalperiksa', '=', $request->tanggalperiksa)
            ->where('kodepoli', $request->kodepoli)
            ->max('NOMOR');

        //get antrian di hari yg sama
        $harisama = Antrian::where('nomorkartu', '=', $request->nomorkartu)
            ->wheretanggalperiksa($request->tanggalperiksa)
            ->first('tanggalperiksa');

        //get tanggal sekarang
        $dt1 = Carbon::now();
        $dt = $dt1->toDateString();

        //get nama poly

        $namapoli2 = Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('namapoli')
            ->first();

        //get nama dokter

        $namadokter = Dokter::where('kodedokter', '=', $request->kodedokter)
            ->select('namadokter')
            ->first();

        //cek peserta BPJS
        $data = "13359";
        $secretKey = "0vLCB9FAD2";
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);

        $encodedSignature = base64_encode($signature);

        $Xconsid = $data;
        $Xtimestamp = $tStamp;
        $Xsignature = $encodedSignature;

        $kartu = $request->nomorkartu;


        $vcpoli = Http::withHeaders([
            'X-cons-id' => $Xconsid,
            'X-timestamp' => $Xtimestamp,
            'X-signature' => $Xsignature
        ])


            ->get('https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/Peserta/nokartu/' . $kartu . '/tglSEP/' . $tanggal)
            ->json();

        $hasilcekpeserta = $vcpoli['metaData']['code'];
        $nama = $vcpoli['response']['peserta']['nama'];

        // cek apakah ada no antri di hari yg sama (status ok)?
        if ($validator->fails()) {
            //return response()->json($validator->errors()->toJson(), 400);
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "Kartu tidak valid",
                    "code" => 400
                ])
            ]);
        } elseif ($hasilcekpeserta == 201) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "No Peserta Tidak ditemukan",
                    "code" => 400
                ])
            ]);
        } elseif (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->tanggalperiksa)) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "Format Tanggal Salah",
                    "code" => 400
                ])
            ]);
        } elseif ($request->tanggalperiksa <= $dt) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "tanggal tidak boleh kurang dari hari ini",
                    "code" => 400
                ])
            ]);
        } elseif ($harisama == !null) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "Pasien sudah mendapatkan no antrian",
                    "code" => 400
                ])
            ]);
        } elseif ($namapoli2 == null) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "Nama Poli tidak ditemukan",
                    "code" => 400
                ])
            ]);
        } elseif ($gethari == null) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "Poliklinik di RSU Dr. Slamet tidak melakukan pelayanan di tanggal tersebut",
                    "code" => 400
                ])
            ]);
        } elseif ($max1 >= $gethari->kuota) {
            return response()->json([
                "response" => ([]), "metadata" => ([
                    "message" => "No Antrian Habis",
                    "code" => 400
                ])
            ]);
        } else {
            $input = new Antrian;

            $input->nomorkartu = $request->nomorkartu;
            $input->nik = $request->nik;
            $input->notelp = $request->nohp;
            $input->kodepoli = $request->kodepoli;
            $input->norm = $request->norm;
            $input->name = $nama;
            $input->tanggalperiksa = $request->tanggalperiksa;
            $input->kodedokter = $request->kodedokter;
            $input->jampraktek = $request->jampraktek;
            $input->jeniskunjungan = $request->jeniskunjungan;
            $input->nomorreferensi = $request->nomorreferensi;
            $input->kodebooking = Str::random(20);


            $timestamps = strtotime($request->tanggalperiksa) * 1000;
            $jumlah = 2 + 5;

            //$tanggalcari=Antrian::where('tanggalperiksa',$request->tanggalperiksa)->first();
            $max = Antrian::where('tanggalperiksa', '=', $request->tanggalperiksa)->max('waktuperiksa');
            $jml = $max + 7;

            $tanggalcari = Antrian::where('tanggalperiksa', '=', $request->tanggalperiksa)->first();
            //  return $tanggalcari;

            if ($tanggalcari) {

                $input->waktuperiksa = $max + 300000;
            } else {
                $input->waktuperiksa = $timestamps + 25200000;
            }

            //input kode antrian
            $results = Poli::where('kodepoli', '=', $request->kodepoli)
                ->select('kodeantri')
                ->get();
            $input->kodeantri = $results[0]->kodeantri;

            //input nama poli
            $results = Poli::where('kodepoli', '=', $request->kodepoli)
                ->select('namapoli')
                ->get();
            $input->namapoli = $results[0]->namapoli;

            //input nama dokter
            $results = Dokter::where('kodedokter', '=', $request->kodedokter)
                ->select('namadokter')
                ->get();
            $input->namadokter = $results[0]->namadokter;


            //get kuota
            $kuota = Jadwalpoli::where('kodepoli', '=', $request->kodepoli)
                ->where('namahari', '=', $hari)
                ->select('kuota')
                ->get();
            $kuota2 = $kuota[0]->kuota;

            $input->save();

            $sisajkn = $kuota2 - $input->id;

            return response()->json([
                "response" => ([
                    'nomorantrean' => $input->kodeantri . $input->id,
                    'angkaantrean' => $input->id,
                    'kodebooking' => $input->kodebooking,
                    'pasienbaru' => 0,
                    'norm' => $input->norm,
                    'namapoli' => $input->namapoli,
                    'namadokter' => $input->namadokter,
                    'estimasidilayani' => $input->waktuperiksa,
                    'sisakuotajkn' => $sisajkn,
                    'kuotajkn' => $kuota2,
                    'sisakuotanonjkn' => $sisajkn,
                    'kuotanonjkn' => $kuota2,

                    'keterangan' => 'peserta harap 60 menit lebih awal guna pencatatan administrasi'
                ]), "metadata" => ([
                    "message" => "ok",
                    "code" => 200
                ])
            ]);
        }
    }
}
