<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12 relative">
            <span id="copy-message" class="invisible px-3 py-2 bg-blue-200 absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
                コピーしました！
            </span>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <div class="border-gray-200">
                    <div class="pb-3 border-b border-gray-500 flex min-w-70rem">
                        <div class="w-2/12 text-center">ファイルの状態</div>
                        <div class="w-4/12 mx-2 ">リンクタイトル</div>
                        <div class="w-2/12 text-center">リンク作成日時</div>
                        <div class="w-2/12 text-center">リンク有効期限</div>
                        <div class="w-1/12 text-center">リンク</div>
                        <div class="w-1/12 text-center">削除</div>
                    </div>
                    <ul>
                        @foreach ($upload_links as $upload_link)
                        <li class="pt-4 pb-3 list-none border-b border-gray-200 flex min-w-70rem hover:bg-gray-200">
                            <div class="w-2/12 ml-3">
                                <x-upload-status
                                type="button"
                                class="upload-detail-btn" :uploadLink="$upload_link->uploads->upload_status ?? null"></x-upload-status>
                            </div>
                            <div class="w-4/12 mx-2 break-words"> {{ $upload_link->message }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->created_at }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->expire_date }}</div>
                            <div class="w-1/12 text-center">
                                <x-link-button type="button" class="copy-btn" data-src="{{ route('user.upload', ['key' => $upload_link->query]) }}"></x-link-button>
                            </div>
                            <div class="w-1/12 text-center">
                                <x-link-delete-button type="button" class="delete-btn" data-src="{{ route('user.upload', ['key' => $upload_link->query]) }}"></x-link-delete-button>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-6">
                    {{$upload_links->links()}}
                </div>
            </div>
        </div>
    </div>
    <script>
        let copyBtns = document.querySelectorAll('.copy-btn');
        let message = document.querySelector('#copy-message');
        if(copyBtns){
            copyBtns.forEach(btn => {
                btn.addEventListener('click', e => {
                    let src = e.currentTarget.dataset.src;
                    if(navigator.clipboard){
                        navigator.clipboard.writeText(src);
                        message.classList.remove('invisible');
                        setTimeout(() =>{
                            message.classList.add('invisible');
                        },3000);
                    }
                });
            });
        }
    </script>
</x-app-layout>
