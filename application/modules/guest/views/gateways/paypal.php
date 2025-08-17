<div class="container">
    <div id="paypal-buttons" class="col-xs-12 col-md-8 col-md-offset-2"></div>

    <?php $adv_enabled = !empty($advanced_credit_cards); ?>
    <?php $venmo_enabled = !empty($venmo); ?>
    <?php if ($adv_enabled): ?>
        <div id="card-fields" class="col-xs-12 col-md-8 col-md-offset-2" style="margin-top:20px;">
            <h4 style="margin-bottom:10px;">Pay with credit or debit card</h4>

            <!-- Validation Error Display -->
            <div id="card-error-container" class="alert alert-danger" style="display:none; margin-bottom:20px;">
                <div class="error-header">
                    <i class="fa fa-exclamation-circle" style="margin-right:8px;"></i>
                    <strong>Please correct the following errors:</strong>
                </div>
                <ul id="card-error-list" style="margin-top:10px; margin-bottom:0; padding-left:20px;"></ul>
                <div id="card-error-message" style="margin-top:10px; display:none;"></div>
            </div>

            <div id="card-name-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-number-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-expiry-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-cvv-field-container" class="form-group" style="margin-bottom:10px;"></div>

            <button id="card-submit" type="button" class="btn btn-primary">Pay with Card</button>
            <span id="card-spinner" style="display:none; margin-left:10px;" role="status" aria-live="polite" aria-label="Processing…">
              <span style="
                display:inline-block; width:16px; height:16px; border:2px solid #ccc; border-top-color:#333; border-radius:50%;
                animation: ip-spin 0.7s linear infinite; vertical-align:middle;">
              </span>
              <span style="margin-left:6px; vertical-align:middle;">Processing…</span>
            </span>
        </div>
    <?php endif; ?>
</div>
<style>
    @keyframes ip-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* Error styling */
    #card-error-container {
        border-left: 4px solid #dc3545;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .error-header {
        font-weight: 500;
    }

    #card-error-list li {
        margin-bottom: 5px;
    }

    /* Field validation states */
    .field-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
