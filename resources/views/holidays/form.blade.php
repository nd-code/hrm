<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>

        <label class="block mb-2 text-sm font-medium text-gray-700">
            Holiday Name
        </label>

        <input type="text"
               name="title"
               value="{{ old('title', $holiday->title ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">

        @error('title')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror

    </div>

    <div>

        <label class="block mb-2 text-sm font-medium text-gray-700">
            Holiday Date
        </label>

        <input type="date"
               name="holiday_date"
               value="{{ old('holiday_date', $holiday->holiday_date ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">

        @error('holiday_date')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror

    </div>

</div>

<div class="mt-6">

    <label class="block mb-2 text-sm font-medium text-gray-700">
        Description
    </label>

    <textarea name="description"
              rows="4"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">{{ old('description', $holiday->description ?? '') }}</textarea>

</div>

<div class="mt-6">
    
    <label class="block mb-2 text-sm font-medium text-gray-700">
        Day
    </label>

    <select name="day" class="form-control" required>
        <option value="">Select Day</option>

        @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
            <option value="{{ $day }}"
                {{ old('day', $holiday->day ?? '') == $day ? 'selected' : '' }}>
                {{ $day }}
            </option>
        @endforeach
    </select>

</div>

<div class="mt-8">

    <button type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
        Save Holiday
    </button>

</div>