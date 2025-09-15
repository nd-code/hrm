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
  <title>Relieving Letter</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .expiLetter {
        padding: 150px 0;
    }
    @media (min-width: 1200px) {
        .expiLetter .container {
            padding: 0 20%;
        }
    }
    .expiLetter h2 {
        text-decoration: underline;
        text-align: center;
        font-size: 24px;
        margin: 30px;
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
  </style>
</head>
<body>

  <div class="expiLetter">
    
    <div class="container">
        <div class="date text-end">Date: {{ now()->format('d/m/Y') }}</div>
        <div class="nameAddress mt-2">
            To, <br>
            <b>{{ $employee->name }}</b><br>
            Designation: {{ $employee->position }}<br>
            Address: {{ $employee->address }}
        </div>
        <h2>To Whomsoever It May Concern</h2>
        <div class="detail" contenteditable="true">
			<p>
				This is to declare that <b>{{ $employee->name }}</b> was working as 
				<b>{{ $employee->position }}</b> with our esteemed organization 
				from <b>{{ formatDateWithSuffix($employee->joining_date) }}</b> 
				to <b>{{ formatDateWithSuffix(now()) }}</b>. 
				<span class="pronoun">He/She</span> is a hardworking, trustworthy, and qualified responsible person. 
				We have confirmed that <span class="pronoun">he/she</span> has submitted all 
				her liabilities to the company and relieved her by 
				<b>{{ formatDateWithSuffix(now()) }}</b>.
			</p>
		</div>
        <div class="wishesh">
            <p>We wish herthe best of luck for her future.</p>
        </div>
        <div class="thanksLine">Thank you.</div>
        <div class="thanksName">
            Ashesh Suthar. <br>
            <b>(CEO - Aarav Info Solutions Private Limited).</b>
        </div>
		
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
		  border: 1px dashed #999; /* visible only on screen */
		  padding: 5px;
	  }*/
	</style>
</body>
</html>