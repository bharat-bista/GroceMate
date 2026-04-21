<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply from GroceMate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
        }
        .message-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .message-box h2 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #1f2937;
        }
        .message-text {
            white-space: pre-line;
            color: #374151;
            font-size: 14px;
        }
        .context {
            border-top: 1px solid #e5e7eb;
            padding-top: 14px;
            margin-top: 14px;
            font-size: 14px;
            color: #4b5563;
        }
        .context strong {
            color: #1f2937;
        }
        .footer {
            text-align: center;
            padding: 18px;
            color: #6b7280;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Message from GroceMate</h1>
        <p>Regarding your contact request</p>
    </div>

    <div class="message-box">
        <h2>Our response</h2>
        <div class="message-text">{{ $messageBody }}</div>

        <div class="context">
            <div><strong>Subject:</strong> {{ $contactMessage->subject }}</div>
            <div><strong>Sent on:</strong> {{ $contactMessage->created_at->format('M d, Y h:i A') }}</div>
        </div>
    </div>

    <div class="footer">
        Thank you for reaching out to GroceMate.
    </div>
</body>
</html>
