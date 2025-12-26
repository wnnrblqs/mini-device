@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Transaction List</h2>

    <div class="mb-3">
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Back to Devices</a>
    </div>

    @if($transactions->isEmpty())
        <p>No transactions found. Activate a device to see transactions.</p>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Device</th>
                    <th>Transaction Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->device->device_type }}</td>
                        <td>{{ $transaction->transaction_details }}</td>
                        <td>{{ $transaction->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection