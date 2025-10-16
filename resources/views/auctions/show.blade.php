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

                            <p class="text-gray-500 text-sm mt-4">Kết thúc sau: <span id="countdown" class="font-bold"></span></p>

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
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </form>
                            @else
                            <p class="mt-4 text-red-500">Bạn cần <a href="{{ route('login') }}" class="underline">đăng nhập</a> để đặt giá.</p>
                            @endauth

                            <div class="mt-8">
                                <h3 class="text-lg font-semibold">Lịch sử trả giá</h3>
                                <ul class="mt-4 space-y-2">
                                    @forelse($auction->bids->sortByDesc('created_at') as $bid)
                                        <li class="flex justify-between p-2 bg-gray-50 rounded">
                                            <span>{{ $bid->user->name }}</span>
                                            <span class="font-semibold">{{ number_format($bid->amount) }} VND</span>
                                        </li>
                                    @empty
                                        <li class="text-gray-500">Chưa có ai trả giá.</li>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const auctionId = {{ $auction->id }};

        // --- Phần code cho đồng hồ đếm ngược ---
        const countdownElement = document.getElementById('countdown');
        const endTimeString = '{{ $auction->end_time->toIso8601String() }}';
        const endTime = new Date(endTimeString).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                countdownElement.innerHTML = "ĐÃ KẾT THÚC";
                countdownElement.classList.add('text-red-500');
                clearInterval(countdownInterval);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${days} ngày ${hours} giờ ${minutes} phút ${seconds} giây`;
        }

        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();

        // --- Phần code lắng nghe sự kiện trả giá mới ---
        window.Echo.channel(`auction.${auctionId}`)
            .listen('NewBidPlaced', (e) => {
                console.log("Dữ liệu nhận được:", e); // Dùng để kiểm tra trong Console (F12)

                // 1. Cập nhật giá cao nhất hiện tại
                const currentPriceElement = document.querySelector('.text-4xl.font-bold.text-red-600');
                if (currentPriceElement) {
                    currentPriceElement.innerText = new Intl.NumberFormat('vi-VN').format(e.bid.amount) + ' VND';
                }

                // 2. Thêm một dòng mới vào lịch sử trả giá
                const bidHistoryList = document.querySelector('.mt-8 ul');
                if (bidHistoryList) {
                    // Xóa thông báo "Chưa có ai trả giá" nếu có
                    const emptyMessage = bidHistoryList.querySelector('li.text-gray-500');
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }

                    const newRow = document.createElement('li');
                    newRow.classList.add('flex', 'justify-between', 'p-2', 'bg-gray-50', 'rounded');

                    const bidderSpan = document.createElement('span');
                    bidderSpan.innerText = e.bid.user.name;

                    const amountSpan = document.createElement('span');
                    amountSpan.classList.add('font-semibold');
                    amountSpan.innerText = new Intl.NumberFormat('vi-VN').format(e.bid.amount) + ' VND';

                    newRow.appendChild(bidderSpan);
                    newRow.appendChild(amountSpan);

                    // Chèn vào đầu danh sách
                    bidHistoryList.insertBefore(newRow, bidHistoryList.firstChild);
                }
            });
    });
</script>
@endpush