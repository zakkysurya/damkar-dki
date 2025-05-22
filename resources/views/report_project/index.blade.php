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
                                          <th scope="col" class="align-middle">Man Power</th>
                                          <th scope="col" class="align-middle">Jumlah Task</th>
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
                  url: "{!! route('report-project.data-table') !!}",
                  columns: [{
                              data: 'DT_RowIndex',
                              orderable: false,
                              searchable: false
                        },
                        {
                              data: 'nama_project',
                        },
                        {
                              data: 'man_power',
                        },
                        {
                              data: 'total_task',
                              orderable: false,
                              searchable: false
                        },
                        {
                              data: 'action',
                              orderable: false,
                              searchable: false
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
                  }
            }
            // Inisialisasi
            EventsHandler.init();
      });
</script>
@endsection