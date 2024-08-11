@extends('layouts.app')
@section('title', 'Tambah Data Jurusan')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Jurusan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Jurusan</h6>
        </div>

        <div class="card-body">

            <a href="{{ route('dashboard.major.index') }}" class="btn btn-info mb-4 mt-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <form class="row g-3 p-2" action="{{ route('dashboard.major.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Jurusan</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="acronym" class="form-label">Alias</label>
                    <input type="text" class="form-control @error('acronym') is-invalid @enderror" id="acronym"
                        name="acronym" value="{{ old('acronym') }}" required>
                    @error('acronym')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                {{-- <div class="form-check col-12 mb-3">
                    <input class="form-check-input" type="checkbox" id="status" name="status">
                    <label class="form-check-label" for="status">
                        Sembunyikan?
                    </label>
                </div> --}}
                <div class="text-left">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
