@extends('admin.layouts.master')

@section('title', 'Create Poll')
@section('page-title', 'Create New Poll')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Create Poll</h5>
    <a href="{{ route('admin.polls.index') }}" class="btn btn-secondary btn-sm float-end">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
  <div class="card-body">
    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.polls.store') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Select Webinar</label>
        <select name="webinar_session_id" class="form-control @error('webinar_session_id') is-invalid @enderror" required>
          <option value="">Select Webinar</option>
          @foreach($webinars as $webinar)
          <option value="{{ $webinar->id }}" {{ old('webinar_session_id') == $webinar->id ? 'selected' : '' }}>
            {{ $webinar->title }}
          </option>
          @endforeach
        </select>
        @error('webinar_session_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Poll Question</label>
        <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="3" required placeholder="Enter your poll question...">{{ old('question') }}</textarea>
        @error('question')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Options (Minimum 2) - Select the correct answer</label>
        <div id="options-container">
          @php
          $oldOptions = old('options', ['', '']);
          @endphp
          @foreach($oldOptions as $index => $oldOption)
          <div class="input-group mb-2 option-group">
            <div class="input-group-text">
              <input type="radio" name="correct_option" value="{{ $index }}" class="form-check-input mt-0" {{ old('correct_option') == $index ? 'checked' : '' }} required>
            </div>
            <input type="text" name="options[]" class="form-control" placeholder="Option {{ $index+1 }}" value="{{ $oldOption }}" required>
            @if($index >= 2)
            <button type="button" class="btn btn-danger remove-option">
              <i class="fas fa-trash"></i>
            </button>
            @endif
          </div>
          @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" id="add-option">
          <i class="fas fa-plus"></i> Add Option
        </button>
        @error('options')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
        @error('correct_option')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : 'checked' }}>
        <label class="form-check-label" for="is_active">Active (Show poll immediately)</label>
      </div>

      <button type="submit" class="btn btn-primary">Create Poll</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    var optionCount = {
      {
        count(old('options', ['', '']))
      }
    };

    $('#add-option').on('click', function() {
      optionCount++;
      var newOption = `
        <div class="input-group mb-2 option-group">
          <div class="input-group-text">
            <input type="radio" name="correct_option" value="${optionCount-1}" class="form-check-input mt-0">
          </div>
          <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}" required>
          <button type="button" class="btn btn-danger remove-option">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      `;
      $('#options-container').append(newOption);

      // Update radio button values after adding
      $('.option-group').each(function(index) {
        $(this).find('input[type="radio"]').val(index);
      });
    });

    $(document).on('click', '.remove-option', function() {
      if ($('.option-group').length > 2) {
        $(this).closest('.option-group').remove();
        optionCount--;

        // Update radio button values after removal
        $('.option-group').each(function(index) {
          $(this).find('input[type="radio"]').val(index);
        });
      }
    });
  });
</script>
@endpush