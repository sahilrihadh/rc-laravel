<?php $__env->startSection('title', 'Questions'); ?>
<?php $__env->startSection('page-title', 'Questions Management'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active">Questions</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
            <h3 class="mb-0"><?php echo e($stats['total']); ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Answered</h5>
            <h3 class="mb-0"><?php echo e($stats['answered']); ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <h5 class="card-title">Pending</h5>
            <h3 class="mb-0"><?php echo e($stats['pending']); ?></h3>
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
            <th width="150">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="<?php echo e(!$question->is_answered ? 'table-warning' : ''); ?>" id="question-row-<?php echo e($question->id); ?>">
            <td>
              <input type="checkbox" class="record-checkbox" value="<?php echo e($question->id); ?>">
            </td>
            <td><?php echo e($question->id); ?></td>
            <td>
              <strong><?php echo e($question->user->full_name ?? $question->user->name ?? 'N/A'); ?></strong>
            </td>
            <td><?php echo e($question->user->email_id ?? $question->user->email ?? 'N/A'); ?></td>
            <td>
              <div style="word-wrap: break-word;">
                <?php echo e($question->question_text ?? $question->question_input); ?>

              </div>
            </td>
            <td id="answer-cell-<?php echo e($question->id); ?>">
              <?php if($question->is_answered): ?>
                <div class="answer-box p-2 bg-light rounded">
                  <p class="mb-0"><?php echo e(Str::limit($question->answer_text, 100)); ?></p>
                </div>
              <?php else: ?>
                <span class="badge bg-warning">Awaiting answer</span>
              <?php endif; ?>
             </td>
            <td>
              <span class="badge status-badge-<?php echo e($question->id); ?> <?php echo e($question->is_answered ? 'bg-success' : 'bg-warning'); ?>">
                <?php echo e($question->is_answered ? 'Answered' : 'Pending'); ?>

              </span>
            </td>
            <td><?php echo e($question->created_at->format('d M Y h:i A')); ?></td>
            <td>
              <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-primary answer-question" 
                        data-id="<?php echo e($question->id); ?>"
                        data-question="<?php echo e($question->question_text ?? $question->question_input); ?>"
                        data-user="<?php echo e($question->user->full_name ?? $question->user->name ?? 'N/A'); ?>"
                        data-current-answer="<?php echo e($question->answer_text ?? ''); ?>">
                  <i class="fas fa-<?php echo e($question->is_answered ? 'edit' : 'reply'); ?>"></i> 
                  <?php echo e($question->is_answered ? 'Edit' : 'Reply'); ?>

                </button>
                <button class="btn btn-info view-question" 
                        data-id="<?php echo e($question->id); ?>"
                        data-question="<?php echo e($question->question_text ?? $question->question_input); ?>"
                        data-user="<?php echo e($question->user->full_name ?? $question->user->name ?? 'N/A'); ?>"
                        data-email="<?php echo e($question->user->email_id ?? $question->user->email ?? 'N/A'); ?>"
                        data-answer="<?php echo e($question->answer_text ?? ''); ?>"
                        data-status="<?php echo e($question->is_answered ? 'Answered' : 'Pending'); ?>">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger delete-question" 
                        data-id="<?php echo e($question->id); ?>"
                        data-question="<?php echo e(Str::limit($question->question_text ?? $question->question_input, 50)); ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="9" class="text-center">No questions found</td>
          </tr>
          <?php endif; ?>
        </tbody>
      能有
    </div>

    <div class="d-flex justify-content-center mt-4">
      <?php echo e($questions->links()); ?>

    </div>
  </div>
</div>

<!-- Answer Modal -->
<div class="modal fade" id="answerModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="answerModalTitle">Reply to Question</h5>
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
          <label class="form-label" id="answerLabel">Your Answer:</label>
          <textarea id="answerText" class="form-control" rows="5" placeholder="Type your answer here..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitAnswer">
          <i class="fas fa-paper-plane"></i> <span id="submitBtnText">Submit Answer</span>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let selectedIds = [];
    let currentQuestionId = null;
    let isEditing = false;
    
    // Answer button click (works for both Reply and Edit)
    $('.answer-question').on('click', function() {
        currentQuestionId = $(this).data('id');
        const userName = $(this).data('user');
        const questionText = $(this).data('question');
        const currentAnswer = $(this).data('current-answer');
        const hasAnswer = currentAnswer && currentAnswer.trim() !== '';
        
        $('#modalUserName').text(userName);
        $('#modalQuestionText').text(questionText);
        
        if (hasAnswer) {
            $('#answerModalTitle').text('Edit Answer');
            $('#answerLabel').text('Edit Your Answer:');
            $('#answerText').val(currentAnswer);
            $('#submitBtnText').text('Update Answer');
            isEditing = true;
        } else {
            $('#answerModalTitle').text('Reply to Question');
            $('#answerLabel').text('Your Answer:');
            $('#answerText').val('');
            $('#submitBtnText').text('Submit Answer');
            isEditing = false;
        }
        
        $('#answerModal').modal('show');
    });
    
    // View button click
    $('.view-question').on('click', function() {
        $('#viewUserName').text($(this).data('user'));
        $('#viewUserEmail').text($(this).data('email'));
        $('#viewQuestionText').text($(this).data('question'));
        $('#viewStatus').html($(this).data('status') === 'Answered' ? '<span class="badge bg-success">Answered</span>' : '<span class="badge bg-warning">Pending</span>');
        $('#viewSubmittedAt').text($(this).closest('tr').find('td:eq(7)').text());
        
        const answer = $(this).data('answer');
        if (answer && answer.trim() !== '') {
            $('#viewAnswerText').text(answer);
            $('#answerSection').show();
        } else {
            $('#answerSection').hide();
        }
        
        $('#viewModal').modal('show');
    });
    
    // Submit/Update Answer
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
        
        const actionText = isEditing ? 'update' : 'submit';
        
        Swal.fire({
            title: isEditing ? 'Update Answer?' : 'Submit Answer?',
            text: isEditing ? "This will update the existing answer." : "This answer will be visible to the user.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: isEditing ? 'Yes, update it!' : 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const $btn = $('#submitAnswer');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                $.ajax({
                    url: '/admin/questions/' + currentQuestionId + '/answer',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        answer_text: answerText
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the table row without reload
                            updateQuestionRow(currentQuestionId, answerText);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#answerModal').modal('hide');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to ' + actionText + ' answer';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            }
        });
    });
    
    // Update question row dynamically
    function updateQuestionRow(questionId, answerText) {
        const $row = $('#question-row-' + questionId);
        const $answerCell = $('#answer-cell-' + questionId);
        const $statusBadge = $('.status-badge-' + questionId);
        
        // Update answer cell
        $answerCell.html(`
            <div class="answer-box p-2 bg-light rounded">
                <p class="mb-0">${escapeHtml(answerText.substring(0, 100))}${answerText.length > 100 ? '...' : ''}</p>
            </div>
        `);
        
        // Update status badge
        $statusBadge.removeClass('bg-warning').addClass('bg-success');
        $statusBadge.text('Answered');
        
        // Update the action button
        const $actionBtn = $row.find('.answer-question');
        $actionBtn.html('<i class="fas fa-edit"></i> Edit');
        $actionBtn.data('current-answer', answerText);
        
        // Remove warning class from row
        $row.removeClass('table-warning');
        
        // Show success toast
        toastr.success('Answer updated successfully!', 'Success');
    }
    
    // Escape HTML helper
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
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
        } else {
            $('#bulkActionsBar').hide();
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
    $('#confirmBulkDelete').on('click', function() {
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
                        _token: '<?php echo e(csrf_token()); ?>',
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
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#question-row-' + id).fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/admin/questions/index.blade.php ENDPATH**/ ?>