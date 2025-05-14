@extends('layouts.admin')

@section('title')
    <title>Daftar Lokasi</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    {{-- <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Lokasi</h1>
                    </div> --}}
                    <div class="col-sm-12">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                            <li class="breadcrumb-item active">Kelola Titik Lokasi</li>
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
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tambah Titik Lokasi</h4>
                            </div>
                            <form id="addLocationForm" action="{{ route('location.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Titik Lokasi</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Titik Lokasi">
                                        <span class="text-danger" id="name_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Foto Titik Lokasi</label>
                                        <input type="file" name="image" id="image" class="form-control" onchange="previewImage(event)">
                                        <span class="text-danger" id="image_error"></span>

                                        <div class="d-flex justify-content-center align-items-center padding-image">
                                            <img id="preview" src="#" alt="Image Preview" style="display: none;" class="preview-img-frame">
                                        </div>
                                        <span class="text-danger" id="image_error"></span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-sm float-right">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body loader-area">
                                <div class="table-responsive">
                                    <table id="locationTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">No.</th>
                                                <th style="padding: 10px 10px;">Titik Lokasi</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editLocationModal" tabindex="-1" aria-labelledby="editLocationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editLocationForm" action="{{ route('location.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Titik Lokasi</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-name">Titik Lokasi</label>
                            <input type="text" name="edit-name" id="edit-name" class="form-control" required>
                            <span class="text-danger" id="edit-error-name"></span>
                        </div>
                        <div class="form-group">
                            <label for="image">Foto Titik Lokasi</label>
                            <div class="mb-3">
                                <img id="edit-preview" src="#" alt="Preview Gambar" class="preview-img-frame">
                            </div>
                            
                            <!-- Optional: Hidden untuk image lama -->
                            <input type="hidden" id="edit-old-image" name="old_image">
                            <input type="file" name="edit-image" class="form-control" onchange="previewImage(event)">
                            <span style="color: black;" class="text-sm">*Biarkan kosong jika tidak ingin mengganti gambar</span>
                            <span class="text-danger" id="edit-error-image"></span>

                            <!-- preview image -->
                            <div class="d-flex justify-content-center align-items-center padding-image">
                                <img id="preview-2" src="#" alt="Image Preview" style="display: none;" class="preview-img-frame">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Location -->
    <div class="modal fade" id="locationDetailModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationDetailModalLabel">Detail Titik Lokasi</h5>
                </div>
                <div class="modal-body modal-loader-area">
                    <table class="table table-borderless table-striped">
                        <tbody id="locationDetailsContent">
                            <!-- Product details will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->

    <!-- IMPORTANT LINK -->
    <a href="{{ route('location.getDatatables') }}" id="locationGetDatatables"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        // image add
        function previewImage(event) {
            const image = event.target.files[0];
            const preview = document.getElementById('preview');

            if (image) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';

                    // Tambah padding-top jika belum ada
                    $('.padding-image').addClass('mt-3');
                }
                reader.readAsDataURL(image);
            } else {
                preview.style.display = 'none';
            }
        }

        // image edit
        function previewImage(event) {
            const image = event.target.files[0];
            const preview = document.getElementById('preview-2');

            if (image) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';

                    // Tambah padding-top jika belum ada
                    $('.padding-image').addClass('mt-3');
                }
                reader.readAsDataURL(image);
            } else {
                preview.style.display = 'none';
            }
        }

        $(document).ready(function() {

            // csrf
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Lokasi...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada data'
                },
                initComplete: function() {
                    var $searchInput = $('#locationTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Lokasi...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#locationTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#locationTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#locationGetDatatables').attr('href');
            var table = $('#locationTable').DataTable({
                ajax: {
                    url: url,
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
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // detail location
            $('#locationTable').on('click', '.detail-location', function() {
                var locationId = $(this).data('location-id');
                var showUrl = '{{ route("location.detail", ":id") }}'.replace(':id', locationId);

                $.ajax({
                    url: showUrl,
                    type: 'GET',
                    beforeSend: function() {
                        $('#locationDetailModal').modal('show');
                        $('#locationDetailsContent').block({ 
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
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('#locationDetailsContent').unblock(); // Hide loader after request complete
                    },
                    success: function(response) {
                        // Create table rows from the response
                        var locationDetailHtml = `
                            <tr>
                                <th style="width: 10%;">Titik Lokasi</th>
                                <td style="width: 5%;">:</td>
                                <td style="width: 50%;">${response.name}</td>
                            </tr>
                            <tr>
                                <th style="width: 10%;">Foto Titik Lokasi</th>
                                <td style="width: 5%;">:</td>
                                <td style="width: 50%;">
                                    <div class="image-wrapper">
                                        <img src="${response.image}" alt="${response.name}" class="img-frame-auto">
                                    </div>
                                </td>
                            </tr>
                        `;

                        // Insert the HTML into the table body
                        $('#locationDetailsContent').html(locationDetailHtml);
                        // Show the modal
                        $('#locationDetailModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memuat detail produk. Silahkan coba lagi nanti.',
                            icon: 'error'
                        });
                        $('#locationDetailModal').modal('hide');
                    }
                });
            });

            // add location
            $('#addLocationForm').on('submit', function(e){
                e.preventDefault();

                let formData = new FormData(this);
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
                        console.log(response)
                        if(response.success == true){
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                timer: 2000, 
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.reload(true);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: response.message,
                                icon: 'error',
                                timer: 2000, 
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.reload(true);
                                }
                            });
                        }
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
                                // Display validation errors using SweetAlert
                                let errorMessage = '';
                                $.each(errors, function(key, error) {
                                    errorMessage += error[0] + '<br>';
                                    $('#' + key + '_error').text(error[0]);
                                    $('#' + key).addClass('input-error');

                                    // Set timeout to clear the error text after 3 seconds
                                    setTimeout(function() {
                                        $('#' + key + '_error').text('');
                                        $('#' + key).removeClass('input-error');
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

            // edit location
            $('#locationTable').on('click', '.edit-location', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const image = $(this).data('image');
                
                $('#edit-id').val(id);
                $('#edit-name').val(name);
                
                if (image) {
                    $('#edit-preview').attr('src', image).show();
                    $('#edit-old-image').val(image);
                } else {
                    $('#edit-preview').hide();
                    $('#edit-old-image').val('');
                }
                $('#editLocationModal').modal('show');
            });

            // update location
            $('#editLocationForm').on('submit', function(e){
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#editLocationModal').modal('hide');
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
                                table.ajax.reload();
                                window.location.reload(true);
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
                                // Display validation errors using SweetAlert
                                let errorMessage = '';
                                $.each(errors, function(key, error) {
                                    errorMessage += error[0] + '<br>';
                                    $('#' + key + '_error').text(error[0]);
                                    $('#' + key).addClass('input-error');

                                    // Set timeout to clear the error text after 3 seconds
                                    setTimeout(function() {
                                        $('#' + key + '_error').text('');
                                        $('#' + key).removeClass('input-error');
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

            // delete location
            $('#locationTable').on('click', '.delete-location', function(e) {
                e.preventDefault();

                var locationId = $(this).data('location-id');
                var deleteUrl = '{{ route("location.destroy", ":id") }}'.replace(':id', locationId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus titik lokasi ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
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
                                }); // Show loader before request
                            },
                            success: function(response) {
                                $('.loader-area').unblock(); // Hide loader after request complete
                                if (response.success == true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, 
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('.loader-area').unblock(); // Hide loader after request complete
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessage,
                                    icon: 'error',
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        table.ajax.reload();
                                    }
                                });;
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }

        .preview-img-frame {
            max-width: 100%;
            max-height: 100%;
            aspect-ratio: auto;
            object-fit: cover;
            border: 1px solid #ededed;
            border-radius: 5px;
            padding: 4px;
            background-color: transparent;
        }
        .image-wrapper {
            max-width: 100%;
            max-height: 100%;
            aspect-ratio: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border: 1px solid transparent;
            border-radius: 5px;
            background-color: #transparent;
            padding: 4px;
        }

        .img-frame-auto {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }
    </style>
@endsection