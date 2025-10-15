<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chi tiết phiên đấu giá') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <img src="{{ asset('storage/' . $auction->product->image) }}" alt="{{ $auction->product->name }}" class="w-full rounded-lg">
                        </div>

                        <div>
                            <h1 class="text-3xl font-bold">{{ $auction->product->name }}</h1>
                            <p class="text-gray-600 mt-2">{{ $auction->product->description }}</p>
                            <p class="text-gray-500 text-sm mt-4">Đăng bởi: {{ $auction->product->user->name }}</p>

                            <div class="mt-6">
                                <p class="text-lg">Giá hiện tại:</p>
                                <p class="text-4xl font-bold text-red-600">{{ number_format($auction->current_price) }} VND</p>
                            </div>

                            <p class="text-gray-500 text-sm mt-4">Kết thúc sau: <span id="countdown"></span></p>

                            @auth
                            <form action="{{ route('auctions.bids.store', $auction) }}" method="POST" class="mt-4">
                                @csrf
                                <x-input-label for="amount" :value="__('Giá của bạn (phải lớn hơn giá hiện tại)')" />
                                <div class="flex items-center">
                                    <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" required />
                                    <x-primary-button class="ml-4">
                                        {{ __('Đặt giá') }}
                                    </x-primary-button>
                                </div>
                            </form>
                            @else
                            <p class="mt-4 text-red-500">Bạn cần <a href="{{ route('login') }}" class="underline">đăng nhập</a> để đặt giá.</p>
                            @endauth

                            <div class="mt-8">
                                <h3 class="text-lg font-semibold">Lịch sử trả giá</h3>
                                <ul class="mt-4 space-y-2">
                                    @forelse($auction->bids as $bid)
                                        <li class="flex justify-between p-2 bg-gray-50 rounded">
                                            <span>{{ $bid->user->name }}</span>
                                            <span class="font-semibold">{{ number_format($bid->amount) }} VND</span>
                                        </li>
                                    @empty
                                        <li>Chưa có ai trả giá.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>