<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function generar()
    {
        try {
            Artisan::call('db:backup');
            return back()->with('success', '✅ Backup generado y subido a Cloudinary correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el backup: ' . $e->getMessage());
        }
    }
}