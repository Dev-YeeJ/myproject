<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Official Youth List</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 40px; text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.history.back()">Back to Dashboard</button>
    </div>

    <div class="header">
        <h2>KATIPUNAN NG KABATAAN (KK) REGISTRY</h2>
        <p>Barangay Calbueg, Malasiqui</p>
        <p><strong>Filter:</strong> {{ ucfirst($filter ?? 'All Members') }} | <strong>Date Generated:</strong> {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Civil Status</th>
                <th>Voter?</th>
                <th>Occupation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($youths as $index => $youth)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $youth->last_name }}, {{ $youth->first_name }}</td>
                <td>{{ $youth->calculated_age }}</td>
                <td>{{ $youth->gender }}</td>
                <td>{{ $youth->civil_status }}</td>
                <td>{{ $youth->is_registered_voter ? 'Yes' : 'No' }}</td>
                <td>{{ $youth->occupation }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Certified Correct:</p>
        <br><br>
        <p><strong>SK SECRETARY</strong></p>
    </div>

</body>
</html>