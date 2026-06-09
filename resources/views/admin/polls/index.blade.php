@extends('admin.layouts.master')

@section('title', 'Polls')
@section('page-title', 'Polls Management')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>All Polls</h5>
    <a href="{{ route('admin.polls.create') }}" class="btn btn-primary btn-sm float-end">
      <i class="fas fa-plus"></i> Create Poll
    </a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Question</th>
            <th>Options</th>
            <th>Total Votes</th>
            <th>Status</th>
            <th>Expires At</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($polls as $poll)
          <tr>
            <td>{{ $poll->id }}</td>
            <td>{{ Str::limit($poll->question, 60) }}</td>
            <td>{{ $poll->options->count() }} options</td>
            <td>{{ $poll->votes->count() }}</td>
            <td>
              <span class="badge {{ $poll->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $poll->is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>{{ $poll->expires_at ? $poll->expires_at->format('d M Y') : 'Never' }}</td>
            <td>{{ $poll->created_at->format('d M Y') }}</td>
            <td>
              <button class="btn btn-sm btn-warning toggle-status" data-id="{{ $poll->id }}">
                <i class="fas {{ $poll->is_active ? 'fa-pause' : 'fa-play' }}"></i>
              </button>
              <a href="{{ route('admin.polls.edit', $poll->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              <button class="btn btn-sm btn-danger delete-poll" data-id="{{ $poll->id }}" data-name="{{ $poll->question }}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No polls found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $polls->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Toggle poll status
    $('.toggle-status').on('click', function() {
      var pollId = $(this).data('id');
      var $btn = $(this);

      // Show loading state
      $btn.html('<i class="fas fa-spinner fa-spin"></i>');
      $btn.prop('disabled', true);

      $.ajax({
        url: '/admin/polls/' + pollId + '/toggle-status',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          if (response.success) {
            // Show success message
            swal({
              title: "Success!",
              text: response.message,
              type: "success",
              timer: 1500,
              showConfirmButton: false
            });

            // Reload page after 1.5 seconds
            setTimeout(function() {
              location.reload();
            }, 1500);
          } else {
            swal({
              title: "Error!",
              text: response.message,
              type: "error",
              timer: 2000,
              showConfirmButton: false
            });
            location.reload();
          }
        },
        error: function(xhr) {
          var message = xhr.responseJSON?.message || 'Failed to update status';
          swal({
            title: "Error!",
            text: message,
            type: "error",
            timer: 2000,
            showConfirmButton: false
          });
          location.reload();
        }
      });
    });

    // Delete poll
    $('.delete-poll').on('click', function() {
      var pollId = $(this).data('id');

      swal({
        title: "Are you sure?",
        text: "You want to delete this poll?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!"
      }, function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: '/admin/polls/' + pollId,
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
                  timer: 1500,
                  showConfirmButton: false
                });
                setTimeout(function() {
                  location.reload();
                }, 1500);
              }
            },
            error: function() {
              swal({
                title: "Error!",
                text: "Failed to delete poll",
                type: "error",
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