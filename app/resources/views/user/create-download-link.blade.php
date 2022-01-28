<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダウンロードリンクの作成
        </h2>
    </x-slot>
    <div class="py-12">
        @if($status)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('user.create.download.link') }}">
                        @csrf
                        <div class="py-3">
                            <!-- expire_date -->
                            <div class="mt-4">
                                <x-label for="expire_date" :value="__('Expired date')" />

                                <x-select id="expire_date" class="block mt-1 w-full" name="expire_date" :value="old('expire_date')" :options="$options" required />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-button class="ml-4">
                                    {{ __('Register') }}
                                </x-button>
                            </div>
                        </div>
                        <x-label for="expire_date" value="選択したファイル" />
                        @php($i = 1)
                        @foreach($files as $file)
                        <x-input type="hidden" name="file[]" value="{{ $file->id }}" required />
                        {{-- <x-input type="hidden" name="file[]" value="6" required /> --}}

                        <div class="my-2">
                            <div class="rounded-md shadow-sm border border-gray-300 overflow-hidden">
                                <p class="block font-medium text-sm text-gray-50 bg-gray-800 px-3 py-2">File{{$i}}</p>
                                <div class="px-3 pt-2 pb-3">
                                    <p class="block mx-1 font-medium text-sm text-gray-700">File Name</p>
                                    <p class="block break-words mx-2 mb-2 w-full">{{ $file->name }}</p>
                                    <p class="block mx-1 font-medium text-sm text-gray-700">File Type</p>
                                    <p class="block break-words mx-2 w-full">{{ $file->type }}</p>
                                </div>
                            </div>
                        </div>
                        @php($i++)
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
        @else
        <span id="error_wrap" class="rounded-md px-6 py-5 bg-red-200 absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
            <p class="block w-full text-center text-xl mb-2">エラーが発生しました</p>
            <p>値が不正です</p>
        </span>
        @endif
    </div>
    <script>
    let btn = document.querySelector('#copy-btn');
    let text = document.querySelector('#copy-text');
    let message = document.querySelector('#copy-message');

    if(btn){
    btn.addEventListener('click', () => {
        let innerText = text.innerText;

        if(navigator.clipboard){
            navigator.clipboard.writeText(innerText);
            message.classList.remove('invisible');
            setTimeout(() =>{
                message.classList.add('invisible');
            },3000);
        }
    });
    }
    </script>
</x-app-layout>
