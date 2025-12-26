@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Device List</h2>

    <div class="mb-3">
        <a href="{{ route('devices.create') }}" class="btn btn-primary">Create New Device</a>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">View Transactions</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($devices->isEmpty())
      <p>No devices found. Create a new device to get started.</p>
    @else
      <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Device Type</th>
                <th>Device Status</th>
                <th>Last Activity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $device)
                <tr>
                    <td>{{ $device->id }}</td>
                    <td>{{ $device->device_type }}</td>
                    <td>
                        @if($device->device_status == 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $device->device_last_activity }}</td>
                    <td>
                        @if($device->device_status == 'Inactive')
                            <form method="POST" action="{{ route('devices.activate', $device) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Activate</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('devices.deactivate', $device) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Deactivate</button>
                            </form>
                        @endif
                        <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
      </table>
    @endif
</div>
@endsection