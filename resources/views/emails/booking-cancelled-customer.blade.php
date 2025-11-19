<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #EF4444; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .details-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .details-label { font-weight: bold; width: 140px; color: #6b7280; }
        .details-value { color: #111827; }
        .button { display: inline-block; background-color: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">❌ Booking Cancelled</h1>
    </div>
    
    <div class="content">
        <p>Hi {{ $appointment->customer->name }},</p>
        
        <p>Your appointment has been cancelled. Here are the details of the cancelled booking:</p>
        
        <div class="details">
            <div class="details-row">
                <div class="details-label">Business:</div>
                <div class="details-value">{{ $appointment->business->name }}</div>
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
        
        <p>We hope to see you again soon. You can book a new appointment anytime.</p>
        
        <center>
            <a href="{{ url('/businesses') }}" class="button">Book New Appointment</a>
        </center>
    </div>
    
    <div class="footer">
        <p>Thank you for using Appointifi!</p>
        <p style="font-size: 12px; color: #9ca3af;">This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>
