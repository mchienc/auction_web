<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Danh sách các phiên đấu giá') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if($auction->status == 'finished')
    <span class="mt-2 inline-block bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Đã kết thúc</span>
            @endif
            @if($auction->status == 'running')
    @auth
        <form action="{{ route('auctions.bids.store', $auction) }}" method="POST" class="mt-4">
            {{-- ... nội dung form ... --}}
        </form>
    @else
        <p class="mt-4 text-red-500">Bạn cần <a href="{{ route('login') }}" class="underline">đăng nhập</a> để đặt giá.</p>
    @endauth
@else
    {{-- Hiển thị khi phiên đấu giá đã kết thúc --}}
    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
        <p class="font-semibold text-lg text-center">Phiên đấu giá đã kết thúc!</p>
        @if($auction->winner)
            <p class="text-center mt-2">Người chiến thắng: <span class="font-bold">{{ $auction->winner->name }}</span> với giá <span class="font-bold text-red-600">{{ number_format($auction->current_price) }} VND</span></p>
        @else
            <p class="text-center mt-2">Phiên đấu giá kết thúc mà không có người trả giá.</p>
        @endif
    </div>
@endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($auctions as $auction)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <a href="#">
                            <img src="{{ asset('storage/' . $auction->product->image) }}" alt="{{ $auction->product->name }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold">{{ $auction->product->name }}</h3>
                            <p class="text-gray-600 mt-2">Giá hiện tại: <span class="font-bold text-red-500">{{ number_format($auction->current_price) }} VND</span></p>
                            <p class="text-gray-500 text-sm mt-1">Kết thúc vào: {{ \Carbon\Carbon::parse($auction->end_time)->format('H:i, d/m/Y') }}</p>
                            <div class="mt-4">
                                <a href="#" class="w-full text-center inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">Chưa có phiên đấu giá nào.</p>
                @endforelse
            </div>
            <div class="mt-8">
    {{ $auctions->links() }}
    </div>
        </div>
    </div>
</x-app-layout>
