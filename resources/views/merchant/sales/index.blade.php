@extends('layouts.dashboard')

@section('title', __('Sales'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Sales') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Product Selection -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Select Product') }}</h6>
            </div>
            <div class="card-body">
                <div class="row" id="products-container">
                    @forelse($products as $product)
                        <div class="col-md-6 mb-3 product-item" data-product-id="{{ $product->id }}" data-product-name="{{ $product->localized_name }}" data-product-price="{{ $product->price }}" data-stock="{{ $product->track_stock ? $product->stock_quantity : 999 }}">
                            <div class="card h-100 border {{ $product->isAvailable() ? 'border-primary' : 'border-secondary' }}" style="cursor: pointer;" onclick="selectProduct({{ $product->id }}, {{ json_encode($product->localized_name) }}, {{ $product->price }}, {{ $product->track_stock ? $product->stock_quantity : 999 }})">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $product->localized_name }}</h6>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning"><i class="fas fa-star"></i></span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-2">{{ Str::limit($product->localized_description, 60) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary">﷼{{ number_format($product->price, 2) }}</strong>
                                        @if($product->isAvailable())
                                            <span class="badge bg-success">{{ __('Available') }}</span>
                                            @if($product->track_stock)
                                                <small class="text-muted">({{ $product->stock_quantity }} {{ __('in stock') }})</small>
                                            @endif
                                        @else
                                            <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>{{ __('No products available for sale.') }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Sale Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Process Sale') }}</h6>
            </div>
            <div class="card-body">
                <form id="saleForm">
                    @csrf
                    
                    <!-- Selected Product -->
                    <div class="mb-3" id="selected-product-container" style="display: none;">
                        <label class="form-label">{{ __('Selected Product') }}</label>
                        <div class="alert alert-info" id="selected-product-info">
                            <strong id="selected-product-name"></strong><br>
                            <small>{{ __('Price') }}: <span id="selected-product-price"></span> {{ __('SAR') }}</small>
                        </div>
                        <input type="hidden" id="product_id" name="product_id">
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="1" min="1" required>
                        <small class="text-muted" id="stock-info"></small>
                    </div>

                    <!-- Branch -->
                    @if($branches->count() > 0)
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">{{ __('Branch') }}</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">{{ __('Select Branch (optional)') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Total Amount -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Total Amount') }}</label>
                        <div class="alert alert-primary">
                            <strong id="total-amount">0.00</strong> {{ __('SAR') }}
                        </div>
                    </div>

                    <!-- QR Code Scanner -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Scan Customer Card') }} <span class="text-danger">*</span></label>
                        <div id="reader" style="width: 100%;"></div>
                        <button type="button" class="btn btn-secondary btn-sm mt-2 w-100" onclick="toggleManualEntry()">
                            <i class="fas fa-keyboard me-2"></i>{{ __('Enter Card Number Manually') }}
                        </button>
                    </div>

                    <!-- Manual Card Entry -->
                    <div class="mb-3" id="manual-entry-container" style="display: none;">
                        <label for="card_number" class="form-label">{{ __('Card Number') }}</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" 
                               placeholder="{{ __('Enter customer card number') }}">
                    </div>

                    <!-- QR Data (hidden) -->
                    <input type="hidden" id="qr_data" name="qr_data">

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg w-100" id="process-sale-btn" disabled>
                        <i class="fas fa-cash-register me-2"></i>{{ __('Process Sale') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Sale Result -->
        <div class="card shadow" id="sale-result" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Sale Result') }}</h6>
            </div>
            <div class="card-body" id="sale-result-content">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.css" rel="stylesheet">
<style>
    .product-item .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .product-item .card.selected {
        border: 3px solid #0d6efd !important;
        background-color: #f0f8ff;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let selectedProduct = null;
    let qrScanned = false;

    // Initialize QR Code Scanner
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        
        // Stop scanning
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                console.log("QR Code scanning stopped.");
            }).catch((err) => {
                console.log(err);
            });
        }

        // Set QR data
        document.getElementById('qr_data').value = decodedText;
        qrScanned = true;
        updateSubmitButton();
        
        // Show success message
        showAlert('success', '{{ __("QR Code scanned successfully!") }}');
        
        // Hide manual entry if shown
        document.getElementById('manual-entry-container').style.display = 'none';
    }

    function onScanFailure(error) {
        // Handle scan failure, ignore most errors
        // console.warn(`QR Code scan error: ${error}`);
    }

    // Start QR Scanner
    function startQRScanner() {
        html5QrCode = new Html5Qrcode("reader");
        
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        ).catch((err) => {
            console.error("Unable to start scanning", err);
            showAlert('warning', '{{ __("Unable to access camera. Please use manual entry.") }}');
        });
    }

    // Initialize scanner on page load
    document.addEventListener('DOMContentLoaded', function() {
        startQRScanner();
        
        // Update total on quantity change
        document.getElementById('quantity').addEventListener('input', updateTotal);
    });

    // Select Product
    function selectProduct(productId, productName, productPrice, stock) {
        selectedProduct = {
            id: productId,
            name: productName,
            price: productPrice,
            stock: stock
        };

        // Update UI
        document.getElementById('product_id').value = productId;
        document.getElementById('selected-product-name').textContent = productName;
        document.getElementById('selected-product-price').textContent = productPrice.toFixed(2);
        document.getElementById('selected-product-container').style.display = 'block';
        
        // Update quantity max
        const quantityInput = document.getElementById('quantity');
        quantityInput.max = stock;
        quantityInput.value = 1;
        
        // Update stock info
        if (stock < 999) {
            document.getElementById('stock-info').textContent = `{{ __('Available') }}: ${stock}`;
        } else {
            document.getElementById('stock-info').textContent = '';
        }

        // Highlight selected product
        document.querySelectorAll('.product-item .card').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');

        updateTotal();
        updateSubmitButton();
    }

    // Update Total
    function updateTotal() {
        if (!selectedProduct) return;

        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const total = selectedProduct.price * quantity;
        
        document.getElementById('total-amount').textContent = total.toFixed(2);
        updateSubmitButton();
    }

    // Toggle Manual Entry
    function toggleManualEntry() {
        const container = document.getElementById('manual-entry-container');
        if (container.style.display === 'none') {
            container.style.display = 'block';
            // Stop scanner
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.log(err));
            }
        } else {
            container.style.display = 'none';
            // Restart scanner
            startQRScanner();
        }
    }

    // Update Submit Button
    function updateSubmitButton() {
        const hasProduct = selectedProduct !== null;
        const hasQuantity = parseInt(document.getElementById('quantity').value) > 0;
        const hasQR = qrScanned || document.getElementById('card_number').value.trim() !== '';
        
        document.getElementById('process-sale-btn').disabled = !(hasProduct && hasQuantity && hasQR);
    }

    // Handle Manual Card Entry
    document.getElementById('card_number')?.addEventListener('input', function() {
        qrScanned = false;
        updateSubmitButton();
    });

    // Handle Form Submission
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = document.getElementById('process-sale-btn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Processing...") }}';

        // If manual card entry, use processSaleManual
        const cardNumber = document.getElementById('card_number').value.trim();
        let url = '{{ route("merchant.sales.process") }}';
        let method = 'POST';

        if (cardNumber && !qrScanned) {
            // Use manual entry endpoint
            formData.delete('qr_data');
            url = '{{ route("merchant.sales.process-manual") }}';
        }

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSaleResult(data);
                // Reset form
                resetForm();
                // Restart scanner
                setTimeout(() => {
                    startQRScanner();
                }, 2000);
            } else {
                showAlert('danger', data.message || '{{ __("An error occurred.") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', '{{ __("An error occurred. Please try again.") }}');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            updateSubmitButton();
        });
    });

    // Show Sale Result
    function showSaleResult(data) {
        const resultContainer = document.getElementById('sale-result');
        const resultContent = document.getElementById('sale-result-content');
        
        resultContent.innerHTML = `
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle me-2"></i>{{ __("Sale Completed Successfully!") }}</h5>
                <hr>
                <p><strong>{{ __("Customer") }}:</strong> ${data.data.customer.full_name}</p>
                <p><strong>{{ __("Product") }}:</strong> ${data.data.product.localized_name}</p>
                <p><strong>{{ __("Amount") }}:</strong> ﷼${parseFloat(data.data.amount).toFixed(2)}</p>
                <p><strong>{{ __("Points Earned") }}:</strong> ${data.data.points_earned || 0} {{ __("points") }}</p>
                <p><strong>{{ __("Transaction ID") }}:</strong> ${data.data.transaction.transaction_id}</p>
            </div>
        `;
        
        resultContainer.style.display = 'block';
        resultContainer.scrollIntoView({ behavior: 'smooth' });
        
        // Hide after 5 seconds
        setTimeout(() => {
            resultContainer.style.display = 'none';
        }, 5000);
    }

    // Reset Form
    function resetForm() {
        selectedProduct = null;
        qrScanned = false;
        document.getElementById('saleForm').reset();
        document.getElementById('selected-product-container').style.display = 'none';
        document.getElementById('manual-entry-container').style.display = 'none';
        document.getElementById('qr_data').value = '';
        document.querySelectorAll('.product-item .card').forEach(card => {
            card.classList.remove('selected');
        });
        updateSubmitButton();
    }

    // Show Alert
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
@endpush

