<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use App\Jobs\ProcessCsvUpload;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate(['csv_file' => 'required|mimes:csv,txt']);

        $filename = $request->file('csv_file')->getClientOriginalName();
        $request->file('csv_file')->move(storage_path('app/uploads'), $filename);

        $upload = FileUpload::create([
            'filename' => $filename,
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        ProcessCsvUpload::dispatch($upload);

        return redirect('/')->with('success', 'Upload started!');
    }

    public function uploads()
    {
        return FileUpload::orderBy('uploaded_at', 'desc')->get();
    }
}
