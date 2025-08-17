// PayPal Payment Forms Handler
(function() {
    'use strict';

    // Wait for DOM and config to be ready
    if (typeof window.PayPalConfig === 'undefined') {
        console.error('PayPal configuration not found');
        return;
    }

    const config = window.PayPalConfig;

    // Build SDK components and URL
    const SDK_COMPONENTS = config.advEnabled ? 'buttons,card-fields' : 'buttons';
    const sdkBase = `https://www.paypal.com/sdk/js?client-id=${config.clientId}&currency=${config.currency}&intent=capture&components=${SDK_COMPONENTS}`;
    const sdkUrl = config.venmoEnabled ? (sdkBase + '&enable-funding=venmo') : sdkBase;

    // Error parsing and handling functions
    function parsePayPalError(errorObj) {
        console.log('Parsing PayPal error:', errorObj);

        if (!errorObj) return { errors: [], message: 'Unknown error occurred' };

        // Type 1: Direct validation errors with details array
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

        // Type 2: Error message contains JSON response
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
            return { errors: [], message: errorObj.message };
        }

        // Type 3: Simple string error
        if (typeof errorObj === 'string') {
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
            return { errors: [], message: errorObj };
        }

        return {
            errors: [],
            message: errorObj.message || errorObj.toString() || 'Payment processing error'
        };
    }

    function mapPayPalFieldPath(fieldPath) {
        if (!fieldPath) return null;

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

        errorList.innerHTML = '';
        errorMessage.style.display = 'none';

        if (errors.length > 0) {
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = formatFieldError(error);
                errorList.appendChild(li);
            });
        } else if (message) {
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
        const fieldNames = {
            'name': 'Cardholder Name',
            'number': 'Card Number',
            'expiry': 'Expiration Date',
            'cvv': 'CVV',
            'expirationDate': 'Expiration Date',
            'securityCode': 'CVV'
        };

        if (typeof error === 'string') return error;
        if (error.field && error.message) {
            const fieldName = fieldNames[error.field] || error.field;
            return `${fieldName}: ${error.message}`;
        }
        if (error.message) return error.message;
        return 'Invalid field value';
    }

    function highlightFieldErrors(errors = []) {
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
                if (iframe) iframe.classList.remove('field-error');
            }
        });

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
                        if (iframe) iframe.classList.add('field-error');
                    }
                }
            }
        });
    }

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

    function initPayPal() {
        // Standard PayPal buttons
        paypal.Buttons({
            createOrder() {
                return fetch(config.createOrderUrl, { method: "GET" })
                    .then(response => response.json())
                    .then(order => order.id);
            },
            onApprove: function(data) {
                return fetch(config.capturePaymentUrl + data.orderID, { method: 'GET' })
                    .then(response => {
                        if (!response.ok) throw new Error('Capture failed with HTTP ' + response.status);
                        window.location.replace(config.successUrl);
                    })
                    .catch(err => {
                        console.error('Buttons capture error:', err);
                        window.location.replace(config.errorUrl);
                    });
            },
            onError: function(error) {
                console.log('error on initPayPal', error);
                window.location.replace(config.errorUrl);
            }
        }).render('#paypal-buttons');

        // Advanced Card Fields
        if (config.advEnabled) {
            const cardSection = document.getElementById('card-fields');

            if (typeof paypal.CardFields !== 'function') {
                if (cardSection) cardSection.style.display = 'none';
                return;
            }

            const cardFields = paypal.CardFields({
                createOrder() {
                    return fetch(config.createOrderUrl, { method: 'GET' })
                        .then(res => res.json())
                        .then(order => order.id);
                },
                onApprove(data) {
                    return fetch(config.capturePaymentUrl + data.orderID, { method: 'GET' })
                        .then(res => {
                            if (!res.ok) throw new Error('Capture failed with HTTP ' + res.status);
                            window.location.replace(config.successUrl);
                        })
                        .catch(err => {
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

            // Render fields
            cardFields.NameField().render('#card-name-field-container');
            cardFields.NumberField().render('#card-number-field-container');
            cardFields.ExpiryField().render('#card-expiry-field-container');
            cardFields.CVVField().render('#card-cvv-field-container');

            // Processing state management
            const submitBtn = document.getElementById('card-submit');
            const spinnerEl = document.getElementById('card-spinner');

            function setCardProcessing(isProcessing) {
                if (submitBtn) submitBtn.disabled = isProcessing;
                if (spinnerEl) spinnerEl.style.display = isProcessing ? 'inline-block' : 'none';
                if (isProcessing) hideCardError();
            }

            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    setCardProcessing(true);
                    Promise.resolve(cardFields.submit())
                        .catch(err => {
                            setCardProcessing(false);
                            handlePayPalError(err, 'cardFields.submit');
                        });
                });
            }
        }
    }

    // Load PayPal SDK and initialize
    $.ajax({
        url: sdkUrl,
        dataType: "script",
        cache: true,
        success: initPayPal
    });

})();
