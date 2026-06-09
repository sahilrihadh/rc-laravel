@extends('admin.layouts.master')

@section('title', 'Admin Users')
@section('page-title', 'Admin Users Management')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Admin Users</h5>
    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-sm float-end">Create New Admin</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection