@extends('layouts.admin')

@section('title')
    <title>Tambah Jadwal Tugas</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Konsumen</h1> --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('schedule.index') }}">Jadwal Tugas</a></li>
                            <li class="breadcrumb-item active">Tambah Jadwal Tugas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('schedule.index') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <form id="addScheduleForm" action="{{ route('schedule.store') }}" method="post">
                                @csrf
                                <div class="card-body loader-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="security_name">Pilih Satpam</label>
                                                <select name="security_name" id="security_name" class="form-control">
                                                    <option value="">Pilih Satpam</option>
                                                    @foreach ($security as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="security_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shift_date">Tanggal Tugas</label>
                                                <input type="text" id="multi-date" name="dates" class="form-control dates" placeholder="Pilih Tanggal" readonly/>
                                                <span class="text-danger" id="dates_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div id="time-inputs" class="mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-md float-right">Simpan</button>
                                    </div>
                                </div>
                            </form>    
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
    
        // dates
        let selectedDates = [];

        // Fungsi untuk format tanggal ke Bahasa Indonesia
        function formatTanggalIndonesia(dateString) {
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                        'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const [year, month, day] = dateString.split('-').map(Number);
            const dateObj = new Date(year, month - 1, day);

            const namaHari = hari[dateObj.getDay()];
            const namaBulan = bulan[month - 1];

            return `${namaHari}, ${day} ${namaBulan} ${year}`;
        }

        function updateMultiDateInput() {
            $('#multi-date').val(selectedDates.join(', '));
        }

        $('#multi-date').multiDatesPicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0, // hanya bisa pilih dari hari ini ke depan
            onSelect: function (dateText) {
                if (!selectedDates.includes(dateText)) {
                    selectedDates.push(dateText);
                    updateMultiDateInput(); // Update tampilan input

                    // Ubah format tanggal untuk tampilan
                    const tanggalFormatted = formatTanggalIndonesia(dateText);

                    $('#time-inputs').append(`
                        <div class="border p-3 mb-2 rounded time-row" data-date="${dateText}">
                            <div class="d-flex justify-content-between">
                                <label><strong>${tanggalFormatted}</strong></label>
                                <button type="button" class="btn btn-sm btn-danger remove-shift" title="Hapus ${tanggalFormatted}" data-date="${dateText}"><span class="fa fa-trash"></span></button>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Waktu Mulai :</label>
                                    <input type="time" name="times[${dateText}][start]" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Waktu Selesai :</label>
                                    <input type="time" name="times[${dateText}][end]" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
        });

        $(document).on('click', '.remove-shift', function () {
            const date = $(this).data('date');
            selectedDates = selectedDates.filter(d => d !== date);
            $(`.time-row[data-date="${date}"]`).remove();
            // Update isi input
            updateMultiDateInput()
        });

        $(document).ready(function (){

            $('#addScheduleForm').on('submit', function(e){
                e.preventDefault();

                var formData = $(this).serialize();
                console.log(formData);
                var actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
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
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
                    },
                    success: function(response){
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('schedule.index') }}";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        console.log('ini error ' + errors)
                        console.log(input)

                        // Clear previous errors
                        $('.text-danger').text('');

                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }
                        Swal.fire({
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                // Display validation errors using SweetAlert
                                let errorMessage = '';
                                $.each(errors, function(key, error) {
                                    errorMessage += error[0] + '<br>';
                                    console.log(errorMessage);
                                    $('#' + key + '_error').text(error[0]);
                                    $('#' + key).addClass('input-error');
                                    $('.' + key).addClass('input-error');

                                    // Set timeout to clear the error text after 3 seconds
                                    setTimeout(function() {
                                        $('#' + key + '_error').text('');
                                        $('#' + key).removeClass('input-error');
                                        $('.' + key).removeClass('input-error');
                                    }, 3000);
                                });

                                // Retain input values
                                $.each(input, function(key, value) {
                                    $('#' + key).val(value);
                                });
                            }
                        });
                    }
                })
            });

        });
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection