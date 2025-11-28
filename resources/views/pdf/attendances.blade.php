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

<h3>Attendance Report - {{ $employee->name }}</h3>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Online</th>
            <th>Offline</th>
            <th>Total</th>
            <th>Project</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>

    @foreach ($data as $s)
        <?php
            $online = $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '-';
            $offline = $s->end_time ? \Carbon\Carbon::parse($s->end_time)->format('h:i A') : '-';

            if ($s->start_time && $s->end_time) {
                $seconds = \Carbon\Carbon::parse($s->end_time)->diffInSeconds(\Carbon\Carbon::parse($s->start_time));
                $total = gmdate('H:i:s', $seconds);
            } else {
                $total = '-';
            }
        ?>
        <tr>
            <td>{{ \Carbon\Carbon::parse($s->work_date)->format('d-m-Y') }}</td>
            <td>{{ $online }}</td>
            <td>{{ $offline }}</td>
            <td>{{ $total }}</td>
            <td>{{ $s->project_name }}</td>
            <td>{{ $s->comment }}</td>
        </tr>
    @endforeach

    </tbody>
</table>

</body>
</html>