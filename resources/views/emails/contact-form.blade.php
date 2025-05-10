<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wiadomość z formularza kontaktowego</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .content {
            padding: 15px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .field-value {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nowa wiadomość z formularza kontaktowego</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="field-label">Imię i nazwisko:</div>
                <div class="field-value">{{ $name }}</div>
            </div>
            
            <div class="field">
                <div class="field-label">Adres e-mail:</div>
                <div class="field-value">{{ $email }}</div>
            </div>
            
            @if($phone)
            <div class="field">
                <div class="field-label">Telefon:</div>
                <div class="field-value">{{ $phone }}</div>
            </div>
            @endif
            
            <div class="field">
                <div class="field-label">Temat:</div>
                <div class="field-value">{{ $subject }}</div>
            </div>
            
            <div class="field">
                <div class="field-label">Treść wiadomości:</div>
                <div class="field-value">{{ $messageContent }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Ta wiadomość została wysłana automatycznie z formularza kontaktowego na stronie Samodzielny Publiczny Zakład Opieki Zdrowotnej w Obornikach (www.szpital.oborniki.info).</p>
        </div>
    </div>
</body>
</html>
