@extends('layouts.app')
@section('title', 'Tambah Data Mapel')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Mapel</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Mapel</h6>
        </div>

        <div class="card-body">

            <a href="{{ route('dashboard.subject.index') }}" class="btn btn-info mb-4 mt-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <form class="row g-3 p-2" action="{{ route('dashboard.subject.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Mapel</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                        cols="30" rows="10">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                <div class="text-left">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
