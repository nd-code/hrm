@if($onlineEmployees->count() > 0)
    @foreach($onlineEmployees as $session)
        @php
            $start = $session->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
            $end = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : null;
            $total = ($start && $end) ? $start->diff($end)->format('%H:%I:%S') : '-';
        @endphp
        <tr>
            <td class="px-4 py-2 border"><img src="{{ $session->employee->profile_photo 
                                                ? asset('storage/'.$session->employee->profile_photo) 
                                                : asset('images/default-avatar.png') }}"
                                         class="w-10 h-10 rounded-full object-cover me-2 border">
            </td>
            <td class="px-4 py-2 border"><a style="text-decoration: underline;" href="{{ route('employees.show', $session->employee->id) }}">{{ $session->employee->name }}</a></td>
            <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($session->work_date)->format('d-m-Y') }}</td>
            <td class="px-4 py-2 border">{{ $start ? $start->format('h:i A') : '-' }}</td>
        </tr>
    @endforeach
@else
    <tr><td class="px-4 py-2 border" colspan="4">No employees are online.</td></tr>
@endif