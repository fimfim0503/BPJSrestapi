<?php

namespace App\Http\Controllers;

use App\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $produk=Produk::all();
        return response()->json($produk);
    }
    
    public function create(request $request)
    {
        
        $this->validate($request, [
            'nama'=>'required|string',
            'harga'=>'required|integer',
            'warna'=>'required|string',
            'kondisi'=>'required|in:baru,lama',
            'deskripsi'=>'string',
        ]);
        $data = $request->all();
        $produk = Produk::create($data);
        return response()->json($data);
    }
}
