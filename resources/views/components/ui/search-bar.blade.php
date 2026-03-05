<div class="relative max-w-md w-full">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
        </svg>
    </div>
    <input type="text" 
           id="app-search"
           placeholder="Search apps..." 
           class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm transition-all duration-200 shadow-sm"
           onkeyup="filterApps()">
</div>

<script>
    function filterApps() {
        const input = document.getElementById('app-search');
        const filter = input.value.toLowerCase();
        const cards = document.querySelectorAll('[data-name]');

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(filter)) {
                card.style.display = "";
                card.classList.add('animate-fade-in');
            } else {
                card.style.display = "none";
            }
        });
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
