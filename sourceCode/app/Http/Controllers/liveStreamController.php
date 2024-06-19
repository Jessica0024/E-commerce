<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveStream;
use App\Models\Forum;

class LiveStreamController extends Controller
{
    public function save(Request $request)
    {
        // Retrieve form data
        $live_name = $request->input('name');
        $desc = $request->input('desc');
        $embed_link = $request->input('embed_link');



        // Save data to the database
        $liveStream = new LiveStream();
        $liveStream->name = $live_name;
        $liveStream->embed_link = $embed_link;
        $liveStream->description = $desc;
        $liveStream->save();

        // Assuming 'content' field exists in form
        $forum = new Forum;
        $forum->live_stream_id = $liveStream->id; // Assigning the ID of the newly created LiveStream
        $forum->user_id = auth()->id();
        $forum->content = $request->input('content');
        $forum->save();
     
        // Redirect to the 'aboutUs' route with a success message
        return redirect()->route('aboutUs')->with('success', 'Post has been created!');
    }
}
