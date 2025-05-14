<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Patroli</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            line-height: 20px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 10px;
            text-align: center;
            line-height: 20px;
            color: #666;
        }

        .page-number:before {
            content: "Halaman " counter(page);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            word-wrap: break-word;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }

        th.col-tanggal, td.col-tanggal {
            width: 20%;
        }

        th.col-petugas, td.col-petugas {
            width: 21%;
        }

        .date-header {
            background-color: #d9edf7;
            font-weight: bold;
        }

        .shift-header {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- Header tetap --}}
    <header>
        <strong>Laporan Patroli</strong><br>
        Periode : {{ $startpdf }} s/d {{ $endpdf }}
    </header>

    {{-- Footer tetap --}}
    <footer>
        <div class="page-number"></div>
    </footer>

    
    <table>
        <thead>
            <tr>
                <th class="col-tanggal">Tanggal</th>
                <th class="col-petugas">Petugas</th>
                <th>Catatan</th>
                <th>Laporan Kejadian</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($groupedSchedules) && count($groupedSchedules) > 0)
                @php
                    $previousDateCount = [];
                    $previousShiftCount = [];
                @endphp

                @foreach($groupedSchedules as $key => $items)
                    @php
                        $firstItem = $items[0];
                        $shiftDate = \Carbon\Carbon::parse($firstItem['shift_date'])->locale('id')->translatedFormat('l, d F Y');
                        $shiftLabel = $firstItem['shift'];
                        $dateGroup = $firstItem['shift_date'];

                        $dateRowCount = collect($groupedSchedules)->filter(fn ($value, $k) => strpos($k, $dateGroup) === 0)->flatten(1)->count();
                        $shiftRowCount = count($items);
                    @endphp

                    @foreach($items as $index => $item)

                        @php
                            $startTime = \Carbon\Carbon::parse($item['schedule']->start_time)->locale('id')->format('H:i A') ?? '';
                            $endTime = \Carbon\Carbon::parse($item['schedule']->end_time)->locale('id')->format('H:i A') ?? '';
                            $timeCombine = $startTime . ' - ' . $endTime;
                        @endphp

                        <tr>
                            @if (!isset($previousDateCount[$dateGroup]))
                                <td rowspan="{{ $dateRowCount }}" style="vertical-align: middle;" class="col-tanggal">{{ $shiftDate }}</td>
                                @php $previousDateCount[$dateGroup] = true; @endphp
                            @endif

                            @if (!isset($previousShiftCount[$key]))
                                <td rowspan="{{ $shiftRowCount }}" style="vertical-align: middle;" class="col-petugas">
                                    <span>{{ $shiftLabel . ' - ' . $item['security']->name ?? '-' }}</span><br>
                                    <span>{{ $timeCombine }}</span>
                                </td>
                                @php $previousShiftCount[$key] = true; @endphp
                            @endif

                            <td style="vertical-align: middle;">
                                @if($item['absences']->isNotEmpty())
                                    @foreach($item['absences'] as $absen)

                                        @php
                                            $absenTimes = \Carbon\Carbon::parse($absen->absent_time)->locale('id')->format('H:i A') ?? '-';
                                            $absenLocations = isset($absen->location->name) ? ' - ' . $absen->location->name : '';
                                        @endphp

                                        <ul style="padding-left: 10px; margin: 0;">
                                            <li style="margin-bottom: 5px;">
                                                {{ $absenTimes . $absenLocations }}<br>
                                                <span style="font-style: italic; font-weight: bold;">Keterangan:</span> {{ ucwords($absen->note) ?? '-' }}
                                            </li>
                                        </ul>
                                    @endforeach
                                @else
                                    <span style="font-weight: bold; font-style: italic;">Tidak ada absen</span>
                                @endif
                            </td>
                            <td style="vertical-align: middle;">
                                @if($item['incidents']->isNotEmpty())
                                    @foreach($item['incidents'] as $incident)
                                        <ul style="padding-left: 10px; margin: 0;">
                                            <li style="margin-bottom: 5px;">
                                                <span>Lokasi:</span> {{ $incident->location->name ?? '-' }}<br>
                                                <span>Prioritas:</span> {{ $incident->urgency ?? '-' }}<br>
                                                <span style="font-style: italic; font-weight: bold;">Keterangan:</span> {{ ucwords($incident->description) ?? '-' }}
                                            </li>
                                        </ul>
                                    @endforeach
                                @else
                                    <span style="font-weight: bold; font-style: italic;">Tidak ada laporan kejadian</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <td colspan="4" style="text-align: center;">
                    <span style="font-weight: bold; font-style: italic;">Tidak ada data.</span>
                </td>
            @endif
        </tbody>
    </table>

</body>
</html>
