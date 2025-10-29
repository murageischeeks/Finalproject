<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #007bff;
        }
        .footer {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $appointment->patient->name ?? 'Patient' }},</h2>

        <p>Your appointment with <strong>Dr. {{ $appointment->doctor->name ?? 'Unknown Doctor' }}</strong> scheduled on
        <strong>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('l, j F Y g:i A') }}</strong> has been
        <strong>{{ ucfirst($status) }}</strong>.</p>

        @if($status === 'approved' || $status === 'in_progress')
            <p>Please arrive 10–15 minutes early. You can check your appointment details in your BleakHospital dashboard.</p>
        @elseif($status === 'cancelled')
            <p>We’re sorry, but your appointment has been cancelled. You can reschedule anytime through your account.</p>
        @elseif($status === 'completed')
            <p>Thank you for attending. We hope your consultation went well.</p>
        @endif

        <p class="footer">— The BleakHospital Team</p>
    </div>
</body>
</html>
