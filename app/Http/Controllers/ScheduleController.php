<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Schedule;
use App\Security;
use App\Location;

// additional
use DataTables;
use PDF;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.schedule.index');
    }

    public function getDatatables(Request $request){
        $schedules = Schedule::orderBy('created_at', 'DESC');

        return DataTables::of($schedules)
            ->addColumn('action', function ($schedule) {
                return '
                    <a href="'. route('schedule.edit', $schedule->id) .'" class="btn btn-sm btn-primary" title="Edit Jadwal Tugas"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-schedule" data-schedule-id="'. $schedule->id .'"><span class="fa fa-trash"></span></button>
 
                    <form id="deleteForm{{ $schedule->id }}" action="'. route('schedule.destroy', $schedule->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->addColumn('name', function($schedule) {
                return optional($schedule->security)->name;
            })
            ->addColumn('shiftDate', function($schedule) {
                return Carbon::parse($schedule->shift_date)->locale('id')->translatedFormat('l, d F Y');
            })
            ->addColumn('startTime', function($schedule) {
                return Carbon::parse($schedule->start_time)->format('H:i A');
            })
            ->addColumn('endTime', function($schedule) {
                return Carbon::parse($schedule->end_time)->format('H:i A');
            })
            ->addColumn('shift', function ($schedule) {
                $start = Carbon::parse($schedule->start_time)->format('H:i');
                $end = Carbon::parse($schedule->end_time)->format('H:i');
            
                if ($start === '07:00' && $end === '15:00') {
                    return '1';
                } elseif ($start === '15:00' && $end === '23:00') {
                    return '2';
                } elseif ($start === '23:00' && $end === '07:00') {
                    return '3';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['action', 'name', 'shiftDate', 'startTime', 'endTime', 'shift'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $security = Security::orderBy('name', 'ASC')->get();
        return view('admin.schedule.create', compact('security')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'security_name' => 'required',
            'dates' => 'required',
            'times' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }
        
        try {
            $securityId = $request->security_name;
            $times = $request->times;

            foreach ($times as $date => $time) {
                // Gabungkan tanggal + jam
                $startDateTime = strtotime("$date {$time['start']}");
                $endDateTime = strtotime("$date {$time['end']}");

                // Shift malam (lewat tengah malam)
                if ($endDateTime <= $startDateTime) {
                    $endDateTime = strtotime("$date {$time['end']} +1 day");
                }

                if ($startDateTime === $endDateTime) {
                    return response()->json([
                        'error' => "Jam mulai dan selesai tidak boleh sama pada tanggal $date."
                    ], 422);
                }

                // Cek ke database: Apakah shift tumpang tindih dengan jadwal yang ada?
                $existingSchedules = Schedule::where('security_id', $securityId)
                    ->where('shift_date', $date)
                    ->get();

                foreach ($existingSchedules as $existingSchedule) {
                    $existingStart = strtotime("$date {$existingSchedule->start_time}");
                    $existingEnd = strtotime("$date {$existingSchedule->end_time}");

                    if ($existingEnd <= $existingStart) {
                        $existingEnd = strtotime("$date {$existingSchedule->end_time} +1 day");
                    }

                    // Validasi tumpang tindih
                    if ($startDateTime < $existingEnd && $existingStart < $endDateTime) {
                        return response()->json([
                            'error' => "Terdapat tumpang tindih shift pada tanggal $date dengan jadwal yang sudah ada: " .
                                date('H:i', $existingStart) . ' - ' . date('H:i', $existingEnd)
                        ], 422);
                    }
                }
            }

            // Simpan data ke DB jika semua lolos validasi
            foreach ($times as $date => $time) {
                Schedule::create([
                    'security_id' => $securityId,
                    'shift_date'  => $date,
                    'start_time'  => $time['start'],
                    'end_time'    => $time['end'],
                ]);
            }

            return response()->json(['success' => 'Jadwal satpam berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $security = Security::all();

        $existingDates = [$schedule->shift_date]; // hanya satu tanggal
        $existingTimes = [
            $schedule->shift_date => [
                [
                    'start' => $schedule->start_time,
                    'end' => $schedule->end_time,
                ]
            ]
        ];

        return view('admin.schedule.edit', compact('schedule', 'security', 'existingDates', 'existingTimes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'security_name' => 'required',
            'dates' => 'required|string',
            'times' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {

            $securityId = $request->input('security_name');
            $dates = explode(',', $request->input('dates'));
            $times = $request->input('times');

            // Validasi tidak boleh ada waktu yang tumpang tindih pada satu tanggal
            foreach ($times as $date => $entries) {
                $existingShifts = [];

                $count = count($entries);
                for ($i = 0; $i < $count; $i++) {
                    $startA = strtotime($entries[$i]['start']);
                    $endA = strtotime($entries[$i]['end']);

                    if ($startA === $endA) {
                        return response()->json([
                            'error' => "Pada tanggal $date, jam mulai dan selesai tidak boleh sama."
                        ], 422);
                    }

                    // Jika shift malam (end lebih kecil dari start), tambahkan 1 hari
                    if ($endA <= $startA) {
                        $endA += 86400;
                    }

                    // Validasi shift duplikat
                    $key = $entries[$i]['start'] . '-' . $entries[$i]['end'];
                    if (in_array($key, $existingShifts)) {
                        return response()->json([
                            'error' => "Terdapat shift dengan jam yang sama pada tanggal $date: {$entries[$i]['start']} - {$entries[$i]['end']}."
                        ], 422);
                    }
                    $existingShifts[] = $key;

                    for ($j = $i + 1; $j < $count; $j++) {
                        $startB = strtotime($entries[$j]['start']);
                        $endB = strtotime($entries[$j]['end']);
                        if ($endB <= $startB) {
                            $endB += 86400;
                        }

                        // Cek overlap dengan jadwal lain
                        if ($startA < $endB && $startB < $endA) {
                            return response()->json([
                                'error' => "Terdapat waktu tugas yang tumpang tindih pada tanggal $date."
                            ], 422);
                        }
                    }
                }

                // Validasi jadwal yang sudah ada untuk memastikan tidak ada shift dengan jam yang sama pada tanggal yang sama
                $existingSchedules = Schedule::where('security_id', $securityId)
                    ->where('shift_date', $date)
                    ->get();

                foreach ($entries as $entry) {
                    $startTime = strtotime($entry['start']);
                    $endTime = strtotime($entry['end']);

                    // Jika shift malam (end lebih kecil dari start), tambahkan 1 hari
                    if ($endTime <= $startTime) {
                        $endTime += 86400;
                    }

                    // Cek overlap dengan jadwal yang sudah ada
                    foreach ($existingSchedules as $existingSchedule) {
                        $existingStart = strtotime($existingSchedule->start_time);
                        $existingEnd = strtotime($existingSchedule->end_time);

                        // Jika shift baru tumpang tindih dengan jadwal yang sudah ada
                        if ($startTime < $existingEnd && $existingStart < $endTime) {
                            return response()->json([
                                'error' => "Terdapat tumpang tindih shift pada tanggal $date dengan shift yang sudah ada."
                            ], 422);
                        }
                    }
                }
            }

            // Hapus semua jadwal lama pada tanggal-tanggal tersebut untuk satpam ini
            Schedule::where('security_id', $securityId)
                ->whereIn('shift_date', $dates)
                ->delete();

            // Simpan ulang jadwal baru
            foreach ($times as $date => $entries) {
                foreach ($entries as $entry) {
                    Schedule::create([
                        'security_id' => $securityId,
                        'shift_date'  => $date,
                        'start_time'  => $entry['start'],
                        'end_time'    => $entry['end'],
                    ]);
                }
            }

            return response()->json(['success' => 'Jadwal berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        try {
            $schedule->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data gagal terhapus'], 500);
        }
    }

    // for report
    public function reportIndex()
    {
        return view('admin.report.index');
    }

    public function getReportDatatables(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        // Ambil data, lalu kelompokkan berdasarkan tanggal shift
        $schedules = Schedule::with(['security', 'absence', 'incident'])
            ->whereBetween('shift_date', [Carbon::parse($start)->format('Y-m-d'), Carbon::parse($end)->format('Y-m-d')])
            ->orderBy('shift_date', 'DESC')
            ->get()
            ->groupBy('shift_date'); // hasilnya Collection<shift_date => [Schedule, Schedule, ...]>


            return DataTables::of($schedules)
            ->addColumn('dates', function ($rows) {
                // Ambil tanggal dari group key
                $date = $rows->first()->shift_date ?? null;
                return $date ? Carbon::parse($date)->locale('id')->translatedFormat('l, d M Y') : '-';
            })
            ->addColumn('securities', function ($rows) {
                $shifted = [];
            
                foreach ($rows as $schedule) {
                    $start = Carbon::parse($schedule->start_time)->format('H:i');
                    $end = Carbon::parse($schedule->end_time)->format('H:i');
            
                    if ($start === '07:00' && $end === '15:00') {
                        $shift = 1;
                    } elseif ($start === '15:00' && $end === '23:00') {
                        $shift = 2;
                    } elseif ($start === '23:00' && $end === '07:00') {
                        $shift = 3;
                    } else {
                        $shift = 0;
                    }
            
                    $name = $schedule->security ? $schedule->security->name : '-';
                    $shifted[$shift] = "<li><strong>Shift {$shift} - {$name}</strong></li>";
                }
            
                ksort($shifted); // Urutkan berdasarkan key shift
            
                return '<ul>' . implode('', $shifted) . '</ul>';
            })
            ->addColumn('details', function($rows) {
                $shiftedData = [];
            
                // Susun ulang data berdasarkan shift dan nama security
                foreach ($rows as $schedule) {
                    $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                    $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
            
                    if ($start === '07:00' && $end === '15:00') {
                        $shift = '1';
                    } elseif ($start === '15:00' && $end === '23:00') {
                        $shift = '2';
                    } elseif ($start === '23:00' && $end === '07:00') {
                        $shift = '3';
                    } else {
                        $shift = '0'; // fallback
                    }
            
                    $securityName = $schedule->security ? $schedule->security->name : '-';
                    $key = sprintf('%s_%s', str_pad($shift, 2, '0', STR_PAD_LEFT), strtolower($securityName));
                    
                    $shiftedData[$key][] = $schedule;
                }
            
                // Urutkan berdasarkan key
                ksort($shiftedData);
            
                $output = '<ul>';
            
                foreach ($shiftedData as $key => $schedules) {
                    foreach ($schedules as $schedule) {
                        $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                        $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
            
                        if ($start === '07:00' && $end === '15:00') {
                            $shift = '1';
                        } elseif ($start === '15:00' && $end === '23:00') {
                            $shift = '2';
                        } elseif ($start === '23:00' && $end === '07:00') {
                            $shift = '3';
                        } else {
                            $shift = '-';
                        }
            
                        $securityName = $schedule->security ? $schedule->security->name : '-';
            
                        $output .= <<<HTML
                            <li class="mb-3">
                                <strong>Shift {$shift} - {$securityName}</strong>
                                <ul>
                        HTML;
            
                        // Absen
                        if ($schedule->absence->isNotEmpty()) {
                            foreach ($schedule->absence as $absen) {
                                $time = \Carbon\Carbon::parse($absen->absent_time)->format('H.i A');
                                $location = $absen->location ? ' - ' . $absen->location->name : '';
                                $note = ucfirst($absen->note) ?? '-';
            
                                $output .= <<<HTML
                                    <li>
                                        {$time}{$location}<br>
                                        <strong><span class="font-italic">Catatan:</span></strong> {$note}
                                    </li>
                                HTML;
                            }
                        } else {
                            $output .= '<li><em>Tidak ada absen</em></li>';
                        }
            
                        // Incident
                        if ($schedule->incident->isNotEmpty()) {
                            $output .= '<li><strong>Laporan Kejadian:</strong><ul>';
            
                            foreach ($schedule->incident as $incident) {
                                $urgencyLabel = $incident->urgency ?? '-';
                                $location = $incident->location ? $incident->location->name : '-';
                                $description = ucfirst($incident->description) ?? '-';
            
                                $badgeClass = match(strtolower($urgencyLabel)) {
                                    'tinggi' => 'badge bg-danger',
                                    'sedang' => 'badge bg-warning text-dark',
                                    'rendah' => 'badge bg-success',
                                    default => 'badge bg-secondary',
                                };
            
                                $output .= <<<HTML
                                    <li>
                                        Lokasi: {$location}<br>
                                        Prioritas: <span class="{$badgeClass}">{$urgencyLabel}</span><br>
                                        <span class="font-weight-bold font-italic">Keterangan:</span> {$description}
                                    </li>
                                HTML;
                            }
            
                            $output .= '</ul></li>';
                        } else {
                            $output .= '<li><em>Tidak ada laporan kejadian</em></li>';
                        }
            
                        $output .= '</ul></li>';
                    }
                }
            
                $output .= '</ul>';
            
                return $output;
            })
            ->rawColumns(['dates', 'securities', 'details'])
            ->make(true);
    }

    public function reportPdf($daterange)
    {
        try {
            // Memisahkan rentang tanggal
            $date = explode('+', $daterange);
            $start = Carbon::parse($date[0])->format('Y-m-d');
            $end = Carbon::parse($date[1])->format('Y-m-d');

            // Mendapatkan jadwal patroli sesuai rentang tanggal
            $schedules = Schedule::with([
                'security', 
                'absence',
                'incident'
            ])
            ->whereBetween('shift_date', [$start, $end])
            ->orderBy('shift_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->get();

            // Format tanggal untuk ditampilkan di judul laporan
            $startpdf = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
            $endpdf = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

            // Format dan sortir jadwal
            $groupedSchedules = $this->formatScheduleDetails($schedules);

            // Menghasilkan PDF
            $pdf = PDF::loadView('admin.report.reportpdf', compact('groupedSchedules', 'startpdf', 'endpdf'));

            // Menyimpan PDF sementara di server
            $fileName = 'Laporan Patroli Periode ' . $startpdf . ' sampai ' . $endpdf . '.pdf';
            $filePath = storage_path('app/public/adminreports/' . $fileName);

            $pdf->save($filePath);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF berhasil diunduh',
                    'file_url' => asset('storage/adminreports/' . $fileName)
                ], 200);
            }

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan PDF.',
                'error' => $e->getMessage()
            ], 500);
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
