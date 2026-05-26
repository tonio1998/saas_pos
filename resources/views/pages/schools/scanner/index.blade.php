@extends('layouts.scanner')

@section('title','Gate Scanner')

@section('content')
    <div class="scanner-core">
        <div
            id="status-bar"
            class="status-bar idle"
        >

            <div class="status-pulse"></div>

            <div>

                <div class="status-title">
                    TIME IN MODE
                </div>

                <div class="status-subtitle">
                    Waiting for entry scan
                </div>

            </div>

        </div>
        <div class="left-zone">

            <div class="identity-stage">

                <div class="identity-glow"></div>

                <div class="scanner-ring">

                    <div class="scanner-ring-2"></div>

                    <div class="avatar-shell">

                        <img
                            id="person-photo"
                            src="{{ asset('images/avatar.png') }}"
                            alt=""
                        >

                        <div class="scanner-line"></div>

                    </div>

                </div>

            </div>

        </div>
        <div class="center-zone">
            <input
                type="text"
                id="scan-input"
                autocomplete="off"
                autofocus
            >
            <div
                id="greeting-text"
                class="welcome-chip"
            >
                READY TO SCAN
            </div>

            <div
                id="person-name"
                class="person-name"
            >
                WAITING...
            </div>

            <div
                id="person-role"
                class="person-role"
            >
                TAP RFID CARD OR SCAN QR CODE
            </div>

            <div class="activity-strip">

                <div class="activity-pulse"></div>

                TERMINAL ACTIVE

            </div>

        </div>

        <div class="right-zone">

            <div class="metric-card primary">

                <div class="metric-label">
                    SCAN TIME
                </div>

                <div
                    id="scan-time"
                    class="metric-value"
                >
                    --:--
                </div>

            </div>

            <div class="metric-card yellow">

                <div class="metric-label dark">
                    DAILY SCANS
                </div>

                <div
                    id="scan-count"
                    class="metric-value dark"
                >
                    0
                </div>

            </div>

        </div>

    </div>

    <div class="bottom-terminal">

        <div class="terminal-item">

            <div class="terminal-dot"></div>

            SYSTEM READY

        </div>

        <div class="terminal-divider"></div>

        <div class="terminal-item">
            SNSU ATTENDANCE TERMINAL
        </div>

        <div class="terminal-divider"></div>

        <div class="terminal-item">
            LIVE MONITORING ENABLED
        </div>

    </div>


@endsection
