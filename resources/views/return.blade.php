<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment status</title>
</head>
<body>
    @if ($state === 'not_found')
        <p>We couldn't find this payment.</p>
    @elseif ($state === 'status')
        <p>Status: {{ $paymentStatus }}</p>
        <p>Amount: {{ $paymentAmount['currency'] ?? '' }} {{ $paymentAmount['value'] ?? '' }}</p>
        <p>Payment reference: {{ $paymentRequestId }}</p>
        <p>Date/time: {{ $paymentTime }}</p>
        @if (! is_null($paymentFailReason))
            <p>Reason: {{ $paymentFailReason }}</p>
        @endif
    @elseif ($state === 'inquiry_failed')
        <p>We couldn't confirm this payment's status right now.</p>
    @endif

    <a href="{{ $backUrl }}">Back</a>
</body>
</html>
