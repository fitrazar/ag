@extends('layouts.app')
@section('title', 'Tambah Data Penugasan')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Penugasan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Penugasan</h6>
        </div>

        <div class="card-body">

            <a href="{{ route('dashboard.working.index') }}" class="btn btn-info mb-4 mt-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <form class="row g-3 p-2" action="{{ route('dashboard.working.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="col-6 mb-3">
                    <label for="code" class="form-label">Kode</label>
                    <input type="number" class="form-control @error('code') is-invalid @enderror" id="code"
                        name="code" value="{{ old('code') }}" required>
                    @error('code')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="hours" class="form-label">Jam Mengajar</label>
                    <input type="text" class="form-control @error('hours') is-invalid @enderror" id="hours"
                        name="hours" value="{{ old('hours') }}" required>
                    @error('hours')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="rombel" class="form-label">Kelas</label>
                    <select class="form-select  @error('rombel') is-invalid @enderror" name="rombel" id="select2"
                        data-placeholder="Pilih Kelas" required>
                        <option value="" disabled selected>Pilih Kelas</option>
                        @foreach ($rombels as $rombel)
                            <option value="{{ $rombel['id'] }}" {{ old('rombel') == $rombel['id'] ? ' selected' : ' ' }}>
                                {{ $rombel['name'] }}</option>
                        @endforeach
                    </select>
                    @error('rombel')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="teacher_id" class="form-label">Guru / Pengajar</label>
                    <select class="form-select  @error('teacher_id') is-invalid @enderror" name="teacher_id" id="select2"
                        data-placeholder="Pilih Guru / Pengajar" required>
                        <option value="" disabled selected>Pilih Guru / Pengajar</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                {{ old('teacher_id') == $teacher->id ? ' selected' : ' ' }}>
                                {{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="subject_id" class="form-label">Nama Mapel</label>
                    <select class="form-select  @error('subject_id') is-invalid @enderror" name="subject_id" id="select2"
                        data-placeholder="Pilih Mapel" required>
                        <option value="" disabled selected>Pilih Mapel</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ old('subject_id') == $subject->id ? ' selected' : ' ' }}>
                                {{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
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
