



<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex justify-end position-relative">
        <div class="relative inline-block">
    <!-- Trigger Button -->
    <a href="javascript:void(0);"
       class="w-10 h-10 flex items-center justify-center text-xl bg-white border rounded-full shadow hover:bg-gray-50 transition"
       id="popupNotification">
        <i class="fa-solid fa-plus text-blue-400"></i>
    </a>

    <!-- Popup Box -->
    <div id="notificationPopup"
         class="absolute top-full right-0 mt-2 w-96 bg-white border rounded-lg shadow-lg p-4 hidden z-50">
        <div class="relative">
            <!-- Close Button -->
            <button id="closePopup" class="absolute top-0 right-0 text-red-500 bg-inherit border-0">
                ✖
            </button>

            <h2 class="text-lg font-semibold mb-3">Notification Title</h2>

            <textarea class="form-input w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none h-40"
                      placeholder="Type your message..."></textarea>

            <div class="mt-3 flex justify-end space-x-2">
                <!-- <button class="px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button> -->
                <button class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Post</button>
            </div>
        </div>
    </div>
</div>


    </div>

    <div class="py-6">
		<div class="mx-auto">
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

				<!-- Employees Count -->
				<a href="{{ route('employees.index') }}">
					<div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold">Employees</h3>
						<p class="text-3xl font-bold">{{ $employeeCount }}</p>
					</div>
				</a>

				<!-- Reviews Count -->
				<a href="{{ route('reviews.index') }}">
					<div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold">Feedback</h3>
						<p class="text-3xl font-bold">{{ $reviewCount }}</p>
					</div>
				</a>

				<!-- Leaves Count -->
				<a href="{{ route('leaves.index') }}">
					<div class="bg-purple-500 text-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold">Leaves</h3>
						<p class="text-3xl font-bold">{{ $leaveCount }}</p>
					</div>
				</a>

				<!-- KPA Count -->
				<a href="{{ route('assessments.index') }}">
					<div class="bg-red-500 text-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold">KPA</h3>
						<p class="text-3xl font-bold">{{ $assessmentCount }}</p>
					</div>
				</a>

			</div>



			<!-- Online Employees -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Today's Online Employees</h3>

                @if($onlineEmployees->count() > 0)
					<table class="w-full border">
						<thead>
							<tr class="bg-gray-100">
								<th class="px-4 py-2 border">Employee</th>
								<th class="px-4 py-2 border">Date</th>
								<th class="px-4 py-2 border">Start Time</th>
								<th class="px-4 py-2 border">End Time</th>
								<th class="px-4 py-2 border">Total Hours</th>
							</tr>
						</thead>
						<tbody id="online-employees-body">
							@include('online-employees', ['onlineEmployees' => $onlineEmployees])
						</tbody>
					</table>
				@else
					<p>No employees are online right now.</p>
				@endif
            </div>
		</div>
	</div>

<script>
function loadOnlineEmployees() {
    fetch('dashboard/online')
        .then(response => response.text())
        .then(html => {
            document.getElementById('online-employees-body').innerHTML = html;
        })
        .catch(error => console.error('Error fetching employees:', error));
}

// Refresh every 5 seconds
setInterval(loadOnlineEmployees, 5000);
</script>



<script>
    const popupBtn = document.getElementById('popupNotification');
    const popup = document.getElementById('notificationPopup');
    const closeBtn = document.getElementById('closePopup');
    popupBtn.addEventListener('click', () => {
        popup.classList.toggle('hidden');
    });
    closeBtn.addEventListener('click', () => {
        popup.classList.add('hidden');
    });
    document.addEventListener('click', (e) => {
        if (!popup.contains(e.target) && !popupBtn.contains(e.target)) {
            popup.classList.add('hidden');
        }
    });
</script>

</x-app-layout>
