<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Transaction;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    //show create device form
    public function create()
    {
        return view('devices.create');
    }

    //store new device
    public function store(Request $request)
    {
        $request->validate([
            'device_type' => 'required',
        ]);

        Device::create([
            'device_type' => $request->device_type,
            'device_status' => 'Inactive',
            'device_last_activity' => now(),
        ]);

        return redirect()->route('devices.index')->with('success', 'Device created successfully');
    }

    //activate device
    public function activate(Device $device)
    {
        if ($device->device_status == 'Active') {
            return redirect()->route('devices.index');
        }

        $device->device_status = 'Active';
        $device->save();

        //start device simulation
        $this->startDeviceSimulation($device);

        return redirect()->route('devices.index')->with('success', 'Device activated successfully');
    }

    //deactivate device
    public function deactivate(Device $device)
    {
        if ($device->device_status == 'Inactive') {
            return redirect()->route('devices.index');
        }

        $device->device_status = 'Inactive';
        $device->save();

        return redirect()->route('devices.index')->with('success', 'Device deactivated successfully');
    }
    //simulate devices subprocess
    private function startDeviceSimulation(Device $device)
    {
        // Dispatch the first transaction job immediately
        \App\Jobs\GenerateDeviceTransaction::dispatch($device->id, $device->device_type);
    }
}