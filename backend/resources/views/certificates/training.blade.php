<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; text-align: center; padding: 60px 40px; }
        .border { border: 3px solid #1e40af; padding: 40px; margin: 20px; }
        h1 { color: #1e40af; font-size: 28px; margin-bottom: 10px; }
        h2 { color: #374151; font-size: 22px; margin: 20px 0; }
        .name { font-size: 26px; color: #1e40af; font-weight: bold; margin: 20px 0; }
        .details { font-size: 14px; color: #6b7280; margin: 5px 0; }
        .score { font-size: 18px; font-weight: bold; margin: 15px 0; }
        .cert-number { font-size: 12px; color: #9ca3af; margin-top: 40px; }
        .footer { margin-top: 50px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="border">
        <h1>Connex</h1>
        <h2>Training Completion Certificate</h2>
        <p class="details">This certifies that</p>
        <p class="name">{{ $worker_name }}</p>
        <p class="details">has successfully completed</p>
        <p class="details"><strong>{{ $training_name }}</strong></p>
        <p class="details">{{ $start_date }} - {{ $end_date }}</p>
        <p class="score">Score: {{ $score }} | Classification: {{ $classification }}</p>
        <p class="cert-number">Certificate No: {{ $cert_number }}</p>
        <div class="footer">
            <p>Issued by Connex Resource Management System</p>
            <p>Date: {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
</body>
</html>
