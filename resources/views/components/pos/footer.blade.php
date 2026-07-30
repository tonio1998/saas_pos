<footer class="be-footer">
    <div class="container-fluid">
        <div class="row align-items-center gy-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                    <div class="be-footer-logo me-3">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>

                    <div>
                        <div class="fw-bold mb-0">BantayEskwela</div>
                        <small class="text-muted">
                            QR & NFC-Based Student Monitoring and Attendance Management Platform
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="text-center text-lg-end">
                    <div class="small text-muted">
                        <span class="fw-semibold">Version {{ config('app.version', '1.0.0') }}</span>
                        <span class="mx-2">•</span>
                        © {{ date('Y') }} {{ config('app.name') }}
                        <span class="mx-2">•</span>
                        All Rights Reserved.
                    </div>
                    <div class="small text-muted mt-1">
                        Secure • Reliable • Real-Time Student Monitoring
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .be-footer {
        background: #fff;
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
        margin-top: auto;
    }

    .be-footer-logo {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #6f42c1;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(111, 66, 193, .18);
    }

    .be-footer .fw-bold {
        font-size: .95rem;
    }

    .be-footer small {
        font-size: .8rem;
    }

    @media (max-width: 991.98px) {
        .be-footer {
            text-align: center;
            padding: 1rem;
        }

        .be-footer .d-flex {
            justify-content: center;
        }

        .be-footer-logo {
            margin-right: .75rem;
        }

        .be-footer .text-lg-end {
            text-align: center !important;
            margin-top: .5rem;
        }
    }
</style>
