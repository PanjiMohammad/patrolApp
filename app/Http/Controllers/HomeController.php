<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Security;
use App\Location;
use App\Schedule;
use App\User;

// additional
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $schedules = Schedule::get();
        $locations = Location::get();
        $securities = Security::get();
        
        return view('admin.home', compact('locations', 'schedules', 'securities'));
    }

    public function getEvents()
    {
        $events = Schedule::all()->map(function ($schedule) {
            $shift = '-';

            // Konversi jam ke format 24 jam untuk perbandingan
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            // Tentukan shift
            if ($start->format('H:i') === '07:00' && $end->format('H:i') === '15:00') {
                $shift = 'Shift 1';
            } elseif ($start->format('H:i') === '15:00' && $end->format('H:i') === '23:00') {
                $shift = 'Shift 2';
            } elseif ($start->format('H:i') === '23:00' && $end->format('H:i') === '07:00') {
                $shift = 'Shift 3';
            } 

            return [
                'title' => strtok($schedule->security->name, ' ') . ' | ' . $shift,
                'start' => $schedule->shift_date . 'T' . $start->format('H:i:s'),
                'end' => $schedule->shift_date . 'T' . $end->format('H:i:s'),
            ];
        });

        return response()->json($events);
    }

    public function accountSetting($id){
        $admin = auth()->guard('web')->user();
        return view('admin.setting.setting', compact('admin'));
    }
    
    public function postAccountSetting(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal, Harap periksa kembali',
                'errors' => $validator->errors(),
                'input' => $request->all()
            ], 422);
        }

        try {
            // Begin transaction
            DB::beginTransaction();

            // Find the security
            $data = User::findOrFail($request->user_id);

            // Prepare updated data
            $data = $request->only(['name', 'email', 'phone_number', 'address', 'status']);
            if ($request->filled('password')) {
                $data['password'] = $request->password; // Hash the password
            }

            $data['activate_token'] = null; // Reset activate_token

            // Update the security
            $security->update($data);

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => 'Data berhasil diperbarui', 'data' => $data], 200);
        } catch (\Exception $e) {
            // Rollback on failure
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function adminReport(){
        return view('admin.report.index');
    }
}
