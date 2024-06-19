<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\qnas; 

class QnaController extends Controller
{
    public function save(Request $request)
    {
        // Retrieve form data
        $question = $request->input('question');
        $answer = $request->input('answer');

        // Generate a unique ID for the question
        $uniqueId = uniqid();

        // Save data to the database
        $qnas = new qnas();
        $qnas->id = $uniqueId; // Assign the generated ID to the 'id' field
        $qnas->question = $question;
        $qnas->answer = $answer;
        $qnas->save();

        return redirect('thankyou')->with('success', 'Question has been added!');

    }
    public function destroy($id)
{
    // Find the FAQ item
    $qna = qnas::find($id);

    // If the FAQ item exists, delete it
    if ($qna) {
        $qna->delete();
        return redirect()->back()->with('success', 'FAQ deleted successfully.');
    } else {
        return redirect()->back()->with('error', 'FAQ not found.');
    }
}

}
