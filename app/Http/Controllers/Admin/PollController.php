<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Events\PollStatusChanged;
use Illuminate\Http\Request;
use App\Models\WebinarSession;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::with('options')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.polls.index', compact('polls'));
    }

    public function create()
    {
        $webinars = WebinarSession::orderBy('id')->get();
        return view('admin.polls.create', compact('webinars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'webinar_session_id' => 'required|exists:webinar_sessions,id',
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0',
        ]);

        // Checkbox value - if present, it's active
        $isActive = $request->has('is_active');

        $poll = Poll::create([
            'webinar_session_id' => $request->webinar_session_id,
            'question' => $request->question,
            'is_active' => $isActive
        ]);

        foreach ($request->options as $index => $optionText) {
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $optionText,
                'vote_count' => 0,
                'is_correct' => ($index == $request->correct_option)
            ]);
        }

        if ($poll->is_active) {
            $pollHtml = $this->generatePollHtml($poll);
            broadcast(new PollStatusChanged($pollHtml, 'active'))->toOthers();
        }

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll created successfully');
    }

    public function edit($id)
    {
        $poll = Poll::with('options')->findOrFail($id);
        return view('admin.polls.edit', compact('poll'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $poll = Poll::findOrFail($id);

        $oldStatus = $poll->is_active;

        $poll->update([
            'question' => $request->question,
            'is_active' => $request->has('is_active')
        ]);

        $existingOptionIds = [];
        foreach ($request->options as $index => $optionText) {
            if (isset($request->option_ids[$index]) && $request->option_ids[$index]) {
                $option = PollOption::find($request->option_ids[$index]);
                if ($option) {
                    $option->update(['option_text' => $optionText]);
                    $existingOptionIds[] = $option->id;
                }
            } else {
                $newOption = PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $optionText,
                    'vote_count' => 0
                ]);
                $existingOptionIds[] = $newOption->id;
            }
        }

        PollOption::where('poll_id', $poll->id)
            ->whereNotIn('id', $existingOptionIds)
            ->delete();

        // Broadcast status change
        if ($poll->is_active != $oldStatus) {
            if ($poll->is_active) {
                $pollHtml = $this->generatePollHtml($poll);
                broadcast(new PollStatusChanged($pollHtml, 'active'))->toOthers();
            } else {
                broadcast(new PollStatusChanged(null, 'inactive'))->toOthers();
            }
        }

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll updated successfully');
    }

    public function destroy($id)
    {
        try {
            $poll = Poll::findOrFail($id);

            // Broadcast poll closed before deletion
            if ($poll->is_active) {
                broadcast(new PollStatusChanged(null, 'inactive'))->toOthers();
            }

            $poll->delete();

            return response()->json([
                'success' => true,
                'message' => 'Poll deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete poll'
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $poll = Poll::with('options')->findOrFail($id);
            $poll->is_active = !$poll->is_active;
            $poll->save();

            // Broadcast status change
            if ($poll->is_active) {
                $pollHtml = $this->generatePollHtml($poll);
                broadcast(new PollStatusChanged($pollHtml, 'active'))->toOthers();
            } else {
                broadcast(new PollStatusChanged(null, 'inactive'))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => 'Poll status updated successfully',
                'is_active' => $poll->is_active
            ]);
        } catch (\Exception $e) {
            \Log::error('Toggle status failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePollHtml($poll)
    {
        $html = '<div class="poll-container">';
        $html .= '<h4>' . e($poll->question) . '</h4>';
        $html .= '<form id="poll-form">';
        $html .= '<input type="hidden" name="poll_id" value="' . $poll->id . '">';

        foreach ($poll->options as $option) {
            $html .= '<div class="form-check mb-2">';
            $html .= '<input class="form-check-input" type="radio" name="poll_option" value="' . $option->id . '" id="option_' . $option->id . '">';
            $html .= '<label class="form-check-label" for="option_' . $option->id . '">';
            $html .= e($option->option_text);
            $html .= '</label>';
            $html .= '</div>';
        }

        $html .= '<button type="submit" class="btn btn-danger mt-3">Submit Vote</button>';
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }
}
