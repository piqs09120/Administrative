<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities Monitoring Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 5px;
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .card .number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .card.primary { border-left: 4px solid #3b82f6; }
        .card.warning { border-left: 4px solid #f59e0b; }
        .card.success { border-left: 4px solid #10b981; }
        .card.error { border-left: 4px solid #ef4444; }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #1e40af;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background: #f1f5f9;
            font-weight: bold;
            color: #475569;
        }
        .table tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-error { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .chart-placeholder {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #64748b;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facilities Monitoring Report</h1>
        <p>Generated on {{ $generated_at->format('F d, Y \a\t H:i:s') }}</p>
        <p>Report Period: {{ $date_range['start']->format('M d, Y') }} - {{ $date_range['end']->format('M d, Y') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card primary">
            <h3>Total Requests</h3>
            <div class="number">{{ $summary['total_requests'] }}</div>
        </div>
        <div class="card warning">
            <h3>In Progress</h3>
            <div class="number">{{ $summary['in_progress'] }}</div>
        </div>
        <div class="card success">
            <h3>Completed</h3>
            <div class="number">{{ $summary['completed'] }}</div>
        </div>
        <div class="card error">
            <h3>Urgent</h3>
            <div class="number">{{ $summary['urgent'] }}</div>
        </div>
    </div>

    <!-- Facility Statistics -->
    <div class="summary-cards">
        <div class="card primary">
            <h3>Total Facilities</h3>
            <div class="number">{{ $summary['total_facilities'] }}</div>
        </div>
        <div class="card success">
            <h3>Available</h3>
            <div class="number">{{ $summary['available_facilities'] }}</div>
        </div>
        <div class="card error">
            <h3>Occupied</h3>
            <div class="number">{{ $summary['occupied_facilities'] }}</div>
        </div>
    </div>

    <!-- Weekly Overview -->
    <div class="section">
        <h2>Weekly Requests Overview</h2>
        <div class="chart-placeholder">
            <h3>Weekly Request Counts</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weekly_data as $day)
                    <tr>
                        <td>{{ $day['day'] }}</td>
                        <td>{{ $day['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="section">
        <h2>Recent Requests</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Department</th>
                    <th>Facility</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_requests as $request)
                <tr>
                    <td>{{ $request['code'] }}</td>
                    <td>{{ ucfirst($request['type']) }}</td>
                    <td>
                        <span class="badge badge-{{ $request['priority'] === 'urgent' ? 'error' : ($request['priority'] === 'high' ? 'warning' : 'info') }}">
                            {{ ucfirst($request['priority']) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $request['status'] === 'completed' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'info') }}">
                            {{ ucfirst($request['status']) }}
                        </span>
                    </td>
                    <td>{{ $request['department'] ?? 'N/A' }}</td>
                    <td>{{ $request['facility'] ?? 'N/A' }}</td>
                    <td>{{ $request['created_at'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b;">No recent requests found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Facilities List -->
    <div class="section page-break">
        <h2>Facilities Overview</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Facility Name</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $facility)
                <tr>
                    <td>{{ $facility->name }}</td>
                    <td>{{ $facility->location }}</td>
                    <td>
                        <span class="badge badge-{{ $facility->status === 'available' ? 'success' : 'error' }}">
                            {{ ucfirst($facility->status) }}
                        </span>
                    </td>
                    <td>{{ $facility->description ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">No facilities found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the SOLIERA Facilities Management System</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>
</body>
</html>
