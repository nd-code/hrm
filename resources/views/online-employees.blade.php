@foreach($onlineEmployees as $session)
    @php
        $start = $session->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
        $end = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : null;
        $total = ($start && $end) ? $start->diff($end)->format('%H:%I:%S') : '-';
    @endphp
    <tr>
        <td class="px-4 py-2 border"><a href="{{ route('employees.show', $session->employee->id) }}">{{ $session->employee->name }}</a></td>
        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($session->work_date)->format('d-m-Y') }}</td>
        <td class="px-4 py-2 border">{{ $start ? $start->format('h:i A') : '-' }}</td>
        <td class="px-4 py-2 border">{{ $end ? $end->format('h:i A') : '-' }}</td>
        <td class="px-4 py-2 border">{{ $total }}</td>
    </tr>
@endforeach