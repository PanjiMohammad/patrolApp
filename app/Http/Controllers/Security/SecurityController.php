<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Security;
use App\Location;
use App\Schedule;
use App\User;

// additional
use Carbon\Carbon;

class SecurityController extends Controller
{
    public function index()
    {        
        return view('security.home');
    }

    public function getEvents()
    {
        $security = auth()->guard('security')->user();

        if (!$security) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $events = Schedule::with('security') // pastikan relasi ada
            ->where('security_id', $security->id)
            ->get()
            ->map(function ($schedule) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);

                $shift = match (true) {
                    $start->format('H:i') === '07:00' && $end->format('H:i') === '15:00' => 'Shift 1',
                    $start->format('H:i') === '15:00' && $end->format('H:i') === '23:00' => 'Shift 2',
                    $start->format('H:i') === '23:00' && $end->format('H:i') === '07:00' => 'Shift 3',
                    default => '-',
                };

                return [
                    'title' => strtok($schedule->security->name, ' ') . ' | ' . $shift,
                    'start' => $schedule->shift_date . 'T' . $start->format('H:i:s'),
                    'end' => $schedule->shift_date . 'T' . $end->format('H:i:s'),
                ];
            });

        return response()->json($events);
    }

    public function accountSetting($id){
        $security = auth()->guard('security')->user();
        return view('security.setting.setting', compact('security'));
    }
    
    public function postAccountSetting(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'phone_number' => 'required',
                'address' => 'required',
                'password' => 'nullable|string'
            ]);

            $security = Security::findOrFail($request->security_id);

            $data = $request->only(['name', 'email', 'phone_number', 'address']);

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $security->update($data);

            return response()->json(['success' => 'Profil berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
