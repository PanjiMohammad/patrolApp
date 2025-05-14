@extends('layouts.admin')

@section('title')
    <title>Dashboard</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    {{-- <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div> --}}
                    <div class="col-sm-12">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                            <li class="breadcrumb-item active">Beranda</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa-solid fa-city mr-1"></i>
                                Aktivitas Patroli Gereja ....
                            </h3>
                        </div>
                        <div class="card-body loader-area">
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="small-box" style="background: linear-gradient(135deg, #00c6ff, #0072ff);">
                                        <div class="inner">
                                            <h4>{{ $securities->count() }}</h4>
                                            <p class="font-weight-bold">Satpam</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-person-stalker"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <!-- small box -->
                                    <div class="small-box" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
                                        <div class="inner">
                                            <h4>{{ $locations->count() }}</h4>
                                            <p class="font-weight-bold">Titik Lokasi</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-location"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box" style="background: linear-gradient(135deg, #f85032, #e73827);">
                                        <div class="inner">
                                            <h4>{{ $schedules->count() }}</h4>
                                            <p class="font-weight-bold">Jadwal</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card">
                        <div class="card-header">
                            <title>test</title>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </section>
    </div>

    {{-- <button class="test">pencet</button> --}}

    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
        
        // test
        $('.test').on('click', function(){
            console.log('it work');
        });

        // load calendar
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                timeZone: 'local',
                events: function(fetchInfo, successCallback, failureCallback) {
                    // Tampilkan loader sebelum mengambil data
                    $('.loader-area').block({
                        message: '<i class="fa fa-spinner fa-spin"></i> Loading...',
                        overlayCSS: {
                            backgroundColor: '#fff',
                            opacity: 0.8,
                            cursor: 'wait'
                        },
                        css: {
                            border: 0,
                            padding: 0,
                            backgroundColor: 'none'
                        }
                    });

                    fetch('{{ route('home.getEvents') }}')
                        .then(response => response.json())
                        .then(events => {
                            events.forEach(event => {
                                event.classNames = ['custom-event'];
                            });
                            successCallback(events);
                            $('.loader-area').unblock(); // Sembunyikan loader saat sukses
                        })
                        .catch(error => {
                            failureCallback(error);
                            $('.loader-area').unblock(); // Sembunyikan loader saat gagal
                        });
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                buttonText: {
                    today: 'Hari ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari',
                    list: 'Daftar'
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            });

            calendar.render();
        });

    </script>
@endsection

@section('css')
    <style>
        
        /* #calendar {
            max-width: auto;
            margin: 30px auto;
        } */

        /* FullCalendar background */
        .fc {
            background-color: #f8f9fa; /* abu terang */
            font-family: 'Segoe UI', sans-serif;
        }

        /* Header Title */
        .fc-toolbar-title {
            color: #343a40;
            font-weight: 600;
        }

        /* Header Buttons */
        .fc-button {
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 5px;
        }
        .fc-button:hover {
            background-color: #0056b3;
        }

        /* Active button (Today / Selected View) */
        .fc-button.fc-button-active {
            background-color: #17a2b8;
            color: #fff;
        }

        /* Event Styling */
        .custom-event {
            background-color: #6f42c1 !important; /* ungu terang */
            color: #ffffff !important;
            padding: 3px 5px;
            border-radius: 5px;
            font-size: 0.85rem;
            border: none;
        }

        /* Today's date highlight */
        .fc-day-today {
            background-color: #fff3cd !important;
        }

        /* Grid border & spacing */
        .fc-daygrid-day-frame {
            border: 1px solid #dee2e6;
            padding: 4px;
        }

        /* Adjust calendar size */
        #calendar {
            max-width: 100%;
            margin: auto;
        }
        
    </style>
@endsection