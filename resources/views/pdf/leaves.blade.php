@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<h3>Leave Report</h3>

<table border="1" width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Apply To</th>
            <th>Leave Type</th>
            <th>From</th>
            <th>To</th>
            <th>Days</th>
            <th>Managed By</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($leaves as $leave)
        <tr>
            <td>{{ $leave->employee->name }}</td>
            <td>{{ $leave->apply_to_names }}</td>
            <td>{{ $leave->leave_type }}</td>
            <td>{{ $leave->from_date }}</td>
            <td>{{ $leave->to_date }}</td>
            <td>
                @php
                    $days = ($leave->leave_type == "Half Day Leave")
                        ? 0.5
                        : (\Carbon\Carbon::parse($leave->from_date)->diffInDays($leave->to_date) + 1);
                @endphp
                {{ $days }}
            </td>
            <td>{{ $leave->manager->name ?? '-' }}</td>
            <td>{{ $leave->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>