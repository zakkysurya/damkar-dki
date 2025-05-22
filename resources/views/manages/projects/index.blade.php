@extends('layouts.admin')
@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/css/index.css') }}">
@endsection
@section('main-content')
{{-- TABLE --}}
<div class="col-12 mb-2">
      <div class="card shadow-md">
            <div class="card-header">
                  <div class="row">
                        <div class="col-6">
                              <h5 class="text-dark fw-bold">Data</h5>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                              <button class="btn btn-sm btn-outline-primary" title="Tambah Data" type="button" id="btn-add_data"><i
                                          class='fa fa-plus'></i> Tambah</button>
                        </div>
                  </div>
            </div>
            <div class="card-body">
                  <div class="table-responsive p-2">
                        <table id="data-table" width="100%" cellspacing="0" class="table table-striped table-bordered nowrap w-100">
                              <thead>
                                    <tr>
                                          <th scope="col" class="align-middle">No</th>
                                          <th scope="col" class="align-middle">Nama Project</th>
                                          <th scope="col" class="align-middle">Deskripsi</th>
                                          <th scope="col" class="align-middle">Taggal Mulai</th>
                                          <th scope="col" class="align-middle">Taggal Selesai</th>
                                          <th scope="col" class="align-middle">Aksi</th>
                                    </tr>
                              </thead>
                              <tbody>
                              </tbody>
                        </table>
                  </div>
            </div>
      </div>
</div>
{{-- ./TABLE --}}
<!-- Modal: Global Events -->
<div class="modal fade" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="globalModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="globalModalLabel"></h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div id="globalModalForm">
                        <!-- Setting in javascript -->
                  </div>
            </div>
      </div>
</div>
<!-- Modal: Global Events -->
@endsection
@section('js')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('custom/datatable.js') }}"></script>
<!-- Setting Datatable -->
<script type="text/javascript">
      let ajaxDataTable;
      $(function() {
            ajaxDataTable = initDataTable({
                  selector: '#data-table',
                  url: "{!! route('manages.projects.data-table') !!}",
                  columns: [{
                              data: 'DT_RowIndex',
                              orderable: false,
                              searchable: false,
                              width: '10px',
                              className: 'text-center'
                        },
                        {
                              data: 'nama_project',
                        },
                        {
                              data: 'deskripsi',
                        },
                        {
                              data: 'tanggal_mulai',
                              orderable: false,
                              searchable: false,
                              width: '10px',
                              className: 'text-center'
                        },
                        {
                              data: 'tanggal_selesai',
                              orderable: false,
                              searchable: false,
                              width: '10px',
                              className: 'text-center'
                        },
                        {
                              data: 'action',
                              orderable: false,
                              searchable: false,
                              width: '10px',
                              className: 'text-center'
                        }
                  ]
            });
      });

      function reloadDataTable() {
            // Memuat ulang data tabel
            ajaxDataTable.ajax.reload();
      }
