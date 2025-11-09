@extends('layouts.dashboard')

@section('title', __('Scan QR Code'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Scan QR Code') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Scan QR Code') }}</h6>
            </div>
            <div class="card-body">
                <!-- Camera Scanner -->
                <div id="scanner-container" class="mb-4">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <!-- Manual Entry -->
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">{{ __('Manual Entry') }}</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer.scan.manual-entry') }}" id="manualEntryForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="coupon">{{ __('Coupon') }}</option>
                                            <option value="card">{{ __('Digital Card') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="code" name="code" 
                                               placeholder="{{ __('Enter coupon or card code') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">{{ __('Purchase Amount') }}</label>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="branch_id" class="form-label">{{ __('Branch') }}</label>
                                        <input type="number" class="form-control" id="branch_id" name="branch_id" 
                                               placeholder="{{ __('Branch ID (optional)') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Result Display -->
                <div id="scan-result" style="display: none;"></div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Use') }}</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li class="mb-2">{{ __('Allow camera access when prompted') }}</li>
                    <li class="mb-2">{{ __('Point your camera at the QR code') }}</li>
                    <li class="mb-2">{{ __('Wait for automatic scanning') }}</li>
                    <li class="mb-2">{{ __('Or manually enter the code below') }}</li>
                    <li>{{ __('Enter purchase amount and branch (if applicable)') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrcodeScanner = null;

    function onScanSuccess(decodedText, decodedResult) {
        // Handle the scanned code
        processQRCode(decodedText);
    }

    function onScanFailure(error) {
        // Handle scan failure
        console.error('Scan error:', error);
    }

    // Initialize scanner when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('reader')) {
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            );
        }
    });

    function processQRCode(qrData) {
        // Show loading
        document.getElementById('scan-result').style.display = 'block';
        document.getElementById('scan-result').innerHTML = '<div class="alert alert-info">{{ __("Processing QR code...") }}</div>';

        // Send to server
        fetch('{{ route("customer.scan.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                qr_data: qrData,
                amount: document.getElementById('amount').value,
                branch_id: document.getElementById('branch_id').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('scan-result').innerHTML = 
                    '<div class="alert alert-success">' +
                    '<h5><i class="fas fa-check-circle me-2"></i>' + data.message + '</h5>' +
                    '<p><strong>{{ __("Discount") }}:</strong> ' + data.data.discount_amount + '</p>' +
                    '<p><strong>{{ __("Final Amount") }}:</strong> ' + data.data.final_amount + '</p>' +
                    '</div>';
            } else {
                document.getElementById('scan-result').innerHTML = 
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</div>';
            }
        })
        .catch(error => {
            document.getElementById('scan-result').innerHTML = 
                '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ __("An error occurred. Please try again.") }}</div>';
        });
    }

    // Cleanup scanner on page unload
    window.addEventListener('beforeunload', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
            });
        }
    });
</script>
@endpush
@endsection

