<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Member') }}: {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3.5" required>
                            @error('name') <p class="mt-2 text-sm text-rose-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3.5" required>
                            @error('email') <p class="mt-2 text-sm text-rose-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-4">Update Password (Leave blank to keep current)</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                                    <input type="password" name="password" id="password" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3.5">
                                    @error('password') <p class="mt-2 text-sm text-rose-600 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3.5">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Assign Role</label>
                            <select name="role_id" id="role_id" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3.5" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <p class="mt-2 text-sm text-rose-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('employees.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition duration-150">
                                Update Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
