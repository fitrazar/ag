@extends('layouts.app')
@section('title', 'Data Guru')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Guru</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Guru</h6>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success d-flex justify-content-center m-3" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-body">

            <a href="{{ route('dashboard.teacher.create') }}" class="btn btn-primary mb-4 mt-2">
                <i class="fas fa-plus me-2"></i> Tambah Data
            </a>

            <div class="table-responsive">
                <table id="teachers" class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 5% !important;">No</th>
                            <th class="text-left">NIK</th>
                            <th class="text-left">Nama Guru</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            let dataTable = $('#teachers').DataTable({
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf', 'print',
                    'colvis'
                ],
                processing: true,
                search: {
                    return: true
                },
                serverSide: true,
                ajax: '{{ url()->current() }}',
                columns: [{
                        data: null,
                        name: 'no',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return `
                                <a href="{{ url('/dashboard/teacher/${full.id}/edit') }}">
                                    <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-pen-square me-2"></i>Edit</button>
                                </a>
                                <form action="{{ url('/dashboard/teacher/${full.id}') }}" style="display: inline;" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash me-2"></i>Hapus</button>
                                </form>
                            `;
                        }
                    },
                ]
            });
        });
    </script>
@endsection
