<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="text-teal-600">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('subscription.update', ['subscription' => $subscription->id]) }}">
                    @method('PATCH')
                    @csrf
                    <div class="space-y-12">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                            <div>
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Subscription</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-600">Please edit your subscription.</p>
                            </div>

                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">

                                <div class="sm:col-span-4">
                                    <label for="city_name" class="block text-sm font-medium leading-6 text-gray-900">
                                        City
                                    </label>
                                    <div class="mt-2">
                                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                            <input disabled type="text" value="{{ $subscription->city->name }}" name="city_name" id="city_name" class="bg-gray-200 block flex-1 border-0 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>
                                    @error('city_name')
                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label for="precipitation_threshold_mm" class="block text-sm font-medium leading-6 text-gray-900">
                                        Precipitation Threshold mm
                                    </label>
                                    <div class="mt-2">
                                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                            <input type="number" value="{{ old('precipitation_threshold_mm') ?? $subscription->precipitation_threshold_mm }}" name="precipitation_threshold_mm" id="precipitation_threshold_mm" class="block flex-1 border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                        </div>
                                    </div>
                                    @error('precipitation_threshold_mm')
                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label for="uv_threshold" class="block text-sm font-medium leading-6 text-gray-900">
                                        UV Threshold
                                    </label>
                                    <div class="mt-2">
                                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                            <input type="number" value="{{ old('uv_threshold') ?? $subscription->uv_threshold }}" name="uv_threshold" id="uv_threshold" class="block flex-1 border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                        </div>
                                    </div>
                                    @error('uv_threshold')
                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label for="pause_for" class="block text-sm font-medium leading-6 text-gray-900">
                                        Pause for hours
                                    </label>
                                    <div class="mt-2">
                                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                            <input type="number" value="{{ old('pause_for') ?? $subscription->pause_for }}" name="pause_for" id="pause_for" class="block flex-1 border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                        </div>
                                    </div>
                                    @error('pause_for')
                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('subscription.index') }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
