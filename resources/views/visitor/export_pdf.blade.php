<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Visitor Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Visitor Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Purpose</th>
                <th>Facility ID</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visitors as $visitor)
                <tr>
                    <td>{{ $visitor->id }}</td>
                    <td>{{ $visitor->name }}</td>
                    <td>{{ $visitor->contact }}</td>
                    <td>{{ $visitor->purpose }}</td>
                    <td>{{ $visitor->facility_id }}</td>
                    <td>{{ $visitor->time_in }}</td>
                    <td>{{ $visitor->time_out }}</td>
                    <td>{{ $visitor->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 