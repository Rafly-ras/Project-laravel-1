<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
            Daftar Post
        </h1>

        {{-- Flash Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white rounded">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('posts.create') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white transition">
            + Buat Post
        </a>

        <div class="mt-6 space-y-4">
            @forelse ($posts as $post)
                <div class="bg-gray-800 dark:bg-gray-700 p-4 rounded shadow">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $post->title }}
                    </h2>

                    <p class="text-gray-300 mt-2">
                        {{ $post->content }}
                    </p>

                    <p class="text-sm text-gray-400 mt-2">
                        Status: {{ $post->status }}
                    </p>
                </div>
            @empty
                <p class="text-gray-400">Belum ada post.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
