<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Buat Post</h1>

        <form action="{{ route('posts.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label>Judul</label>
                <input type="text" name="title"
                       class="border w-full p-2 rounded">
            </div>

            <div class="mb-4">
                <label>Konten</label>
                <textarea name="content"
                          class="border w-full p-2 rounded"></textarea>
            </div>

            <button class="bg-green-500 text-white px-4 py-2 rounded">
                Simpan
            </button>
        </form>
    </div>
</x-app-layout>
