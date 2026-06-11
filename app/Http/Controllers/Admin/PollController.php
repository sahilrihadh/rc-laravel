<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\PollStatusChanged;

class PollController extends Controller
{
    /**
     * Display a listing of polls.
     */
    public function index()
    {
        $polls = Poll::with(['options', 'votes'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.polls.index', compact('polls'));
    }

    /**
     * Show form for creating new poll.
     */
    public function create()
    {
        return view('admin.polls.create');
    }

    /**
     * Store a newly created poll.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:500',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|min:1|max:255',
            'correct_option' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            // Check if trying to activate and there's already an active poll
            $isActive = $request->has('is_active');
            
            if ($isActive) {
                // Deactivate all other polls first
                Poll::where('is_active', true)->update(['is_active' => false]);
            }

            // Create poll
            $poll = Poll::create([
                'question' => $request->question,
                'is_active' => $isActive,
                'expires_at' => null
            ]);

            // Create options
            foreach ($request->options as $index => $optionText) {
                $poll->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => ($request->correct_option == $index),
                    'vote_count' => 0
                ]);
            }

            // Broadcast if active
            if ($isActive) {
                broadcast(new PollStatusChanged($this->getPollData($poll)))->toOthers();
            }

            return redirect()->route('admin.polls.index')
                ->with('success', 'Poll created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating poll: ' . $e->getMessage());
            return back()->with('error', 'Failed to create poll: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing poll.
     */
    public function edit($id)
    {
        $poll = Poll::with('options')->findOrFail($id);
        return view('admin.polls.edit', compact('poll'));
    }

    /**
     * Update the specified poll.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:500',
            'option_ids' => 'nullable|array',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|min:1|max:255',
            'correct_option' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            $poll = Poll::with('options')->findOrFail($id);
            
            // Check if trying to activate
            $isActive = $request->has('is_active');
            
            if ($isActive && !$poll->is_active) {
                // Deactivate all other polls first
                Poll::where('is_active', true)->where('id', '!=', $poll->id)
                    ->update(['is_active' => false]);
            }

            // Update poll
            $poll->update([
                'question' => $request->question,
                'is_active' => $isActive
            ]);

            // Update or create options
            $existingOptionIds = $poll->options->pluck('id')->toArray();
            $submittedOptionIds = $request->option_ids ?? [];
            
            // Delete removed options
            $toDelete = array_diff($existingOptionIds, $submittedOptionIds);
            if (!empty($toDelete)) {
                // Delete votes first
                PollVote::whereIn('poll_option_id', $toDelete)->delete();
                // Then delete options
                PollOption::whereIn('id', $toDelete)->delete();
            }

            // Update or create options
            foreach ($request->options as $index => $optionText) {
                $optionId = $request->option_ids[$index] ?? null;
                
                if ($optionId && in_array($optionId, $existingOptionIds)) {
                    // Update existing option
                    $option = PollOption::find($optionId);
                    if ($option) {
                        $option->update([
                            'option_text' => $optionText,
                            'is_correct' => ($request->correct_option == $index)
                        ]);
                    }
                } else {
                    // Create new option
                    $poll->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($request->correct_option == $index),
                        'vote_count' => 0
                    ]);
                }
            }

            // Broadcast if active
            if ($isActive) {
                broadcast(new PollStatusChanged($this->getPollData($poll)))->toOthers();
            } else {
                broadcast(new PollStatusChanged(null))->toOthers();
            }

            return redirect()->route('admin.polls.index')
                ->with('success', 'Poll updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating poll: ' . $e->getMessage());
            return back()->with('error', 'Failed to update poll: ' . $e->getMessage());
        }
    }

    /**
 * Toggle poll status (activate/deactivate).
 */
public function toggleStatus($id)
{
    try {
        $poll = Poll::findOrFail($id);
        
        if (!$poll->is_active) {
            // Activating - deactivate all others first
            Poll::where('is_active', true)->update(['is_active' => false]);
            $poll->update(['is_active' => true]);
            
            // Get the poll HTML for the event
            $pollHtml = $this->getPollHtmlForBroadcast($poll);
            
            // Broadcast active event with HTML
            broadcast(new PollStatusChanged('active', $pollHtml))->toOthers();
            
            return response()->json([
                'success' => true,
                'message' => 'Poll activated successfully!'
            ]);
        } else {
            // Deactivating
            $poll->update(['is_active' => false]);
            
            // Broadcast inactive event
            broadcast(new PollStatusChanged('inactive', null))->toOthers();
            
            return response()->json([
                'success' => true,
                'message' => 'Poll deactivated successfully!'
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Error toggling poll status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to change poll status'
        ], 500);
    }
}

/**
 * Get poll HTML for broadcasting
 */
private function getPollHtmlForBroadcast($poll)
{
    $poll->load('options');
    
    $html = '<div class="mb-4">';
    $html .= '<h4 class="text-danger poll-title">' . e($poll->question) . '</h4>';
    $html .= '<form id="poll-form" method="post">';
    
    foreach ($poll->options as $option) {
        $html .= '<div class="form-check mb-2">';
        $html .= '<label class="form-check-label">';
        $html .= '<input type="radio" class="form-check-input" name="poll" value="' . $option->id . '">';
        $html .= e($option->option_text);
        $html .= '<i class="input-helper"></i>';
        $html .= '</label>';
        $html .= '</div>';
    }
    
    $html .= '<button type="submit" class="btn btn-canin mt-4" id="but_vote">Vote</button>';
    $html .= '</form>';
    $html .= '</div>';
    
    return $html;
}

    /**
 * Display poll results with user votes
 */
public function show($id)
{
    $poll = Poll::with('options')->findOrFail($id);
    
    // Get user votes with pagination
    $userVotes = PollVote::with(['user', 'option'])
        ->where('poll_id', $id)
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    
    $totalVotes = $poll->votes()->count();
    $correctVotes = PollVote::where('poll_id', $id)
        ->where('is_correct', true)
        ->count();
    $incorrectVotes = $totalVotes - $correctVotes;
    
    return view('admin.polls.show', compact('poll', 'userVotes', 'totalVotes', 'correctVotes', 'incorrectVotes'));
}


/**
     * Remove the specified poll.
     */
    public function destroy($id)
    {
        try {
            $poll = Poll::findOrFail($id);
            
            // If this was active, broadcast deactivation
            if ($poll->is_active) {
                broadcast(new PollStatusChanged(null))->toOthers();
            }
            
            $poll->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Poll deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting poll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete poll'
            ], 500);
        }
    }

}