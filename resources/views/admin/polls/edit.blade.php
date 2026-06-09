@extends('admin.layouts.master')

@section('title', 'Edit Poll')
@section('page-title', 'Edit Poll')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Edit Poll</h5>
    <a href="{{ route('admin.polls.index') }}" class="btn btn-secondary btn-sm float-end">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.polls.update', $poll->id) }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Poll Question</label>
        <textarea name="question" class="form-control" rows="3" required>{{ $poll->question }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Options</label>
        <div id="options-container">
          @foreach($poll->options as $index => $option)
          <div class="input-group mb-2">
            <input type="hidden" name="option_ids[]" value="{{ $option->id }}">
            <input type="text" name="options[]" class="form-control" value="{{ $option->option_text }}" required>
            <button type="button" class="btn btn-danger remove-option" {{ $poll->options->count() <= 2 ? 'style="display: none;"' : '' }}>
              <i class="fas fa-trash"></i>
            </button>
          </div>
          @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" id="add-option">
          <i class="fas fa-plus"></i> Add Option
        </button>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $poll->is_active ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
      </div>

      <button type="submit" class="btn btn-primary">Update Poll</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $('#add-option').on('click', function() {
      var newIndex = $('.input-group').length + 1;
      var newOption = `
            <div class="input-group mb-2">
                <input type="hidden" name="option_ids[]" value="">
                <input type="text" name="options[]" class="form-control" placeholder="Option ${newIndex}" required>
                <button type="button" class="btn btn-danger remove-option">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
      $('#options-container').append(newOption);
      $('.remove-option').show();
    });

    $(document).on('click', '.remove-option', function() {
      if ($('.input-group').length > 2) {
        $(this).closest('.input-group').remove();
      }

      if ($('.input-group').length <= 2) {
        $('.remove-option').hide();
      }
    });
  });
</script>
@endpush