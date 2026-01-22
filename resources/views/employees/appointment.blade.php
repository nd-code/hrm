@php
    function formatDateWithSuffix($date) {
        if(!$date) return '';
        $carbon = \Carbon\Carbon::parse($date);
        $day = $carbon->format('j');
        $monthYear = $carbon->format('F Y');

        // Add ordinal suffix
        if ($day % 10 == 1 && $day != 11) {
            $suffix = 'st';
        } elseif ($day % 10 == 2 && $day != 12) {
            $suffix = 'nd';
        } elseif ($day % 10 == 3 && $day != 13) {
            $suffix = 'rd';
        } else {
            $suffix = 'th';
        }

        return $day . $suffix . ' ' . $monthYear;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Letter</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .expiLetter {
        padding: 80px 0;
    }
    @media (min-width: 1200px) {
        .expiLetter .container {
            padding: 0 20%;
        }
    }
    .expiLetter h2 {
        text-decoration: underline;
        text-align: center;
        font-size: 18px;
        margin: 10px;
    }
    .expiLetter{
        font-size: 14px;
    }
    .thanksLine {
        margin-bottom: 10px;
    }
    .wishesh {
        padding: 10px 0;
    }
    .detail {
        text-align: justify;
    }
    
    @media print {
        /* force page break */
        .page-break {
            page-break-before: always;
            break-before: page;   /* modern browsers */
        }
    }
  </style>
</head>
<body>

  <div class="expiLetter">
    
    <div class="container">
        <h2>Appointment Letter</h2>
        <div class="date text-end"><b>Date:</b> {{ now()->format('jS F Y') }}</div>
        <div class="nameAddress mt-2 mb-4">
            To, <br>
            <b>{{ $employee->name }}<br>
            Designation: {{ $position->name }}<br>
            Address: {{ $employee->address }}</b><br><br>
            
            Dear <b>{{ $employee->name }}</b>,<br>
        </div>
        <div id="letterContent" class="detail" contenteditable="true">
            @if($letter && $letter->content)
                    {!! $letter->content !!}
            @else
                    @include('employees.appointment-content', ['employee' => $employee])
            @endif
        </div>
        
        <br><br>
        <p>
            Thanking you,<br>
            For: <b>Aarav Info Solutions Pvt. Ltd.</b>
        </p>
        <br>
        <p>
            __________________________<br>
            <b>Authorized Signatory</b><br>
            <b>Date: {{ now()->format('jS F Y') }}</b>
        </p>
        <br><hr><br>
        <p>
            I have accepted the above-mentioned terms and conditions.
        </p>
        <p>
            Name: <b>Mr. {{ $employee->name }}</b><br>
            Signature: _______________________<br>
            Date: _______________________
        </p>
		
        <div class="alert alert-info my-3">
                ✍️ You can edit the text before printing.
        </div>
    </div>


    <!-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success btn-lg px-4">Submit</button>
    </div> -->
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<style>
	  @media print {
		  .alert, button { display: none !important; }
		  .detail { border: none; }
	  }
	  /*.detail[contenteditable="true"] {
		  border: 1px dashed #999;
		  padding: 5px;
	  }*/
	</style>
	
	<script>
	let timeout = null;

	document.getElementById('letterContent').addEventListener('input', function() {
		clearTimeout(timeout);
		timeout = setTimeout(saveLetter, 1000); // save after 1s of no typing
	});

	function saveLetter() {
		let content = document.getElementById('letterContent').innerHTML;

		fetch("{{ route('employees.appointment-letter.save', $employee->id) }}", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-CSRF-TOKEN": "{{ csrf_token() }}"
			},
			body: JSON.stringify({
				appointment_letter: content
			})
		}).catch(err => console.error("Auto-save failed", err));
	}
	</script>
</body>
</html>