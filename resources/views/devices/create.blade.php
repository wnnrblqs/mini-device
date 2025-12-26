@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Create New Device</h2>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('devices.store') }}">
    @csrf
    <div class="mb-3">
        <label for="device_type" class="form-label">Device Type</label>
        <select class="form-control" id="device_type" name="device_type" required>
            <option value="">Select a device type</option>
            <option value="Access Controller">Access Controller</option>
            <option value="Face Recognition Reader">Face Recognition Reader</option>
            <option value="ANPR">ANPR</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Create Device</button>
</form>
</div>
@endsection