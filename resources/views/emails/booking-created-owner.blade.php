<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Received</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10B981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .details-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .details-label { font-weight: bold; width: 140px; color: #6b7280; }
        .details-value { color: #111827; }
        .button { display: inline-block; background-color: #10B981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">🔔 New Booking Received!</h1>
    </div>
    
    <div class="content">
        <p>Hi {{ $appointment->business->owner->name }},</p>
        
        <p>You have a new booking for <strong>{{ $appointment->business->name }}</strong>.</p>
        
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
            <div class="details-row">
                <div class="details-label">Duration:</div>
                <div class="details-value">{{ $appointment->service->duration }} minutes</div>
            </div>
            @if($appointment->service->price)
            <div class="details-row">
                <div class="details-label">Price:</div>
                <div class="details-value">RM {{ number_format($appointment->service->price, 2) }}</div>
            </div>
            @endif
        </div>
        
        <p>Please prepare for this appointment. The customer has been sent a confirmation email.</p>
        
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