</script>
<!-- Custom JQUERY -->
<script type="text/javascript">
      $(document).ready(function() {
            // ACTION ADD NEW ACCOUNTS
            const EventsHandler = {
                  init: function() {
                        this.bindEvents();
                  },
                  bindEvents: function() {
                        // Setting form add
                        $('#btn-add_data').on('click', this.handler.settFormAdd.bind(this));
                        // Handle form add accounts
                        $(document).on('submit', '#form-create', this.handler.submitFormAdd.bind(this));

                        // Setting form add
                        $(document).on('click', '.btn-update_data', this.handler.update.bind(this));
                        // Handle form add accounts
                        $(document).on('submit', '#form-edit', this.handler.submitFormUpdate.bind(this));

                        // Event handler untuk tombol .btn-delete_data
                        $(document).on('click', '.btn-delete_data', this.handler.delete.bind(this));
                  },
                  handler: {
                        // setting form add
                        settFormAdd: function(e) {
                              // Setting HTML Form
                              const htmlForm = this.html.htmlFormAddAccounts();
                              // Tampilkan modal
                              $('#globalModalLabel').html('<i class="fa fa-tag fa-2x"></i> Tambah Data');
                              $('#globalModalForm').html(htmlForm);
                              $('#form-create')[0].reset(); // Mengosongkan form
                              $('#globalModal').modal('show');
                        },
                        update: function(e) {
                              const encryptedId = $(e.currentTarget).data(
                                    'encrypted_id');
                              this.ajax.show(encryptedId);
                        },
                        // menambahkan row form
                        addRowForm: function(e) {
                              // Setting HTML Form
                              const htmlRowForm = this.html.htmlRowForm();
                              $('#accountsForm #form-rows').append(htmlRowForm);
                        },
                        // hapus row form
                        removeRowForm: function(e) {
                              $(e.currentTarget).closest('.row').remove();
                        },
                        // submit form add
                        submitFormAdd: function(e) {
                              e.preventDefault();
                              // Ambil nilai input dari form
                              const formData = new FormData(e.currentTarget);
                              // SweetAlert untuk konfirmasi
                              Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    html: `Data akan ditambahkan.`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya',
                                    cancelButtonText: 'Batal'
                              }).then((result) => {
                                    if (result.isConfirmed) {
                                          // Jalankan fungsi addNewAccounts jika dikonfirmasi
                                          this.ajax.save(formData);
                                    }
                              });
                        },
                        submitFormUpdate: function(e) {
                              e.preventDefault();
                              // Ambil nilai input dari form
                              const formData = new FormData(e.currentTarget);
                              // SweetAlert untuk konfirmasi
                              Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    html: `Data akan diubah.`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya',
                                    cancelButtonText: 'Batal'
                              }).then((result) => {
                                    if (result.isConfirmed) {
                                          // Jalankan fungsi addNewAccounts jika dikonfirmasi
                                          this.ajax.update(formData);
                                    }
                              });
                        },
                        delete: function(e) {
                              const encryptedId = $(e.currentTarget).data(
                                    'encrypted_id');
                              // SweetAlert untuk konfirmasi
                              Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    html: `Anda akan menghapus akun <br>${name}.`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, Hapus',
                                    cancelButtonText: 'Batal'
                              }).then((result) => {
                                    if (result.isConfirmed) {
                                          // Jalankan fungsi destroy jika dikonfirmasi
                                          this.ajax.destroy(encryptedId);
                                    }
                              });
                        },
                  },
                  html: {
                        // html form add
                        htmlFormAddAccounts: function() {
                              return `<form id="form-create">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-12 mb-2">
                            <label for="nama_project">Nama Project <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              name="nama_project" 
                              class="form-control"
                              placeholder="Masukan Nama Project" />
                          </div>
                          <div class="col-12 mb-2">
                            <label for="nama_project">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" placeholder="Masukan Deskripsi" cols="30" rows="10"></textarea>
                          </div>
                          <div class="col-12 mb-2">
                            <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input 
                              type="date" 
                              name="tanggal_mulai" 
                              class="form-control"/>
                          </div>
                          <div class="col-12 mb-2">
                            <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input 
                              type="date" 
                              name="tanggal_selesai" 
                              class="form-control"/>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-send"></i>
                          Simpan</button>
                      </div>
                    </form>`;
                        },
                        // html row form ketika di add
                        htmlRowForm: function() {
                              return `<div class="row g-2 mb-3 align-items-center">
                                <!-- Input Code -->
                                <div class="col-3 col-md-4">
                                    <input 
                                        type="text" 
                                        class="form-control input-air-primary" 
                                        name="code[]" 
                                        placeholder="Code" 
                                        required>
                                </div>
                                <!-- Input Name -->
                                <div class="col-8 col-md-7">
                                    <input 
                                        type="text" 
                                        class="form-control input-air-primary" 
                                        name="name[]" 
                                        placeholder="Name" 
                                        required>
                                </div>
                                <!-- Button Add -->
                                <div class="col-1 col-md-1 text-center">
                                    <button type="button" class="btn btn-sm btn-square btn-outline-danger btn-square rounded p-2 btn-delete-row">
                                        <i class="fa fa-trash fa-fw"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        },
                  },
                  ajax: {
                        // Function untuk mengirimkan data
                        save: function(formData) {
                              $.ajax({
                                    url: "{!! route('manages.projects.store') !!}", // Endpoint Laravel
                                    type: 'POST',
                                    data: formData,
                                    processData: false, // Jangan proses data karena menggunakan FormData
                                    contentType: false, // Jangan set content-type secara otomatis
                                    beforeSend: function() {
                                          // Tampilkan loader
                                          showLoader();
                                    },
                                    success: function(response) {
                                          // Refresh data table setelah sukses
                                          reloadDataTable();
                                          // Tampilkan pesan sukses menggunakan SweetAlert
                                          Swal.fire({
                                                title: 'Berhasil!',
                                                text: response.message,
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                          }).then(() => {
                                                // Tutup modal dan refresh halaman
                                                $('#globalModal').modal('hide');
                                          });
                                    },
                                    complete: function() {
                                          // Sembunyikan loader dan aktifkan kembali tombol submit
                                          hideLoader();
                                    },
                                    error: function(xhr) {
                                          // Tampilkan pesan error menggunakan SweetAlert
                                          Swal.fire({
                                                title: 'Gagal!',
                                                text: xhr.responseJSON?.message ||
                                                      'Terjadi kesalahan, coba lagi.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                          });
                                    }
                              });
                        },
                        show: function(encryptedId) {
                              $.ajax({
                                    url: "{!! route('manages.projects.show') !!}", // Endpoint Laravel
                                    type: 'get',
                                    data: {
                                          encryptedId
                                    },
                                    cache: false,
                                    success: function(response) {
                                          // Setting HTML Form
                                          const htmlForm = `<form id="form-edit">
                      @method('PUT')
                      @csrf
                      <input type="hidden" name="encryptedId" value="${encryptedId}" />
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-12 mb-2">
                            <label for="nama_project">Nama Project <span class="text-danger">*</span></label>
                            <input 
                              type="text" 
                              value="${response.data.nama_project}"
                              name="nama_project" 
                              class="form-control"
                              placeholder="Masukan Nama Project" />
                          </div>
                          <div class="col-12 mb-2">
                            <label for="nama_project">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" placeholder="Masukan Deskripsi" cols="30" rows="10">
                              ${response.data.deskripsi}
                              </textarea>
                          </div>
                          <div class="col-12 mb-2">
                            <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input 
                              type="date" 
                              name="tanggal_mulai" 
                              class="form-control"
                              value="${response.data.tanggal_mulai}"/>
                          </div>
                          <div class="col-12 mb-2">
                            <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input 
                              type="date" 
                              name="tanggal_selesai" 
                              class="form-control"
                              value="${response.data.tanggal_selesai}"/>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-send"></i>
                          Edit</button>
                      </div>
                    </form>`;
                                          // Tampilkan modal
                                          $('#globalModalLabel').html(
                                                '<i class="fa fa-tag fa-2x"></i> Edit Data');
                                          $('#globalModalForm').html(htmlForm);
                                          $('#form-edit')[0].reset(); // Mengosongkan form
                                          $('#globalModal').modal('show');
                                    },
                                    error: function(xhr) {
                                          // Tampilkan pesan error menggunakan SweetAlert
                                          Swal.fire({
                                                title: 'Gagal!',
                                                text: xhr.responseJSON?.message ||
                                                      'Terjadi kesalahan, coba lagi.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                          });
                                    }
                              });
                              // Setting HTML Form
                              const htmlForm = this.html.htmlFormAddAccounts();
                              // Tampilkan modal
                              $('#globalModalLabel').html('<i class="fa fa-tag fa-2x"></i> Tambah Data');
                              $('#globalModalForm').html(htmlForm);
                              $('#form-create')[0].reset(); // Mengosongkan form
                              $('#globalModal').modal('show');
                        },
                        // Function untuk mengirimkan data
                        update: function(formData) {
                              $.ajax({
                                    url: "{!! route('manages.projects.update') !!}", // Endpoint Laravel
                                    type: 'POST',
                                    data: formData,
                                    processData: false, // Jangan proses data karena menggunakan FormData
                                    contentType: false, // Jangan set content-type secara otomatis
                                    beforeSend: function() {
                                          // Tampilkan loader
                                          showLoader();
                                    },
                                    success: function(response) {
                                          // Refresh data table setelah sukses
                                          reloadDataTable();
                                          // Tampilkan pesan sukses menggunakan SweetAlert
                                          Swal.fire({
                                                title: 'Berhasil!',
                                                text: response.message,
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                          }).then(() => {
                                                // Tutup modal dan refresh halaman
                                                $('#globalModal').modal('hide');
                                          });
                                    },
                                    complete: function() {
                                          // Sembunyikan loader dan aktifkan kembali tombol submit
                                          hideLoader();
                                    },
                                    error: function(xhr) {
                                          // Tampilkan pesan error menggunakan SweetAlert
                                          Swal.fire({
                                                title: 'Gagal!',
                                                text: xhr.responseJSON?.message ||
                                                      'Terjadi kesalahan, coba lagi.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                          });
                                    }
                              });
                        },
                        // Function untuk mengirimkan data via AJAX untuk menghapus akun
                        destroy: function(encryptedId) {
                              $.ajax({
                                    url: "{!! route('manages.projects.destroy') !!}", // Pastikan ini di-render dengan benar
                                    type: 'POST',
                                    data: {
                                          encryptedId: encryptedId,
                                          _token: '{{ csrf_token() }}' // Menambahkan CSRF token
                                    },
                                    beforeSend: function() {
                                          showLoader(); // Tampilkan loader sebelum permintaan
                                    },
                                    success: function(response) {
                                          // Refresh data table setelah sukses
                                          reloadDataTable();
                                          // SweetAlert untuk notifikasi sukses
                                          Swal.fire({
                                                title: 'Berhasil!',
                                                text: 'Akun berhasil dihapus.',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                          });
                                    },
                                    complete: function() {
                                          hideLoader(); // Sembunyikan loader setelah permintaan selesai
                                    },
                                    error: function(error) {
                                          console.error(error); // Tambahkan log untuk debugging
                                          const message = error.responseJSON.message ||
                                                'Terjadi kesalahan saat menghapus akun.';
                                          // SweetAlert untuk notifikasi gagal
                                          Swal.fire({
                                                title: 'Gagal!',
                                                text: message,
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                          });
                                    }
                              });
                        },
                  },
            }
            // Inisialisasi
            EventsHandler.init();
      });
</script>
@endsection