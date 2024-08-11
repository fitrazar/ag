@extends('layouts.app')
@section('title', 'Edit Data Siswa')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Siswa</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Siswa</h6>
        </div>

        <div class="card-body">

            <a href="{{ route('dashboard.student.index') }}" class="btn btn-info mb-4 mt-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <form class="row g-3 p-2" action="{{ route('dashboard.student.update', $student->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="oldImage" value="{{ $student->photo }}">
                @if ($student->photo)
                    <div class="d-flex justify-content-start">
                        <img src="{{ asset('storage/student/photo/' . $student->photo) }}" class="img-thumbnail"
                            alt="photo">
                    </div>
                @endif
                <div class="col-6 mb-3">
                    <label for="photo" class="form-label">Foto</label>
                    <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo"
                        name="photo" value="{{ old('photo', $student->photo) }}">
                    @error('photo')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="col-6 mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                        name="nisn" value="{{ old('nisn', $student->nisn) }}" required>
                    @error('nisn')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Siswa</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $student->name) }}" required>
                    @error('name')
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
                            <option value="{{ $rombel['id'] }}"
                                {{ old('rombel', $student->grade_id . ' ' . $student->major_id . ' ' . $student->group_id) == $rombel['id'] ? ' selected' : ' ' }}>
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
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select class="form-select  @error('gender') is-invalid @enderror" name="gender" required>
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="Laki - Laki"
                            {{ old('gender', $student->gender) == 'Laki - Laki' ? ' selected' : ' ' }}>Laki - Laki
                        </option>
                        <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? ' selected' : ' ' }}>
                            Perempuan</option>
                    </select>
                    @error('gender')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="phone" class="form-label">No Telpon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone', $student->phone) }}">
                    @error('phone')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                        name="address" value="{{ old('address', $student->address) }}">
                    @error('address')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                <div class="text-left">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
