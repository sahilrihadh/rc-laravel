@extends('admin.layouts.master')

@section('title', 'Questions')
@section('page-title', 'Questions Management')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>All Questions & Answers</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Question</th>
            <th>Answer / Reply</th>
            <th>Submitted On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($questions as $question)
          <tr>
            <td>{{ $question->id }}</td>
            <td>
              {{ $question->user->full_name ?? $question->name ?? 'N/A' }}
            </td>
            <td>{{ $question->user->email_id ?? $question->email_id ?? 'N/A' }}</td>
            <td>
              <div style="max-width: 300px; word-wrap: break-word;">
                {{ $question->question ?? $question->question_input }}
              </div>
            </td>
            <td>
              <div style="max-width: 300px; word-wrap: break-word;">
                @if($question->answer)
                {{ $question->answer }}
                @else
                <span class="badge bg-warning">Not answered yet</span>
                @endif
              </div>
            </td>
            <td>{{ $question->created_at->format('d M Y h:i A') }}</td>
            <td>
              <button class="btn btn-sm btn-danger delete-question"
                data-id="{{ $question->id }}"
                data-question="{{ Str::limit($question->question ?? $question->question_input, 50) }}">
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">No questions found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
      {{ $questions->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Delete question with SweetAlert
    $('.delete-question').on('click', function(e) {
      e.preventDefault();

      var questionId = $(this).data('id');
      var questionText = $(this).data('question');

      swal({
        title: "Are you sure?",
        text: "You want to delete question: \"" + questionText + "\"?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: false
      }, function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: '/admin/questions/' + questionId,
            type: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                swal({
                  title: "Deleted!",
                  text: response.message,
                  type: "success",
                  timer: 2000,
                  showConfirmButton: false
                });

                // Remove row from table
                $('button[data-id="' + questionId + '"]').closest('tr').fadeOut(300, function() {
                  $(this).remove();

                  // If no rows left, show empty message
                  if ($('tbody tr:visible').length === 0) {
                    location.reload();
                  }
                });

                showToast(response.message, 'success');
              }
            },
            error: function(xhr) {
              var message = xhr.responseJSON?.message || "Failed to delete question";
              swal({
                title: "Error!",
                text: message,
                type: "error",
                timer: 2000,
                showConfirmButton: false
              });
              showToast(message, 'error');
            }
          });
        } else {
          swal({
            title: "Cancelled",
            text: "Question deletion cancelled",
            type: "error",
            timer: 1500,
            showConfirmButton: false
          });
        }
      });
    });

    // Toast notification function
    function showToast(message, type) {
      var bgColor = type === 'success' ? '#28a745' : '#dc3545';
      var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

      var toastHtml = `
                <div class="toast-notification" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 5px; animation: slideIn 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    <i class="fas ${icon}"></i>
                    ${message}
                </div>
            `;

      $('body').append(toastHtml);

      setTimeout(function() {
        $('.toast-notification').fadeOut(300, function() {
          $(this).remove();
        });
      }, 3000);
    }
  });
</script>

<style>
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  .table-hover tbody tr:hover {
    background-color: #f5f5f5;
  }

  .table td {
    vertical-align: middle;
  }

  .badge-warning {
    background-color: #ffc107;
    color: #000;
    padding: 5px 10px;
    border-radius: 4px;
  }
</style>
@endpush