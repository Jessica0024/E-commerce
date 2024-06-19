<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveStream;

class AboutUsController extends Controller
{
    public function index()
    {
        // Fetch data or perform any logic needed for the aboutUs view
        $liveStreams = LiveStream::paginate(1);

        // Pass the data to the view
        return view('aboutUs', compact('liveStreams'));
    }
}
