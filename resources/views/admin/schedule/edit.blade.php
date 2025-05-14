@extends('layouts.admin')

@section('title')
    <title>Edit Jadwal Tugas</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    {{-- <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Jadwal Tugas</h1>
                    </div> --}}
                    <div class="col-sm-12">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('schedule.index') }}">Jadwal Tugas</a></li>
                            <li class="breadcrumb-item active">Edit Jadwal Tugas</li>
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('schedule.index') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <form id="editScheduleForm" action="{{ route('schedule.update') }}" method="post"  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="security_id" value="{{ $schedule->id }}">
                                <div class="card-body loader-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="security_name">Nama Satpam</label>
                                                <select name="security_name" id="security_name" class="form-control">
                                                    <option value="">Pilih</option>
                                                    @foreach ($security as $row)
                                                        <option value="{{ $row->id }}" {{ $schedule->security_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="security_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="multi-date">Tanggal Tugas</label>
                                                <input type="text" id="multi-date" name="dates" class="form-control" placeholder="Pilih Tanggal" readonly>
                                                <span class="text-danger" id="dates_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="time-fields"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <button class="btn btn-primary btn-md">Ubah</button>
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
        let selectedDates = {!! json_encode($existingDates) !!};
        let existingTimes = {!! json_encode($existingTimes) !!};

        function updateDateInput() {
            $('#multi-date').val(selectedDates.join(', '));
        }

        function renderTimeFields() {
            $('#time-fields').html('');
            selectedDates.forEach(date => {
                const times = existingTimes[date] || [{ start: '', end: '' }];
                $('#time-fields').append(`
                    <div class="border p-3 mb-3" data-date="${date}">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>${new Date(date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</strong>
                            <button type="button" class="btn btn-sm btn-danger remove-date" title="Hapus ${new Date(date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}" data-date="${date}"><span class="fa fa-trash"></span></button>
                        </div>
                        ${times.map((t, i) => `
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label>Waktu Mulai</label>
                                    <input type="time" name="times[${date}][${i}][start]" value="${t.start}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Waktu Selesai</label>
                                    <input type="time" name="times[${date}][${i}][end]" value="${t.end}" class="form-control" required>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `);
            });
        }

        $(document).ready(function () {
            $('#multi-date').multiDatesPicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function (dateText) {
                    if (!selectedDates.includes(dateText)) {
                        selectedDates.push(dateText);
                        updateDateInput();
                        existingTimes[dateText] = [{ start: '', end: '' }];
                        renderTimeFields();
                    }
                }
            });

            updateDateInput();
            renderTimeFields();

            $(document).on('click', '.remove-date', function () {
                const date = $(this).data('date');
                selectedDates = selectedDates.filter(d => d !== date);
                delete existingTimes[date];
                updateDateInput();
                renderTimeFields();
            });

            $('#editScheduleForm').on('submit', function(e){
                    e.preventDefault();

                    var formData = new FormData(this);
                    console.log(formData);
                    var actionUrl = $(this).attr('action');

                    $.ajax({
                        url: actionUrl,
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
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
                                    if(xhr.status == 422) {
                                        // Display validation errors using SweetAlert
                                        let errorMessage = '';
                                        $.each(errors, function(key, error) {
                                            errorMessage += error[0] + '<br>';
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
                                    } else {
                                        window.location.reload(true);
                                    }
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