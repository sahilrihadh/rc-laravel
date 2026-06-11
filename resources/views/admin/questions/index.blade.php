@extends('admin.layouts.master')

@section('title', 'Questions')
@section('page-title', 'Questions Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Questions</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">All Questions & Answers</h5>
  </div>
  <div class="card-body">
    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Questions</h5>
            <h3 class="mb-0">{{ $stats['total'] }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Answered</h5>
            <h3 class="mb-0">{{ $stats['answered'] }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <h5 class="card-title">Pending</h5>
            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="alert alert-info" style="display:none;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <i class="fas fa-check-circle"></i> <span id="selectedCount">0</span> questions selected
        </div>
        <div>
          <button class="btn btn-sm btn-success" id="selectAllBtn">Select All</button>
          <button class="btn btn-sm btn-secondary" id="clearSelectionBtn">Clear</button>
          <button class="btn btn-sm btn-danger" id="confirmBulkDelete">
            <i class="fas fa-trash"></i> Delete Selected
          </button>
        </div>
      </div>
    </div>

    <!-- Questions Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="questionsTable">
        <thead>
          <tr>
            <th width="50">
              <input type="checkbox" id="selectAllCheckbox">
            </th>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th width="30%">Question</th>
            <th width="30%">Answer / Reply</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($questions as $question)
          <tr class="{{ !$question->is_answered ? 'table-warning' : '' }}">
            <td>
              <input type="checkbox" class="record-checkbox" value="{{ $question->id }}">
            </td>
            <td>{{ $question->id }}</td>
            <td>
              <strong>{{ $question->user->full_name ?? 'N/A' }}</strong>
            </td>
            <td>{{ $question->user->email_id ?? 'N/A' }}</td>
            <td>
              <div style="word-wrap: break-word;">
                {{ $question->question_text ?? $question->question_input }}
              </div>
            </td>
            <td>
              @if($question->is_answered)
                <div class="answer-box p-2 bg-light rounded">
                  <p class="mb-0">{{ $question->answer_text }}</p>
                </div>
              @else
                <span class="badge bg-warning">Awaiting answer</span>
              @endif
            </td>
            <td>
              @if($question->is_answered)
                <span class="badge bg-success">Answered</span>
              @else
                <span class="badge bg-warning">Pending</span>
              @endif
            </td>
            <td>{{ $question->created_at->format('d M Y h:i A') }}</td>
            <td>
              <div class="btn-group" role="group">
                @if(!$question->is_answered)
                  <button class="btn btn-sm btn-primary answer-question" 
                          data-id="{{ $question->id }}"
                          data-question="{{ $question->question_text ?? $question->question_input }}"
                          data-user="{{ $question->user->full_name ?? 'N/A' }}">
                    <i class="fas fa-reply"></i> Reply
                  </button>
                @endif
                <button class="btn btn-sm btn-info view-question" 
                        data-id="{{ $question->id }}"
                        data-question="{{ $question->question_text ?? $question->question_input }}"
                        data-user="{{ $question->user->full_name ?? 'N/A' }}"
                        data-email="{{ $question->user->email_id ?? 'N/A' }}"
                        data-answer="{{ $question->answer_text ?? '' }}"
                        data-status="{{ $question->is_answered ? 'Answered' : 'Pending' }}">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-question" 
                        data-id="{{ $question->id }}"
                        data-question="{{ Str::limit($question->question_text ?? $question->question_input, 50) }}">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center">No questions found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $questions->links() }}
    </div>
  </div>
</div>

<!-- Answer Modal -->
<div class="modal fade" id="answerModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reply to Question</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">User:</label>
          <p class="fw-bold" id="modalUserName"></p>
        </div>
        <div class="mb-3">
          <label class="form-label">Question:</label>
          <div class="p-3 bg-light rounded" id="modalQuestionText"></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Your Answer:</label>
          <textarea id="answerText" class="form-control" rows="5" placeholder="Type your answer here..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitAnswer">
          <i class="fas fa-paper-plane"></i> Submit Answer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Question Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">User:</label>
          <p class="fw-bold" id="viewUserName"></p>
        </div>
        <div class="mb-3">
          <label class="form-label">Email:</label>
          <p id="viewUserEmail"></p>
        </div>
        <div class="mb-3">
          <label class="form-label">Status:</label>
          <p id="viewStatus"></p>
        </div>
        <div class="mb-3">
          <label class="form-label">Submitted On:</label>
          <p id="viewSubmittedAt"></p>
        </div>
        <div class="mb-3">
          <label class="form-label">Question:</label>
          <div class="p-3 bg-light rounded" id="viewQuestionText"></div>
        </div>
        <div class="mb-3" id="answerSection" style="display:none;">
          <label class="form-label">Answer:</label>
          <div class="p-3 bg-success text-white rounded" id="viewAnswerText"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedIds = [];
    let currentQuestionId = null;
    
    // Answer button click
    $('.answer-question').on('click', function() {
        currentQuestionId = $(this).data('id');
        $('#modalUserName').text($(this).data('user'));
        $('#modalQuestionText').text($(this).data('question'));
        $('#answerText').val('');
        $('#answerModal').modal('show');
    });
    
    // View button click
    $('.view-question').on('click', function() {
        $('#viewUserName').text($(this).data('user'));
        $('#viewUserEmail').text($(this).data('email'));
        $('#viewQuestionText').text($(this).data('question'));
        $('#viewStatus').html($(this).data('status') === 'Answered' ? '<span class="badge bg-success">Answered</span>' : '<span class="badge bg-warning">Pending</span>');
        $('#viewSubmittedAt').text($(this).closest('tr').find('td:eq(7)').text());
        
        if ($(this).data('answer')) {
            $('#viewAnswerText').text($(this).data('answer'));
            $('#answerSection').show();
        } else {
            $('#answerSection').hide();
        }
        
        $('#viewModal').modal('show');
    });
    
    // Submit Answer
    $('#submitAnswer').on('click', function() {
        var answerText = $('#answerText').val().trim();
        
        if (!answerText) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Answer',
                text: 'Please enter an answer before submitting.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        Swal.fire({
            title: 'Submit Answer?',
            text: "This answer will be visible to the user.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/questions/' + currentQuestionId + '/answer',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        answer_text: answerText
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to submit answer',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
    
    // Select All checkbox functionality
    $('#selectAllCheckbox').on('change', function() {
        $('.record-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedRecords();
    });
    
    // Individual checkbox change
    $(document).on('change', '.record-checkbox', function() {
        updateSelectedRecords();
    });
    
    // Update selected records
    function updateSelectedRecords() {
        selectedIds = [];
        $('.record-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length > 0) {
            $('#bulkActionsBar').show();
            $('#selectedCount').text(selectedIds.length);
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkActionsBar').hide();
            $('#bulkDeleteBtn').hide();
        }
        
        var totalCheckboxes = $('.record-checkbox').length;
        var checkedCheckboxes = $('.record-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
    }
    
    // Select All button
    $('#selectAllBtn').on('click', function() {
        $('.record-checkbox').prop('checked', true);
        updateSelectedRecords();
    });
    
    // Clear Selection button
    $('#clearSelectionBtn').on('click', function() {
        $('.record-checkbox').prop('checked', false);
        updateSelectedRecords();
    });
    
    // Bulk Delete
    $('#confirmBulkDelete, #bulkDeleteBtn').on('click', function() {
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select records to delete',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} question(s). This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/questions/bulk-delete',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete records',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
    
    // Delete single record
    $(document).on('click', '.delete-question', function() {
        var id = $(this).data('id');
        var questionText = $(this).data('question');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete question: \"" + questionText + "\"?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/questions/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete question',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
    
});
</script>
@endpush