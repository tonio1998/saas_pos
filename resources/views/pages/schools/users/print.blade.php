@extends('layouts.app')
@section('title','ID Preview')

@section('content')
    <style>
        :root{
            --id-width:220px;
            --id-ratio:638 / 1013;
        }

        .id-canvas{
            width:320px;
            height:auto;
            aspect-ratio:638 / 1013;
            border:1px solid #ccc;
            border-radius:12px;
            box-shadow:0 4px 12px rgba(0,0,0,.15);
            background:#fff;
            display:block;
            object-fit:contain;
            image-rendering:auto;
        }

        .canvas-container{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            justify-content:center;
            align-items:flex-start;
        }

        @media print{

            body{
                margin:0;
                padding:0;
            }

            .canvas-container{
                gap:0;
            }

            .id-canvas{
                width:2.125in !important;
                height:3.375in !important;
                border:none !important;
                border-radius:0 !important;
                box-shadow:none !important;
                page-break-inside:avoid;
            }
        }
    </style>

    <div class="d-flex justify-content-center align-items-center">
        <div>
            <x-card class="mb-4">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.change-photo',[encrypt($user->id), 'q=students']) }}" class="btn btn-primary" target="_blank">
                        Upload new Photo
                    </a>
                </div>
            </x-card>

            <div class="d-flex justify-content-center align-items-center">
                <div class="me-4">
                    <canvas id="frontCanvas" class="id-canvas"></canvas>
                </div>
                <div>
                    <canvas id="backCanvas" class="id-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.student = @json($student);
        window.user = @json($user);
    </script>

    @vite('resources/js/qrcode.js')
@endsection
