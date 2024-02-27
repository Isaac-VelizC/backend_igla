<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function downloadBackup(Request $request)
    {
        
        Artisan::call('backup:run');

        return redirect()->back()->with('success', 'Backup realizado exitosamente.');
    }
}
