<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recycle Bin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recycle Bin</h3>
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
                            @forelse($items as $item)
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
                                            <button class="btn btn-success">Restore</button>
                                        </form>

                                        <form method="POST" action="{{ route('recycle.delete', $item->id) }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">
                                        No deleted records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>