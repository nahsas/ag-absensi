<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/users",
     * summary="List semua user",
     * @OA\Response(
     * response=200,
     * description="Berhasil menampilkan daftar user"
     * )
     * )
     */
    public function index()
    {
        // Logika untuk menampilkan daftar user
    }
}
