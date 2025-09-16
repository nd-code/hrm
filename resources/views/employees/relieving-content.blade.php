<p>
	This is to declare that <b>{{ $employee->name }}</b> was working as 
	<b>{{ $employee->position }}</b> with our esteemed organization 
	from <b>{{ formatDateWithSuffix($employee->joining_date) }}</b> 
	to <b>{{ formatDateWithSuffix(now()) }}</b>. 
	<span class="pronoun">He/She</span> is a hardworking, trustworthy, and qualified responsible person. 
	We have confirmed that <span class="pronoun">he/she</span> has submitted all 
	his/her liabilities to the company and relieved his/her by 
	<b>{{ formatDateWithSuffix(now()) }}</b>.<br><br>
	We wish his/her the best of luck for his/her future.
</p>