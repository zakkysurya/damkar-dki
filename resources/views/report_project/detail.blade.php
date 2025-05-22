@extends('layouts.admin')
@section('main-content')
<div class="row">

      <div class="col-lg-12 order-lg-1">


            <div class="card shadow mb-4">
                  <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Project Detail</h6>
                  </div>
                  <div class="card-body">
                        <form method="POST" action="" autocomplete="off">
                              <h6 class="heading-small text-muted mb-4">Man Power</h6>
                              <div class="pl-lg-4">
                                    <div class="row">
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Nama Lengkap</label>
                                                      <input type="text" class="form-control disabled" value="{{ $manPower->nama_lengkap }}" readonly>
                                                </div>
                                          </div>
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Jabatan</label>
                                                      <input type="text" class="form-control disabled" value="{{ $manPower->jabatan }}" readonly>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <h6 class="heading-small text-muted mb-4">Project</h6>
                              <div class="pl-lg-4">
                                    <div class="row">
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Name</label>
                                                      <input type="text" class="form-control disabled" value="{{ $project->nama_project }}" readonly>
                                                </div>
                                          </div>
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Deskripsi</label>
                                                      <input type="text" class="form-control disabled" value="{{ $project->deskripsi }}" readonly>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="pl-lg-4">
                                    <div class="row">
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Tanggal Mulai</label>
                                                      <input type="text" class="form-control disabled" value="{{ $project->tanggal_mulai }}" readonly>
                                                </div>
                                          </div>
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Tanggal Selesai</label>
                                                      <input type="text" class="form-control disabled" value="{{ $project->tanggal_selesai }}" readonly>
                                                </div>
                                          </div>
                                    </div>
                              </div>


                              <h6 class="heading-small text-muted mb-4">Tasks</h6>
                              <div class="pl-lg-4">
                                    @foreach($tasks as $task)
                                    <div class="row">
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Judul</label>
                                                      <input type="text" class="form-control disabled" value="{{ $task->judul_task }}" readonly>
                                                </div>
                                          </div>
                                          <div class="col-lg-6">
                                                <div class="form-group focused">
                                                      <label class="form-control-label" for="name">Status</label>
                                                      <input type="text" class="form-control disabled" value="{{ $task->status }}" readonly>
                                                </div>
                                          </div>
                                    </div>
                                    @endforeach
                              </div>


                              <!-- Button -->
                              <div class="pl-lg-4">
                                    <div class="row">
                                          <div class="col">
                                                <a type="submit" href="{{ route('report-project.index') }}" class="btn btn-primary">Back</a>
                                          </div>
                                    </div>
                              </div>
                        </form>



                  </div>

            </div>

      </div>

</div>
@endsection