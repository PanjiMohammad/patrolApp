<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use File;
use App\Security;
use App\Location;
use App\Schedule;
use App\Absence;
use App\IncidentReport;
use App\User;

// additional
use DataTables;
use PDF;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    public function index()
    {
        return view('security.absence.index');
    }

    public function getDatatables(Request $request)
    {
        // get id
        $securityIds = auth()->guard('security')->user()->id;

        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        // Apply the whereBetween query on the shift_date with the correct format
        $schedules = Schedule::where('security_id', $securityIds)
            ->with(['security' => function($q) use ($securityIds) {
                $q->where('id', $securityIds);
            }, 
            'absence' => function($q) use ($securityIds) {
                $q->where('security_id', $securityIds);
            }, 'incident'])
            ->whereBetween('shift_date', [Carbon::parse($start)->format('Y-m-d'), Carbon::parse($end)->format('Y-m-d')])
            ->orderBy('shift_date', 'DESC')->get();

        return DataTables::of($schedules)
            ->addColumn('dates', function($row) {
                return Carbon::parse($row->shift_date)->locale('id')->translatedFormat('l, d M Y');
            })
            ->addColumn('shift', function($row) {
                // Tentukan shift catatan
                $start = Carbon::parse($row->start_time)->format('H:i');
                $end = Carbon::parse($row->end_time)->format('H:i');
                
                if ($start === '07:00' && $end === '15:00') {
                    $shift = '1';
                } elseif ($start === '15:00' && $end === '23:00') {
                    $shift = '2';
                } elseif ($start === '23:00' && $end === '07:00') {
                    $shift = '3';
                } else {
                    $shift = '-';
                }

                return "{$shift}";
            })
            ->addColumn('details', function($row) {
                // Cek apakah ada laporan kejadian
                if ($row->absence->isEmpty()) {
                    return '<span class="font-italic font-weight-bold">Tidak ada catatan</span>'; // Tidak ada laporan, langsung return
                }

                // Proses laporan kejadian dan susun detail
                return $row->absence->map(function($absen) {
                    $time = Carbon::parse($absen->absent_time)->format('H.i A');
                    $location = $absen->location ? ' - ' . $absen->location->name : '';
                    $note = ucfirst($absen->note) ?? '-';
            
                    // Susun detail laporan
                    return <<<HTML
                        <div>
                            {$time} {$location}<br>
                            <em><span class="font-weight-bold">Catatan:</span></em> {$note}<br>
                        </div>
                    HTML;
                })->implode('<hr>');
                // $details = [];
            
                // foreach ($row->absence as $absen) {
                //     $time = Carbon::parse($absen->absent_time)->format('H.i A');
                //     $location = $absen->location ? ' - ' . $absen->location->name : '';
                //     $note = $absen->note ?? '-';
            
                //     $details[] = <<<HTML
                //         <div>
                //             Jam : {$time} {$location}<br>
                //             <em>Catatan:</em> {$note}<br>
                //         </div>
                //     HTML;
                // }
            
                // return implode('<hr>', $details);
            })
            ->addColumn('incidentReports', function($row) {
                // Cek apakah ada laporan kejadian
                if ($row->incident->isEmpty()) {
                    return '<span class="font-italic font-weight-bold">Tidak ada catatan</span>'; // Tidak ada laporan, langsung return
                }
            
                // Proses laporan kejadian dan susun detail
                return $row->incident->map(function($incident) {
                    $urgencyLabel = $incident->urgency ?? '-';
                    $location = $incident->location ? $incident->location->name : '-';
                    $incidentDescription = $incident->description ?? '-';
            
                    // Tentukan class badge berdasarkan nilai urgency
                    $badgeClass = match(strtolower($urgencyLabel)) {
                        'tinggi' => 'badge bg-danger',
                        'sedang' => 'badge bg-warning text-dark',
                        'rendah' => 'badge bg-success',
                        default => 'badge bg-secondary',
                    };
            
                    // Susun detail laporan
                    return <<<HTML
                        <div>
                            Lokasi: {$location}<br>
                            Prioritas: <span class="{$badgeClass}">{$urgencyLabel}</span><br>
                            <em><span class="font-italic font-weight-bold">Keterangan:</span></em> {$incidentDescription}
                        </div>
                    HTML;
                })->implode('<hr>'); // Gabungkan hasil dengan pemisah garis horizontal
            })
            ->addColumn('action', function ($row) {
                // Waktu saat ini dengan zona waktu Asia/Jakarta
                $now = Carbon::now('Asia/Jakarta'); 

                // Tanggal dan waktu shift
                $shiftDate = Carbon::parse($row->shift_date);
                $startTime = Carbon::parse($row->start_time);
                $endTime = Carbon::parse($row->end_time);

                // Pastikan shift_date adalah hari ini
                $isToday = $shiftDate->isToday();

                // Cek apakah waktu sekarang berada dalam rentang shift (start_time sampai end_time)
                $isWithinShiftTime = $now->between($startTime, $endTime, true);

                // Jika bukan hari ini atau waktu sekarang di luar rentang shift, disable tombol
                $disabled = (!$isToday || !$isWithinShiftTime) ? 'disabled style="pointer-events: none; opacity: 0.6;"' : '';

                return '
                    <a href="' . route('absence.detail', $row->id) . '" class="btn btn-sm btn-success" title="Absen" >
                        <i class="fas fa-user-check"></i>
                    </a>
                    <a href="'. route('absence.newDetail', $row->id) .'" class="btn btn-sm btn-warning" title="Laporan Kejadian">
                        <i class="fas fa-exclamation-triangle"></i>
                    </a>
                ';
            })
            ->rawColumns(['dates', 'shift', 'details', 'incidentReports', 'action'])
            ->make(true);
    }

    public function detail($id)
    {
        $data = Schedule::where('id', $id)->first();
        $location = Location::get();
        return view('security.absence.detail', compact('data', 'location'));
    }

    public function postAbsence(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required',
            'location_point' => $request->status === 'Hadir' && $request->location_point ? 'exists:locations,id' : 'nullable',
            'status' => 'required',
            'photo' => 'nullable|image|max:2048',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        try {
            $userId = auth()->guard('security')->user()->id;
        
            // Validasi lokasi termasuk dalam ruang kerja security jika status hadir dan lokasi ada
            if ($request->status === 'Hadir' && $request->location_point) {
                $allowedLocation = Location::where('id', $request->location_point)->exists();
                if (!$allowedLocation) {
                    return response()->json(['error' => 'Lokasi tidak termasuk dalam ruang kerja Anda.'], 403);
                }
            }
        
            // Coba cari jadwal sesuai lokasi dan tanggal
            $schedule = Schedule::where('security_id', $userId)
                ->where('shift_date', $request->shift_date_2)
                ->first();
        
            // Inisialisasi filename
            $filename = null;
        
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/absences/');
                $file->move($destinationPath, $filename);
            }
        
            // Jika status "Hadir" dan lokasi tidak dipilih, set location_point_id ke null
            $locationPointId = $request->status === 'Hadir' && !$request->location_point ? null : $request->location_point;
        
            // Create the absence entry
            $absence = Absence::create([
                'schedule_id' => $request->schedule_id,
                'location_point_id' => $locationPointId, // Lokasi bisa null jika status hadir dan lokasi tidak dipilih
                'security_id' => $userId,
                'absent_time' => Carbon::now('Asia/Jakarta'),
                'status' => $request->status,
                'photo' => $filename, // Bisa null jika tidak ada foto
                'note' => $request->note,
                'is_scheduled' => $schedule ? true : false,
            ]);
        
            return response()->json(['success' => 'Berhasil melakukan absen'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detailNew($id)
    {
        $data = Schedule::where('id', $id)->first();
        $location = Location::get();
        return view('security.absence.incidentreport', compact('data', 'location'));
    }

    public function postNewAbsence(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required',
            'location_point' => 'required',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            'urgency' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        if ($validator->fails()) {
            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
            }
        }

        try {
            $userId = auth()->guard('security')->user()->id;

            // Coba cari jadwal sesuai lokasi dan tanggal
            $schedule = Schedule::where('security_id', $userId)
                ->where('shift_date', $request->schedule_date)
                ->first();

            // Inisialisasi filename
            $filename = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/incidents/');
                $file->move($destinationPath, $filename);
            }

            // Create the location
            $incidents = IncidentReport::create([
                'schedule_id' => $request->schedule_id,
                'location_point_id' => $request->location_point,
                'description' => $request->description,
                'photo' => $filename,
                'urgency' => $request->urgency,
                'is_scheduled' => $schedule ? true : false,
            ]);

            return response()->json(['success' => 'Berhasil melaporkan kejadian'], 200);

            // return response()->json(['success' => 'Data baru berhasil tersimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function reportPdf($daterange)
    {
        try {
            // Memisahkan rentang tanggal
            $date = explode('+', $daterange);
            $start = Carbon::parse($date[0])->format('Y-m-d');
            $end = Carbon::parse($date[1])->format('Y-m-d');

            // get id
            $securityIds = auth()->guard('security')->user()->id;

            // Mendapatkan jadwal patroli sesuai rentang tanggal
            $schedules = Schedule::with([
                'security', 
                'absence',
                'incident'
            ])
            ->whereBetween('shift_date', [$start, $end])
            ->where('security_id', $securityIds)
            ->orderBy('shift_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

            $startpdf = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
            $endpdf = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

            // Format dan sortir jadwal
            $groupedSchedules = $this->formatScheduleDetails($schedules);

            // Menghasilkan PDF dengan data yang telah dikumpulkan
            $pdf = PDF::loadView('security.absence.report_pdf', compact('groupedSchedules', 'startpdf', 'endpdf'));
            
            // Menyimpan PDF sementara di server
            $fileName = 'Laporan Patroli Periode ' . $startpdf . ' sampai ' . $endpdf . '.pdf';
            $filePath = storage_path('app/public/reports/' . $fileName);

            // Simpan PDF
            $pdf->save($filePath);

            // Jika menggunakan AJAX, mengembalikan respons dengan URL file
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF berhasil diunduh',
                    'file_url' => asset('storage/reports/' . $fileName)
                ], 200);
            }

            // Download PDF langsung jika tidak menggunakan AJAX
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            // Menangani error dengan respons error
            return response()->json(['success' => false, 'message' => 'Gagal menghasilkan PDF.', 'error' => $e->getMessage()], 500);
        }
    }

    // untuk sortir
    private function formatScheduleDetails($schedules)
    {
        // Sort terlebih dahulu berdasarkan shift_date dan start_time (untuk urutan shift)
        $schedules = $schedules->sort(function ($a, $b) {
            $dateA = Carbon::parse($a->shift_date);
            $dateB = Carbon::parse($b->shift_date);

            if ($dateA->eq($dateB)) {
                return Carbon::parse($a->start_time)->timestamp - Carbon::parse($b->start_time)->timestamp;
            }

            return $dateA->lt($dateB) ? -1 : 1;
        });

        $shiftedData = [];

        foreach ($schedules as $schedule) {
            $start = Carbon::parse($schedule->start_time)->format('H:i');
            $end = Carbon::parse($schedule->end_time)->format('H:i');

            if ($start === '07:00' && $end === '15:00') {
                $shift = '1';
            } elseif ($start === '15:00' && $end === '23:00') {
                $shift = '2';
            } elseif ($start === '23:00' && $end === '07:00') {
                $shift = '3';
            } else {
                $shift = '0';
            }

            $securityName = $schedule->security ? strtolower($schedule->security->name) : '-';
            $key = sprintf('%s_%s_%s', $schedule->shift_date, str_pad($shift, 2, '0', STR_PAD_LEFT), $securityName);

            $shiftedData[$key][] = [
                'shift_date' => $schedule->shift_date,
                'shift'      => $shift,
                'security'   => $schedule->security,
                'schedule'   => $schedule,
                'absences'   => $schedule->absence ?? [],
                'incidents'  => $schedule->incident ?? [],
            ];
        }

        return $shiftedData;
    }

}
