@extends('layouts.app')

@section('title','User Photo')

@section('content')
    @vite('resources/js/photo-crop.js')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-card>

                <div class="text-center mb-3">
                    <h5 class="fw-bold mb-1">ID Photo (PH Standard)</h5>
                    <small class="text-muted">
                        Drag to move • Scroll to zoom
                    </small>
                </div>

                <div class="d-flex flex-column align-items-center gap-3">

                    <div
                        style="
                            width:260px;
                            height:330px;
                            border:1px solid #dcdcdc;
                            border-radius:12px;
                            overflow:hidden;
                            background:#f8f9fa;
                            position:relative;
                        "
                    >
                        <canvas
                            id="photoCanvas"
                            style="
                                width:100%;
                                height:100%;
                                display:block;
                                cursor:grab;
                            "
                        ></canvas>
                    </div>

                    <input
                        type="hidden"
                        id="currentPhoto"
                        value="{{ $user->filepath ? asset('storage/'.$user->filepath) : asset('images/avatar.png') }}"
                    >

                    <input
                        type="file"
                        id="uploadPhoto"
                        accept="image/*"
                        class="form-control form-control-sm"
                    >

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="zoomIn"
                        >
                            +
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="zoomOut"
                        >
                            -
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            id="resetPhoto"
                        >
                            Reset
                        </button>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('users.upload') }}"
                        class="w-100"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="cropped_photo"
                            id="croppedPhoto"
                        >

                        <input
                            type="hidden"
                            name="user_id"
                            value="{{ $user->id }}"
                        >

                        <input
                            type="hidden"
                            name="user_type"
                            value="{{ $user_type }}"
                        >

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <a
                                href="{{ route('users.index') }}"
                                class="btn btn-light btn-sm"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
                                id="saveBtn"
                            >
                                Save User
                            </button>
                        </div>
                    </form>

                </div>

            </x-card>
        </div>
    </div>
@endsection
