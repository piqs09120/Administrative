<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Visitor Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .summary h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #333;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .summary-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .visitor-details {
            margin-top: 30px;
        }
        .visitor-details h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #333;
        }
        .visitor-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .visitor-table th,
        .visitor-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .visitor-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .visitor-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .section {
            margin-top: 24px;
        }
        .section h2 { font-size: 18px; margin: 0 0 12px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        .table th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visitor Report</h1>
        <p>Generated on: {{ $generated_at->format('F d, Y \a\t h:i A') }}</p>
        <p>Date Range: {{ $date_range['start']->format('F d, Y') }} - {{ $date_range['end']->format('F d, Y') }}</p>
    </div>

    @if($include_statistics)
    <div class="summary">
        <h2>Report Summary</h2>
        <table class="summary-table">
            <tr>
                <td>Total Visitors:</td>
                <td>{{ $statistics['total_visitors'] }}</td>
            </tr>
            <tr>
                <td>Currently In Building:</td>
                <td>{{ $statistics['currently_in'] }}</td>
            </tr>
            <tr>
                <td>Completed Visits:</td>
                <td>{{ $statistics['completed_visits'] }}</td>
            </tr>
            <tr>
                <td>Average Duration:</td>
                <td>{{ $statistics['average_duration'] }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Analytics Sections -->
    <div class="section">
        <h2>Peak Visiting Hours</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Hour</th>
                    <th>Visitor Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($peak_hours ?? []) as $h)
                    <tr>
                        <td>{{ sprintf('%02d:00', $h['hour']) }}</td>
                        <td>{{ (int)($h['count'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Departments with Most Visitors</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $deptTotal = collect($departments ?? [])->sum('count'); @endphp
                @forelse(($departments ?? []) as $d)
                    <tr>
                        <td>{{ $d['name'] }}</td>
                        <td>{{ (int)($d['count'] ?? 0) }}</td>
                        <td>
                            @php
                                $c = (int)($d['count'] ?? 0);
                                echo $deptTotal > 0 ? round(($c / $deptTotal) * 100) . '%' : '0%';
                            @endphp
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Visit Purposes</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Purpose</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $purpTotal = collect($visitor_types ?? [])->values()->sum(); @endphp
                @forelse(collect($visitor_types ?? [])->toArray() as $purpose => $count)
                    <tr>
                        <td>{{ $purpose }}</td>
                        <td>{{ (int)$count }}</td>
                        <td>
                            @php $c=(int)$count; echo $purpTotal>0 ? round(($c/$purpTotal)*100) . '%' : '0%'; @endphp
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(($overstays ?? collect())->count() > 0)
    <div class="section">
        <h2>Overstays</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Facility</th>
                    <th>Expected Time Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($overstays ?? collect()) as $v)
                    <tr>
                        <td>{{ $v->name }}</td>
                        <td>{{ $v->department ?? 'N/A' }}</td>
                        <td>{{ optional($v->facility)->name ?? 'N/A' }}</td>
                        <td>{{ $v->expected_time_out ? \Carbon\Carbon::parse($v->expected_time_out)->format('Y-m-d H:i') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($include_details && $visitors->count() > 0)
    <div class="visitor-details">
        <h2>Visitor Details</h2>
        <table class="visitor-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Purpose</th>
                    <th>Facility</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Duration</th>
                    <th>Host</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitors as $visitor)
                <tr>
                    <td>{{ $visitor->name }}</td>
                    <td>{{ $visitor->company ?? 'N/A' }}</td>
                    <td>{{ $visitor->purpose ?? 'N/A' }}</td>
                    <td>{{ $visitor->facility->name ?? 'N/A' }}</td>
                    <td>{{ $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y h:i A') : 'N/A' }}</td>
                    <td>{{ $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('M d, Y h:i A') : 'Still in' }}</td>
                    <td>
                        @if($visitor->time_out)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->diffForHumans(\Carbon\Carbon::parse($visitor->time_out), true) }}
                        @elseif($visitor->time_in)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->diffForHumans(now(), true) }} (ongoing)
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $visitor->host_employee ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Soliera Visitor Management System</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>
</body>
</html>
