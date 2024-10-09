

<div id="form-container">
    <div id="sq-card-number"></div>
    <div id="sq-expiration-date"></div>
    <div id="sq-cvv"></div>
    <div id="sq-postal-code"></div>
    <button id="sq-creditcard" class="button-credit-card" onclick="onGetCardNonce(event)">Pay ${{ number_format($amountDue, 2) }}</button>
</div>
<script>
    // INITIALIZE PAYMENT FORM
    const paymentForm = new SqPaymentForm({
        applicationId: 'EAAAl6MMjFcuabAMK2q4fDDZpW14MQ-agdsVCv1MKihd_jAL8u8llxm4JXAQX8Pv',
        inputClass: 'sq-input',
        autoBuild: false,
        cardNumber: {
            elementId: 'sq-card-number',
            placeholder: 'Card Number'
        },
        cvv: {
            elementId: 'sq-cvv',
            placeholder: 'CVV'
        },
        expirationDate: {
            elementId: 'sq-expiration-date',
            placeholder: 'MM/YY'
        },
        postalCode: {
            elementId: 'sq-postal-code',
            placeholder: 'Postal'
        },
        callbacks: {
            cardNonceResponseReceived: function(errors, nonce, cardData) {
                if (errors) {
                    // Handle errors
                    console.error(errors);
                    return;
                }
                // Send the nonce to your server
                fetch('/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nonce: nonce })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Handle server response
                        console.log(data);
                    });
            }
        }
    });

    paymentForm.build();

    //payment process
    // Example using Node.js and Express
    const express = require('express');
    const bodyParser = require('body-parser');
    const { Client, Environment } = require('square');

    const app = express();
    app.use(bodyParser.json());

    const client = new Client({
        environment: Environment.Sandbox, // Use Environment.Production for live
        accessToken: 'EAAAl6MMjFcuabAMK2q4fDDZpW14MQ-agdsVCv1MKihd_jAL8u8llxm4JXAQX8Pv'
    });

    app.post('/process-payment', async (req, res) => {
        const { nonce } = req.body;

        try {
            const response = await client.paymentsApi.createPayment({
                sourceId: nonce,
                idempotencyKey: new Date().getTime().toString(),
                amountMoney: {
                    amount: 100, // Amount in cents
                    currency: 'USD'
                }
            });
            res.json(response.result);
        } catch (error) {
            res.status(500).json(error);
        }
    });

    app.listen(3000, () => {
        console.log('Server is running on port 3000');
    });

</script>
