<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Add Staff</h1>
        <div class="bg-white rounded shadow p-4">
            <form action="{{ route('merchant.staff.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2" />
                    @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full border rounded p-2" />
                    @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Role</label>
                    <select name="role" class="mt-1 w-full border rounded p-2" required>
                        <option value="staff" {{ old('role')==='staff' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role')==='manager' ? 'selected' : '' }}>Manager</option>
                        <option value="cashier" {{ old('role')==='cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="support" {{ old('role')==='support' ? 'selected' : '' }}>Support</option>
                    </select>
                    @error('role')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <a href="{{ route('merchant.staff.index') }}" class="px-4 py-2 mr-2 rounded border">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>