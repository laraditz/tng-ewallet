<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment status</title>
</head>
<body>
    @if ($state === 'not_found')
        <p>We couldn't find this payment.</p>
    @endif

    <a href="{{ $backUrl }}">Back</a>
</body>
</html>