</style>
<script>
    // Flags from PHP
    const ADV_ENABLED = <?php echo $adv_enabled ? 'true' : 'false'; ?>;
    const VENMO_ENABLED = <?php echo $venmo_enabled ? 'true' : 'false'; ?>;

    // Decide which SDK components to request
    const SDK_COMPONENTS = "<?php echo $adv_enabled ? 'buttons,card-fields' : 'buttons'; ?>";

    // Build SDK URL conditionally with Venmo
    const sdkBase = "https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=<?php echo $currency; ?>&intent=capture&components=" + SDK_COMPONENTS;
    const sdkUrl = VENMO_ENABLED ? (sdkBase + "&enable-funding=venmo") : sdkBase;

    // Error parsing and handling functions
    function parsePayPalError(errorObj) {
        console.log('Parsing PayPal error:', errorObj);

        // Handle both client-side and server-side errors.
        if (!errorObj) return { errors: [], message: 'Unknown error occurred' };

        // Type 1: Direct validation errors with details array (client-side validation)
        if (errorObj.details && Array.isArray(errorObj.details)) {
            return {
                errors: errorObj.details.map(detail => ({
                    field: mapPayPalFieldPath(detail.field),
                    message: detail.description || detail.issue || 'Invalid value',
                    raw: detail
                })),
                message: errorObj.message || 'Validation failed'
            };
        }

        // Type 2: Error message contains JSON response (server-side API errors)
        if (errorObj.message && typeof errorObj.message === 'string') {
            const jsonMatch = errorObj.message.match(/\{[\s\S]*\}$/);
            if (jsonMatch) {
                try {
                    const parsedError = JSON.parse(jsonMatch[0]);
                    if (parsedError.details && Array.isArray(parsedError.details)) {
                        return {
                            errors: parsedError.details.map(detail => ({
                                field: mapPayPalFieldPath(detail.field),
                                message: detail.description || detail.issue || 'Invalid value',
                                raw: detail
                            })),
                            message: parsedError.message || 'Payment validation failed'
                        };
                    }
                } catch (e) {
                    console.log('Failed to parse JSON from error message:', e);
                }
            }

            // Return the message as-is if no JSON found
            return {
                errors: [],
                message: errorObj.message
            };
        }

        // Type 3: Simple string error
        if (typeof errorObj === 'string') {
            // Check if the string itself contains JSON
            const jsonMatch = errorObj.match(/\{[\s\S]*\}$/);
            if (jsonMatch) {
                try {
                    const parsedError = JSON.parse(jsonMatch[0]);
                    if (parsedError.details && Array.isArray(parsedError.details)) {
                        return {
                            errors: parsedError.details.map(detail => ({
                                field: mapPayPalFieldPath(detail.field),
                                message: detail.description || detail.issue || 'Invalid value',
                                raw: detail
                            })),
                            message: parsedError.message || 'Payment validation failed'
                        };
                    }
                } catch (e) {
                    console.log('Failed to parse JSON from error string:', e);
                }
            }

            return {
                errors: [],
                message: errorObj
            };
        }

        // Fallback
        return {
            errors: [],
            message: errorObj.message || errorObj.toString() || 'Payment processing error'
        };
    }

    function mapPayPalFieldPath(fieldPath) {
        if (!fieldPath) return null;

        // Map PayPal API field paths to our field names
        const fieldMapping = {
            '/payment_source/card/number': 'number',
            '/payment_source/card/expiry': 'expiry',
            '/payment_source/card/security_code': 'cvv',
            '/payment_source/card/name': 'name',
            'number': 'number',
            'expiry': 'expiry',
            'expirationDate': 'expiry',
            'cvv': 'cvv',
            'securityCode': 'cvv',
            'name': 'name'
        };

        return fieldMapping[fieldPath] || fieldPath.replace(/^\/.*\//, '');
    }

    function showCardError(message, errors = []) {
        const errorContainer = document.getElementById('card-error-container');
        const errorList = document.getElementById('card-error-list');
        const errorMessage = document.getElementById('card-error-message');

        if (!errorContainer) return;

        // Clear previous errors
        errorList.innerHTML = '';
        errorMessage.style.display = 'none';

        if (errors.length > 0) {
            // Show structured field errors
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = formatFieldError(error);
                errorList.appendChild(li);
            });
        } else if (message) {
            // Show general error message
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }

        errorContainer.style.display = 'block';
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideCardError() {
        const errorContainer = document.getElementById('card-error-container');
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
    }

    function formatFieldError(error) {
        // Map PayPal field names to user-friendly names
        const fieldNames = {
            'name': 'Cardholder Name',
            'number': 'Card Number',
            'expiry': 'Expiration Date',
            'cvv': 'CVV',
            'expirationDate': 'Expiration Date',
            'securityCode': 'CVV'
        };

        if (typeof error === 'string') {
            return error;
        }

        if (error.field && error.message) {
            const fieldName = fieldNames[error.field] || error.field;
            return `${fieldName}: ${error.message}`;
        }

        if (error.message) {
            return error.message;
        }

        return 'Invalid field value';
    }

    function highlightFieldErrors(errors = []) {
        // Remove existing field highlights
        const fieldContainers = [
            'card-name-field-container',
            'card-number-field-container',
            'card-expiry-field-container',
            'card-cvv-field-container'
        ];

        fieldContainers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                const iframe = container.querySelector('iframe');
                if (iframe) {
                    iframe.classList.remove('field-error');
                }
            }
        });

        // Highlight error fields
        errors.forEach(error => {
            if (error.field) {
                const fieldMap = {
                    'name': 'card-name-field-container',
                    'number': 'card-number-field-container',
                    'expiry': 'card-expiry-field-container',
                    'expirationDate': 'card-expiry-field-container',
                    'cvv': 'card-cvv-field-container',
                    'securityCode': 'card-cvv-field-container'
                };

                const containerId = fieldMap[error.field];
                if (containerId) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        const iframe = container.querySelector('iframe');
                        if (iframe) {
                            iframe.classList.add('field-error');
                        }
                    }
                }
            }
        });
    }

    // Unified error handling function
    function handlePayPalError(errorObj, context = 'general') {
        console.log(`PayPal error in ${context}:`, errorObj);

        const parsed = parsePayPalError(errorObj);

        if (parsed.errors.length > 0) {
            highlightFieldErrors(parsed.errors);
            showCardError('', parsed.errors);
        } else {
            showCardError(parsed.message);
        }
    }

    $.ajax({
        url: sdkUrl,
        dataType: "script",
        cache: true,
        success: () => { initPayPal(); }
    });

    function initPayPal() {
        // --- Standard PayPal buttons (PayPal / Venmo / Pay Later) ---
        paypal.Buttons({
            createOrder() {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>', { method: "GET" })
                    .then((response) => response.json())
                    .then((order) => order.id);
            },
            onApprove: function (data) {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>' + data.orderID, { method: 'GET' })
                    .then((response) => {
                        if (!response.ok) throw new Error('Capture failed with HTTP ' + response.status);
                        window.location.replace('<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>');
                    })
                    .catch((err) => {
                        console.error('Buttons capture error:', err);
                        window.location.replace('<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>');
                    });
            },
            onError: function (error) {
                console.log('error on initPayPal', error);
                window.location.replace('<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>');
            }
        }).render('#paypal-buttons');

        // --- Advanced Card Fields: only if enabled and available in SDK ---
        if (ADV_ENABLED) {
            const cardSection = document.getElementById('card-fields');

            if (typeof paypal.CardFields !== 'function') {
                // SDK didn't expose CardFields (merchant/app not approved) – hide the section
                if (cardSection) cardSection.style.display = 'none';
                return;
            }

            const cardFields = paypal.CardFields({
                createOrder() {
                    return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>', { method: 'GET' })
                        .then((res) => res.json())
                        .then((order) => order.id);
                },
                onApprove(data) {
                    return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>' + data.orderID, { method: 'GET' })
                        .then((res) => {
                            if (!res.ok) throw new Error('Capture failed with HTTP ' + res.status);
                            window.location.replace('<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>');
                        })
                        .catch((err) => {
                            console.error('Capture error:', err);
                            setCardProcessing(false);
                            showCardError('Payment capture failed. Please try again or use a different payment method.');
                        });
                },
                onError(err) {
                    setCardProcessing(false);
                    handlePayPalError(err, 'cardFields.onError');
                }
            });

            // Render secure fields
            cardFields.NameField().render('#card-name-field-container');
            cardFields.NumberField().render('#card-number-field-container');
            cardFields.ExpiryField().render('#card-expiry-field-container');
            cardFields.CVVField().render('#card-cvv-field-container');

            // Spinner + disable handling
            const submitBtn = document.getElementById('card-submit');
            const spinnerEl = document.getElementById('card-spinner');
            function setCardProcessing(isProcessing) {
                if (submitBtn) submitBtn.disabled = isProcessing;
                if (spinnerEl) spinnerEl.style.display = isProcessing ? 'inline-block' : 'none';

                // Clear errors when starting processing
                if (isProcessing) {
                    hideCardError();
                }
            }

            // Click handler
            if (submitBtn) {
                submitBtn.addEventListener('click', function () {
                    setCardProcessing(true);
                    Promise.resolve(cardFields.submit())
                        .catch((err) => {
                            setCardProcessing(false);
                            handlePayPalError(err, 'cardFields.submit');
                        });
                });
            }
        }
    }
</script>
