<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status Updated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10B981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .details-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .details-label { font-weight: bold; width: 140px; color: #6b7280; }
        .details-value { color: #111827; }
        .status-change { background-color: #FEF3C7; padding: 10px; border-radius: 6px; margin: 10px 0; text-align: center; }
        .button { display: inline-block; background-color: #10B981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">📅 Appointment Status Updated</h1>
    </div>

    <div class="content">
        <p>Hi {{ $appointment->business->owner->name ?? $appointment->business->owner->email }},</p>

        <p>You have updated the status of an appointment for <strong>{{ $appointment->customer->name }}</strong>.</p>

        <div class="status-change">
            <strong>Appointment Status Changed:</strong><br>
            <span style="text-decoration: line-through;">{{ ucfirst($oldStatus) }}</span> → <strong>{{ ucfirst($newStatus) }}</strong>
        </div>

        <div class="details">
            <div class="details-row">
                <div class="details-label">Customer:</div>
                <div class="details-value">{{ $appointment->customer->name }}</div>
            </div>
            <div class="details-row">
                <div class="details-label">Email:</div>
                <div class="details-value">{{ $appointment->customer->email }}</div>
            </div>
            <div class="details-row">
                <div class="details-label">Service:</div>
                <div class="details-value">{{ $appointment->service->name }}</div>
            </div>
            <div class="details-row">
                <div class="details-label">Date:</div>
                <div class="details-value">{{ $appointment->start_time->format('l, F j, Y') }}</div>
            </div>
            <div class="details-row">
                <div class="details-label">Time:</div>
                <div class="details-value">{{ $appointment->start_time->format('g:i A') }}</div>
            </div>
        </div>

        @if($newStatus === 'completed')
        <p>The appointment has been marked as completed. Payment should have been collected.</p>
        @elseif($newStatus === 'cancelled')
        <p>The appointment has been cancelled. This time slot is now available for other customers to book.</p>
        @elseif($newStatus === 'no_show')
        <p>The customer did not show up for the appointment. Consider following up with them.</p>
        @elseif($newStatus === 'arrival')
        <p>The customer has arrived. The service can now be provided.</p>
        @else
        <p>The appointment status is now "{{ ucfirst($newStatus) }}".</p>
        @endif

        <center>
            <a href="{{ url('/business/dashboard') }}" class="button">View Dashboard</a>
        </center>
    </div>

    <div class="footer">
        <p>Appointifi - Your Business Management System</p>
        <p style="font-size: 12px; color: #9ca3af;">This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>