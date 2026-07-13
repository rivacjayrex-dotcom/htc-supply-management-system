<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Inventory Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <!-- Item Name -->
                    <div class="mb-4">
                        <x-input-label for="item_name" :value="__('Item Name')" />
                        <x-text-input id="item_name" name="item_name" type="text" class="block mt-1 w-full" required />
                    </div>

                    <!-- Specifications -->
                    <div class="mb-4">
                        <x-input-label for="specifications" :value="__('Specifications (Optional)')" />
                        <textarea id="specifications" name="specifications" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Initial Quantity')" />
                            <x-text-input id="quantity" name="quantity" type="number" class="block mt-1 w-full" required />
                        </div>

                        <!-- Unit -->
                        <div>
                            <x-input-label for="unit" :value="__('Unit (e.g. Ream, Pc)')" />
                            <x-text-input id="unit" name="unit" type="text" class="block mt-1 w-full" required />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="unit_price" :value="__('Unit Price (₱)')" />
                            <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" class="block mt-1 w-full" required />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('inventory.index') }}" class="mr-4 text-gray-600">Cancel</a>
                        <x-primary-button>
                            {{ __('Save Item') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
