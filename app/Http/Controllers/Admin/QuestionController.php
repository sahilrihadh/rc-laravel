<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $questions = Question::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.questions.index', compact('questions'));
    }

    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete question'
            ], 500);
        }
    }
}
