@extends('layouts.app')
@section('title', 'Generate Jadwal')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Generate Jadwal</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Generate Jadwal</h6>
        </div>

        <div class="card-body">


            <form class="row g-3 p-2" action="{{ route('dashboard.schedule.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="col-6 mb-3">
                    <label for="population" class="form-label">Jumlah Populasi</label>
                    <input type="text" class="form-control @error('population') is-invalid @enderror" id="population"
                        name="population" value="{{ old('population') }}" placeholder="100" required>
                    @error('population')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="max_generation" class="form-label">Maks Generation</label>
                    <input type="text" class="form-control @error('max_generation') is-invalid @enderror"
                        id="max_generation" name="max_generation" value="{{ old('max_generation') }}" placeholder="100"
                        required>
                    @error('max_generation')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="mutation_rate" class="form-label">Mutation Rate</label>
                    <input type="text" class="form-control @error('mutation_rate') is-invalid @enderror"
                        id="mutation_rate" name="mutation_rate" value="{{ old('mutation_rate') }}" placeholder="0.01"
                        required>
                    @error('mutation_rate')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label for="time_slot" class="form-label">Time Slot</label>
                    <input type="number" class="form-control @error('time_slot') is-invalid @enderror" id="time_slot"
                        name="time_slot" value="{{ old('time_slot') }}" placeholder="10" required>
                    @error('time_slot')
                        <span class="text-danger text-sm" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="text-left">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
@endsection
