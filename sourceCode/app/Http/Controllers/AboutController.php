<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LiveStream;
use App\Models\Forum;

class AboutController extends Controller
{
    public function index()
    {
        // Fetch data or perform any logic needed for the aboutUs view
        $liveStreams = LiveStream::paginate(3);

        // Pass the data to the view
        return view('aboutUs', compact('liveStreams'));
    }

    public function search(Request $request)
{
    $query = $request->input('query');

    // Perform the search query to find live streams matching the query
    $liveStreams = LiveStream::where('name', 'like', '%'.$query.'%')
        ->orWhere('description', 'like', '%'.$query.'%')
        ->paginate(10);

    return view('aboutUs', compact('liveStreams'));
}
public function delete($id)
{
    // Find the live stream by ID
    $liveStream = LiveStream::findOrFail($id);

    // Delete the live stream
    $liveStream->delete();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Live stream deleted successfully.');
}


public function storeComment(Request $request, $liveStreamId)
{// Inside your controller method
$liveStreams = LiveStream::with('forum')->paginate(10);

    $request->validate([
        'content' => 'required|string|max:255',
    ]);

    // Create a new comment
    Forum::create([
        'live_stream_id' => $liveStreamId,
        'user_id' => auth()->id(), // Assuming authenticated user
        'content' => $request->input('content'),
    ]);

    return redirect()->back()->with('success', 'Comment posted successfully.');
}

}
