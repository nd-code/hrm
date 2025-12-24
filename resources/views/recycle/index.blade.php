<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="display w-full mt-4">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Record ID</th>
                                <th>Deleted Data</th>
                                <th>Deleted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ ucfirst($item->module) }}</td>
                                <td>{{ $item->record_id }}</td>
                                <td>
                                    <pre class="text-xs">{{ json_encode($item->data, JSON_PRETTY_PRINT) }}</pre>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('recycle.restore', $item->id) }}">
                                        @csrf
                                        <i class="fas fa-undo text-green-500 mr-2"></i>
                                    </form>

                                    <form method="POST" action="{{ route('recycle.delete', $item->id) }}">
                                        @csrf @method('DELETE')
                                        <i class="fas fa-trash-alt text-red-500"></i>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>