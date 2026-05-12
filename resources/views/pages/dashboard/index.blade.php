@extends('layouts.app')
@section('title','Dashboard')
@section('content')
    <x-page-header
        title="Dashboard"
        subtitle="Overview of your system"
    />

    <div class="row g-3">

        <div class="col-md-3">
            <x-card class="text-white bg-gradient bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small opacity-75">Students</div>
                        <h3 class="mb-0">{{ $students ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                </div>
            </x-card>
        </div>

        <div class="col-md-3">
            <x-card class="text-white bg-gradient bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small opacity-75">Teachers</div>
                        <h3 class="mb-0">{{ $teachers ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-person-workspace fs-1 opacity-50"></i>
                </div>
            </x-card>
        </div>

        <div class="col-md-3">
            <x-card class="text-white bg-gradient bg-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small opacity-75">Parents</div>
                        <h3 class="mb-0">{{ $parents ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </x-card>
        </div>

    </div>
@endsection
