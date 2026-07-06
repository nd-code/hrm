<x-app-layout>

    <div class="py-12">

        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h3 class="text-lg font-bold">
                        Add Holiday
                    </h3>

                    <a href="{{ route('holidays.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back
                    </a>

                </div>

                <form method="POST"
                      action="{{ route('holidays.store') }}">

                    @csrf

                    <div class="row mb-4">

                        <div class="col-lg-4">

                            <label class="mb-2">
                                Holiday Name
                            </label>

                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   value="{{ old('title') }}"
                                   placeholder="Enter holiday name"
                                   required>

                            @error('title')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        <div class="col-lg-4">

                            <label class="mb-2">
                                Holiday Date
                            </label>

                            <input type="date"
                                   name="holiday_date"
                                   class="form-control"
                                   value="{{ old('holiday_date') }}"
                                   required>

                            @error('holiday_date')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                        <div class="col-lg-4">
                            <label class="mb-2">Day</label>

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

                    </div>

                    <div class="row mb-4">

                        <div class="col-lg-12">

                            <label class="mb-2">
                                Description
                            </label>

                            <textarea name="description"
                                      rows="4"
                                      class="form-control"
                                      placeholder="Enter holiday description">{{ old('description') }}</textarea>

                        </div>

                    </div>

                    <div class="mt-4">

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">

                            Save Holiday

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>