<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Request Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                <form action="{{ route('request-orders.store') }}" method="POST" id="ro-form">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Customer Name</label>
                            <input type="text" name="customer_name" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-bold" placeholder="e.g. John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Customer Email</label>
                            <input type="email" name="customer_email" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-bold" placeholder="john@example.com">
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-widest">Order Items</h3>
                            <button type="button" onclick="addItem()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                + Add Item
                            </button>
                        </div>

                        <div id="items-container" class="space-y-4">
                            <div class="item-row grid grid-cols-12 gap-4 items-end bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="col-span-12 md:col-span-7">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Product</label>
                                    <select name="items[0][product_id]" required class="w-full bg-white dark:bg-gray-900 border-none rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white text-sm font-bold">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (${{ number_format($product->price, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-10 md:col-span-4">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Quantity</label>
                                    <input type="number" name="items[0][qty]" required min="1" class="w-full bg-white dark:bg-gray-900 border-none rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white text-sm font-bold">
                                </div>
                                <div class="col-span-2 md:col-span-1 text-center">
                                    <button type="button" onclick="removeItem(this)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-12">
                        <a href="{{ route('request-orders.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-black rounded-xl hover:bg-gray-200 transition uppercase tracking-widest text-xs">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-md transition uppercase tracking-widest text-xs">Save Request Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 1;
        function addItem() {
            const container = document.getElementById('items-container');
            const row = container.querySelector('.item-row').cloneNode(true);
            
            // Update names
            row.querySelectorAll('select, input').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${itemCount}]`);
                el.value = '';
            });
            
            container.appendChild(row);
            itemCount++;
        }

        function removeItem(btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
            }
        }
    </script>
</x-app-layout>
