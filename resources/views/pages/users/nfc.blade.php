@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div
                class="p-4 text-white"
                style="
                    background:
                        linear-gradient(
                            135deg,
                            #004D1A 0%,
                            #006B24 100%
                        );
                "
            >

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div>
                        <h3 class="fw-bold mb-1">
                            Assign NFC Card | {{ $user->name }}
                        </h3>

                        <p class="mb-0 text-white-50">
                            Securely assign NFC access cards to users.
                        </p>
                    </div>

                </div>

            </div>

            <div class="card-body p-4 p-lg-5">

                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>

                            <div>
                                <div class="fw-semibold">
                                    Success
                                </div>

                                <div class="small">
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('confirm_replace'))

                    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">

                        <div class="d-flex align-items-start">

                            <div class="me-3 fs-2 text-warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>

                            <div class="flex-grow-1">

                                <div class="fw-bold mb-1">
                                    NFC Card Already Assigned
                                </div>

                                <div class="text-muted mb-2">
                                    <strong>
                                        {{ session('confirm_replace.user_name') }}
                                    </strong>
                                    already has an NFC card assigned.
                                </div>

                                <div class="small text-muted">
                                    Continuing will replace the existing NFC assignment.
                                </div>

                            </div>

                        </div>

                    </div>

                @endif

                <form
                    action="{{ route('users.nfc.assign') }}"
                    method="POST"
                    id="nfcForm"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="userID"
                        value="{{ $user->id }}"
                    >

                    @if(session('confirm_replace'))
                        <input
                            type="hidden"
                            name="force_replace"
                            value="1"
                        >
                    @endif

                    <input
                        type="hidden"
                        id="nfc_uid"
                        name="nfc_uid"
                        value="{{ old('nfc_uid', $user->nfc_code) }}"
                        required
                    >

                    <div class="row g-4">

                        <div class="col-lg-7">

                            <div
                                id="scannerCard"
                                class="scanner-card border rounded-4 p-4 p-lg-5 h-100 position-relative overflow-hidden"
                            >

                                <div class="scanner-wave"></div>
                                <div class="scanner-wave scanner-wave-2"></div>

                                <div class="position-relative">

                                    <div
                                        class="scanner-icon-wrapper mx-auto mb-4"
                                    >
                                        <div class="scanner-pulse"></div>

                                        <div
                                            id="scannerIcon"
                                            class="scanner-icon"
                                        >
                                            <i class="bi bi-nfc"></i>
                                        </div>

                                    </div>

                                    <div class="text-center">

                                        <h3
                                            id="scannerTitle"
                                            class="fw-bold mb-2"
                                        >
                                            NFC Scanner Ready
                                        </h3>

                                        <p
                                            id="scannerSubtitle"
                                            class="text-muted mb-4"
                                        >
                                            Tap the NFC card near the reader to capture the UID automatically.
                                        </p>

                                        <div
                                            id="scanStatusBadge"
                                            class="badge rounded-pill px-4 py-3 bg-warning-subtle text-warning border border-warning-subtle"
                                        >
                                            <i class="bi bi-broadcast-pin me-1"></i>
                                            Waiting for NFC Scan
                                        </div>

                                        <div
                                            id="detectedUID"
                                            class="mt-4 d-none"
                                        >
                                            <div class="small text-muted mb-2">
                                                Captured NFC UID
                                            </div>

                                            <div
                                                class="uid-box"
                                            >
                                                <span id="uidText"></span>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-5">

                            <div class="border rounded-4 p-4 h-100">

                                <div class="d-flex align-items-center mb-4">

                                    <div
                                        class="rounded-circle d-flex justify-content-center align-items-center me-3"
                                        style="
                                            width: 52px;
                                            height: 52px;
                                            background: rgba(0,77,26,.08);
                                            color: #004D1A;
                                            font-size: 22px;
                                        "
                                    >
                                        <i class="bi bi-person-badge"></i>
                                    </div>

                                    <div>
                                        <div class="fw-bold">
                                            User Information
                                        </div>

                                        <div class="text-muted small">
                                            NFC assignment details
                                        </div>
                                    </div>

                                </div>

                                <div class="mb-4">

                                    <div class="text-muted small mb-1">
                                        Full Name
                                    </div>

                                    <div class="fw-semibold fs-5">
                                        {{ $user->name }}
                                    </div>

                                </div>

                                @if($user->nfc_code)

                                    <div
                                        class="alert alert-warning border-0 rounded-4"
                                    >

                                        <div class="fw-semibold mb-2">
                                            Existing NFC Assigned
                                        </div>

                                        <div class="small text-muted mb-2">
                                            Current assigned NFC UID:
                                        </div>

                                        <div class="uid-box small">
                                            {{ $user->nfc_code }}
                                        </div>

                                    </div>

                                @else

                                    <div
                                        class="alert alert-light border rounded-4"
                                    >

                                        <div class="fw-semibold mb-1">
                                            No NFC Assigned
                                        </div>

                                        <div class="small text-muted">
                                            This user does not currently have an NFC card assigned.
                                        </div>

                                    </div>

                                @endif

                                @error('nfc_uid')

                                <div
                                    class="alert alert-danger border-0 rounded-4"
                                >
                                    {{ $message }}
                                </div>

                                @enderror

                                <div class="d-grid mt-4">

                                    <button
                                        type="submit"
                                        id="submitBtn"
                                        class="btn btn-success btn-lg rounded-4 py-3 fw-semibold"
                                        disabled
                                    >
                                        <i class="bi bi-check-circle me-2"></i>
                                        Assign NFC Card
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <style>

        .scanner-card{
            background:
                linear-gradient(
                    180deg,
                    #ffffff 0%,
                    #f8faf9 100%
                );
            min-height: 480px;
        }

        .scanner-wave{
            position:absolute;
            width:400px;
            height:400px;
            background:rgba(0,77,26,.04);
            border-radius:50%;
            top:-180px;
            right:-120px;
            animation:waveFloat 7s infinite linear;
        }

        .scanner-wave-2{
            width:250px;
            height:250px;
            top:auto;
            bottom:-100px;
            left:-60px;
            animation-duration:9s;
        }

        @keyframes waveFloat{
            0%{
                transform:translateY(0px) rotate(0deg);
            }
            50%{
                transform:translateY(12px) rotate(180deg);
            }
            100%{
                transform:translateY(0px) rotate(360deg);
            }
        }

        .scanner-icon-wrapper{
            width:160px;
            height:160px;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .scanner-icon{
            width:120px;
            height:120px;
            border-radius:50%;
            background:
                linear-gradient(
                    135deg,
                    #004D1A 0%,
                    #00752B 100%
                );
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-size:54px;
            position:relative;
            z-index:2;
            box-shadow:
                0 20px 40px rgba(0,77,26,.18);
        }

        .scanner-pulse{
            position:absolute;
            width:120px;
            height:120px;
            border-radius:50%;
            background:rgba(0,77,26,.15);
            animation:pulse 2s infinite;
        }

        @keyframes pulse{
            0%{
                transform:scale(1);
                opacity:.8;
            }
            100%{
                transform:scale(1.6);
                opacity:0;
            }
        }

        .uid-box{
            background:#f5f7f8;
            border:1px dashed #cfd8dc;
            border-radius:16px;
            padding:14px 18px;
            font-weight:700;
            word-break:break-all;
            letter-spacing:1px;
            font-family:monospace;
        }

        .scanner-success .scanner-icon{
            background:
                linear-gradient(
                    135deg,
                    #198754 0%,
                    #28a745 100%
                );
        }

        .scanner-success .scanner-pulse{
            background:rgba(25,135,84,.15);
        }

    </style>

    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const hiddenInput = document.getElementById('nfc_uid');
            const scannerCard = document.getElementById('scannerCard');
            const scannerIcon = document.getElementById('scannerIcon');
            const scannerTitle = document.getElementById('scannerTitle');
            const scannerSubtitle = document.getElementById('scannerSubtitle');
            const scanStatusBadge = document.getElementById('scanStatusBadge');
            const detectedUID = document.getElementById('detectedUID');
            const uidText = document.getElementById('uidText');
            const submitBtn = document.getElementById('submitBtn');

            let buffer = '';
            let timer = null;

            document.addEventListener('keydown', (e) => {

                if (
                    e.target.tagName === 'INPUT' ||
                    e.target.tagName === 'TEXTAREA'
                ) {
                    return;
                }

                if (e.key === 'Enter') {

                    e.preventDefault();

                    const scannedUID = buffer.trim();

                    if (scannedUID.length > 0) {

                        hiddenInput.value = scannedUID;

                        scannerCard.classList.add('scanner-success');

                        scannerIcon.innerHTML = `
                            <i class="bi bi-check2-circle"></i>
                        `;

                        scannerTitle.innerHTML = `
                            NFC Card Detected
                        `;

                        scannerSubtitle.innerHTML = `
                            NFC UID captured successfully and ready for assignment.
                        `;

                        scanStatusBadge.className =
                            'badge rounded-pill px-4 py-3 bg-success-subtle text-success border border-success-subtle';

                        scanStatusBadge.innerHTML = `
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Ready to Assign
                        `;

                        uidText.textContent = scannedUID;

                        detectedUID.classList.remove('d-none');

                        submitBtn.disabled = false;

                        submitBtn.classList.add('shadow');

                        buffer = '';
                    }

                    return;
                }

                if (e.key.length === 1) {
                    buffer += e.key;
                }

                clearTimeout(timer);

                timer = setTimeout(() => {
                    buffer = '';
                }, 500);

            });

        });

    </script>
@endsection
