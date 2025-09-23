<!-- M-Pesa Payment Component -->
<div class="mpesa-payment-container" id="mpesaPayment" style="display: none;">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-mobile-alt me-2"></i>
                M-Pesa Payment
            </h5>
        </div>
        <div class="card-body">
            <div id="mpesaStep1" class="payment-step">
                <div class="text-center mb-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" 
                         alt="M-Pesa" class="img-fluid" style="max-height: 60px;">
                </div>
                
                <form id="mpesaPaymentForm">
                    <div class="mb-3">
                        <label for="mpesaPhone" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text">+254</span>
                            <input type="tel" class="form-control" id="mpesaPhone" 
                                   placeholder="712345678" pattern="[0-9]{9}" required>
                        </div>
                        <div class="form-text">Enter your M-Pesa registered phone number</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Order Total:</span>
                            <strong id="mpesaAmount">KSh {{ number_format($total ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="mpesaSubmitBtn">
                        <i class="fas fa-paper-plane me-2"></i>
                        Send Payment Request
                    </button>
                </form>
            </div>
            
            <div id="mpesaStep2" class="payment-step" style="display: none;">
                <div class="text-center">
                    <div class="spinner-border text-success mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="text-success">Payment Request Sent!</h5>
                    <p class="mb-3">Check your phone and enter your M-Pesa PIN to complete the payment.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Next Steps:</strong>
                        <ol class="mb-0 mt-2 text-start">
                            <li>Check your phone for M-Pesa notification</li>
                            <li>Enter your M-Pesa PIN</li>
                            <li>Wait for confirmation</li>
                        </ol>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" id="checkStatusBtn">
                            <i class="fas fa-sync-alt me-2"></i>
                            Check Payment Status
                        </button>
                        <button class="btn btn-outline-secondary" id="cancelPaymentBtn">
                            Cancel Payment
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="mpesaStep3" class="payment-step" style="display: none;">
                <div class="text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h5 class="text-success">Payment Successful!</h5>
                    <p class="mb-3">Your M-Pesa payment has been confirmed.</p>
                    
                    <div class="alert alert-success">
                        <strong>Transaction Details:</strong>
                        <div id="transactionDetails" class="mt-2"></div>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="window.location.href='{{ route('orderList') }}'">
                        <i class="fas fa-receipt me-2"></i>
                        View Order & Receipt
                    </button>
                </div>
            </div>
            
            <div id="mpesaError" class="payment-step" style="display: none;">
                <div class="text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h5 class="text-danger">Payment Failed</h5>
                    <p class="mb-3" id="errorMessage">Something went wrong with your payment.</p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" id="retryPaymentBtn">
                            <i class="fas fa-redo me-2"></i>
                            Try Again
                        </button>
                        <button class="btn btn-outline-secondary" onclick="selectPaymentMethod('other')">
                            Choose Different Payment Method
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let checkoutRequestId = null;
    let statusCheckInterval = null;
    
    // M-Pesa Payment Form Handler
    document.getElementById('mpesaPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phone = document.getElementById('mpesaPhone').value;
        const orderCode = '{{ $orderCode ?? "" }}';
        
        if (!phone || !orderCode) {
            showError('Please enter a valid phone number');
            return;
        }
        
        // Format phone number
        const formattedPhone = formatPhoneNumber(phone);
        
        // Show loading state
        document.getElementById('mpesaSubmitBtn').innerHTML = 
            '<i class="fas fa-spinner fa-spin me-2"></i>Sending Request...';
        document.getElementById('mpesaSubmitBtn').disabled = true;
        
        // Send payment request
        fetch('/api/mpesa/initiate-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                order_code: orderCode,
                phone_number: formattedPhone
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkoutRequestId = data.data.checkout_request_id;
                showStep('mpesaStep2');
                startStatusCheck();
            } else {
                showError(data.message || 'Payment request failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error. Please try again.');
        })
        .finally(() => {
            // Reset button
            document.getElementById('mpesaSubmitBtn').innerHTML = 
                '<i class="fas fa-paper-plane me-2"></i>Send Payment Request';
            document.getElementById('mpesaSubmitBtn').disabled = false;
        });
    });
    
    // Check Status Button
    document.getElementById('checkStatusBtn').addEventListener('click', function() {
        if (checkoutRequestId) {
            checkPaymentStatus();
        }
    });
    
    // Cancel Payment Button
    document.getElementById('cancelPaymentBtn').addEventListener('click', function() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
        showStep('mpesaStep1');
    });
    
    // Retry Payment Button
    document.getElementById('retryPaymentBtn').addEventListener('click', function() {
        showStep('mpesaStep1');
    });
    
    function formatPhoneNumber(phone) {
        // Remove any non-numeric characters
        phone = phone.replace(/\D/g, '');
        
        // Add 254 prefix if not present
        if (phone.startsWith('0')) {
            phone = '254' + phone.substring(1);
        } else if (!phone.startsWith('254')) {
            phone = '254' + phone;
        }
        
        return phone;
    }
    
    function showStep(stepId) {
        // Hide all steps
        document.querySelectorAll('.payment-step').forEach(step => {
            step.style.display = 'none';
        });
        
        // Show selected step
        document.getElementById(stepId).style.display = 'block';
    }
    
    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        showStep('mpesaError');
    }
    
    function startStatusCheck() {
        // Check status every 5 seconds
        statusCheckInterval = setInterval(checkPaymentStatus, 5000);
        
        // Stop checking after 2 minutes
        setTimeout(() => {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                showError('Payment timeout. Please try again.');
            }
        }, 120000);
    }
    
    function checkPaymentStatus() {
        if (!checkoutRequestId) return;
        
        fetch('/api/mpesa/check-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                checkout_request_id: checkoutRequestId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const resultCode = data.data.ResultCode;
                
                if (resultCode === '0') {
                    // Payment successful
                    clearInterval(statusCheckInterval);
                    showPaymentSuccess(data.data);
                } else if (resultCode !== undefined && resultCode !== '0') {
                    // Payment failed
                    clearInterval(statusCheckInterval);
                    showError('Payment was cancelled or failed. Please try again.');
                }
                // If no result code, payment is still pending
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
        });
    }
    
    function showPaymentSuccess(transactionData) {
        // Show transaction details
        const details = `
            <div class="text-start">
                <small><strong>Transaction ID:</strong> ${transactionData.MpesaReceiptNumber || 'N/A'}</small><br>
                <small><strong>Amount:</strong> KSh ${transactionData.Amount || 'N/A'}</small><br>
                <small><strong>Phone:</strong> ${transactionData.PhoneNumber || 'N/A'}</small>
            </div>
        `;
        document.getElementById('transactionDetails').innerHTML = details;
        
        showStep('mpesaStep3');
        
        // Redirect to success page after 3 seconds
        setTimeout(() => {
            window.location.href = '{{ route("orderList") }}';
        }, 3000);
    }
});

// Function to show M-Pesa payment option
function selectMpesaPayment() {
    document.getElementById('mpesaPayment').style.display = 'block';
    // Hide other payment methods
    document.querySelectorAll('.payment-method:not(#mpesaPayment)').forEach(method => {
        method.style.display = 'none';
    });
}
</script>

<style>
.mpesa-payment-container {
    max-width: 500px;
    margin: 0 auto;
}

.payment-step {
    min-height: 200px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.alert ol {
    padding-left: 1.2rem;
}

@media (max-width: 576px) {
    .mpesa-payment-container {
        margin: 0 10px;
    }
}
</style>