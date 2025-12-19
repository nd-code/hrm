<div class="grid grid-cols-2 gap-4">

    <input type="text" name="name" class="border p-2" placeholder="Name"
           value="{{ $candidate->name ?? old('name') }}" required>

    <input type="email" name="email" class="border p-2" placeholder="Email"
           value="{{ $candidate->email ?? old('email') }}">

    <input type="text" name="phone" class="border p-2" placeholder="Phone"
           value="{{ $candidate->phone ?? old('phone') }}">

    <input type="text" name="city" class="border p-2" placeholder="City"
           value="{{ $candidate->city ?? old('city') }}">

    <input type="text" name="salary" class="border p-2" placeholder="Salary (Per Annum)"
           value="{{ $candidate->salary ?? old('salary') }}">

    <input type="text" name="work_experience" class="border p-2" placeholder="Experience (Yrs.)"
           value="{{ $candidate->work_experience ?? old('work_experience') }}">

    <input type="text" name="designation" class="border p-2" placeholder="Designation"
           value="{{ $candidate->designation ?? old('designation') }}">

    <div>
        <label class="text-sm text-gray-600">Interview Taken Date:</label>
        <input type="date" name="interview_date" class="border p-2"
           value="{{ $candidate->interview_date ?? old('interview_date') }}">
    </div>

    <textarea name="comment" class="border p-2 col-span-2" placeholder="Comment">{{ $candidate->comment ?? old('comment') }}</textarea>

    <div class="col-span-2">
        <label>Upload CV (PDF/DOC)</label>
        <input type="file" name="cv" class="border p-2">

        @if(isset($candidate) && $candidate->cv)
            <a href="{{ asset('storage/'.$candidate->cv) }}" target="_blank"
               class="text-blue-600 underline"><i class="fas fa-file-alt"></i></a>
        @endif
    </div>

</div>