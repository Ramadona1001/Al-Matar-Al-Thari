<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Staff</h1>
            <a href="{{ route('merchant.staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Add Staff</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $member->name }}</td>
                            <td class="px-4 py-2">{{ $member->email ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $member->phone ?? '-' }}</td>
                            <td class="px-4 py-2">{{ ucfirst($member->role) }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-sm {{ $member->status ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $member->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('merchant.staff.edit', $member) }}" class="text-blue-600 mr-2">Edit</a>
                                <form action="{{ route('merchant.staff.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $staff->links() }}</div>
    </div>
</body>
</html>