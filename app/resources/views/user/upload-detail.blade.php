<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アップロードの詳細
        </h2>
    </x-slot>
    <div class="py-12">
        <x-loading-window></x-loading-window>
        <x-error-message></x-error-message>
        <x-confirm-window></x-confirm-window>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(!empty($upload))
                    <h3 class="font-semibold text-l text-gray-800 leading-tight">アップロード済みのファイル</h3>
                    <div class="py-3">
                        <!-- Sender -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Sender</p>
                            <p class="block break-words mx-1 w-full">{{ $upload->sender }}</p>
                        </div>
                        <!-- Message -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Message</p>
                            <p class="block break-words mx-1 w-full">{{ $upload->message }}</p>
                        </div>

                        <!-- Expire Date -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Expire Date</p>
                            <p class="block break-words mx-1 w-full">{{ $expire_date }}</p>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            @if($expire_status)
                            <p>ファイルを選択してください</p>
                            <x-button id="file-delete-btn" class="ml-4 bg-red-700 hover:bg-red-800">
                                <svg class="h-5 w-5 mr-2 text-gray-100 hover:text-gray-300 disabled:opacity-25 transition ease-in-out duration-150"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                ファイルの削除
                            </x-button>
                            <x-button id="link-create-btn" class="ml-4">
                                <svg class="h-5 w-5 mr-2 text-gray-100"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" />  <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" />  <line x1="16" y1="21" x2="16" y2="19" />  <line x1="19" y1="16" x2="21" y2="16" />  <line x1="3" y1="8" x2="5" y2="8" />  <line x1="8" y1="3" x2="8" y2="5" /></svg>
                                ダウンロードリンクの作成
                            </x-button>
                            <x-button id="download-btn" class="ml-4">
                                <svg class="h-5 w-5 mr-2 text-gray-100"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M19 18a3.5 3.5 0 0 0 0 -7h-1a5 4.5 0 0 0 -11 -2a4.6 4.4 0 0 0 -2.1 8.4" />  <line x1="12" y1="13" x2="12" y2="22" />  <polyline points="9 19 12 22 15 19" /></svg>
                                    ダウンロード
                            </x-button>
                            @else
                            <p>ダウンロード可能期間が終了しました</p>
                            @endif
                        </div>
                    </div>
                    <!-- Files -->
                    @php($i = 1)
                    @foreach($upload->files as $file)
                    <div class="my-2">
                        <div class="rounded-md shadow-sm border border-gray-300 overflow-hidden">
                            <p class="block font-medium text-sm text-gray-50 bg-gray-800 px-3 py-2">File{{$i}}</p>
                            <div class="px-3 pt-2 pb-3">
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Name</p>
                                <p class="block break-words mx-2 mb-2 w-full">{{ $file->name }}</p>
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Type</p>
                                <p class="block break-words mx-2 w-full">{{ $file->type }}</p>
                                @if($expire_status)
                                <div class="flex items-center justify-end mt-4">
                                    <x-button class="file-select-btn ml-4" data-file="{{$file->id}}">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="align-middle">選択</span>
                                    </x-button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @php($i++)
                    @endforeach
                    @endif
                </div>
                <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-l text-gray-800 leading-tight">有効なダウンロードリンク</h3>
                    <div class="py-3">
                        <div class="border-gray-200">
                            <div class="pb-3 border-b border-gray-500 flex min-w-70rem">
                                <div class="w-6/12 mx-2 ">ファイル</div>
                                <div class="w-2/12 text-center">リンク作成日時</div>
                                <div class="w-2/12 text-center">リンク有効期限</div>
                                <div class="w-1/12 text-center">リンク</div>
                                <div class="w-1/12 text-center">削除</div>
                            </div>
                            <ul>
                                @foreach ($download_links as $download_link)
                                <li class="pt-4 pb-3 list-none border-b border-gray-200 flex min-w-70rem hover:bg-gray-200">
                                    <div class="w-6/12 mx-2 break-words">
                                        <ul>
                                            @foreach ($download_link->download as $download)
                                            <li class="ml-5 list-disc">{{ $download->file->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="w-2/12 text-center">{{ $download_link->created_at }}</div>
                                    <div class="w-2/12 text-center">{{ $download_link->expire_date }}</div>
                                    <div class="w-1/12 text-center">
                                        <x-link-button type="button" class="copy-btn" data-src="{{ route('user.download', ['key' => $download_link->query]) }}"></x-link-button>
                                    </div>
                                    <div class="w-1/12 text-center">
                                        <x-link-delete-button type="button" class="link-delete-btn" data-link-id="{{ $download_link->id }}"></x-link-delete-button>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-6">
                            {{ $download_links->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        let downloadBtns = document.querySelector('#download-btn');
        let linkCreateBtn = document.querySelector('#link-create-btn');
        let fileDeleteBtn = document.querySelector('#file-delete-btn');
        let fileSelectBtns = document.querySelectorAll('.file-select-btn');
        let confirmWrapCloseBtn = document.querySelector('#confirm_close_btn');
        let copyBtn = document.querySelector('#copy_btn');
        let linkDeleteBtns = document.querySelectorAll('.link-delete-btn');
        let confirmBtn = document.querySelector('#confirm-btn');
        let cancelBtn = document.querySelector('#cancel-btn');
        let loadWrap = document.querySelector('#load_wrap');
        let errorWrap = document.querySelector('#error_wrap');
        let errorWrapSession = document.querySelector('#error_wrap_session');
        let confirmWrap = document.querySelector('#confirm_wrap');
        let errorMessage = document.querySelector('#error_message');
        let copyText = document.querySelector('#copy_text');
        let copyMessage = document.querySelector('#copy_message');
        let requestFiles = new Set();

        function sendForm(url, fileIds, method='post'){
            const form = document.createElement('form');
            form.setAttribute('action', url);
            form.setAttribute('method', method);
            form.style.display = 'none';
            document.body.appendChild(form);

            const csrfInput = document.createElement('input')
            csrfInput.setAttribute('type', 'hidden');
            csrfInput.setAttribute('name', '_token');
            csrfInput.setAttribute('value', '{{ csrf_token() }}');
            form.appendChild(csrfInput);

            fileIds.forEach(id => {
                const input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'id[]');
                input.setAttribute('value', id);
                form.appendChild(input);
            })
            form.submit();
        }

        function classListToggle(target, classNames){
            classNames.forEach(className => {
                target.classList.toggle(className);
            });
        }

        async function showErrorMessage(error, errorMsgElm){
            let text = typeof(error) === 'string' ? error : '';
            if(error.response != undefined){
                text = error.request.responseType === "blob"
                    ? JSON.parse(await error.response.data.text()).message
                    : await error.response.data.message;
            }
            errorMsgElm.innerText = text;
        }

        fileSelectBtns.forEach(btn => btn.addEventListener('click', e => {
            let target = e.currentTarget;
            let fileId = target.dataset.file;

            classListToggle(target, [
                'bg-gray-800','text-white', 'bg-gray-100', 'text-green-500'
            ]);

            requestFiles.has(fileId)
                ? requestFiles.delete(fileId)
                : requestFiles.add(fileId);
        }));

        downloadBtns.addEventListener('click', () =>{
            classListToggle(loadWrap, ['invisible']);
            window.axios({
                url: '{{ route('user.file.download') }}',
                method: 'post',
                dataType: 'json',
                data: JSON.stringify({
                    file: [...requestFiles],
                }),
                responseType: 'blob'
            })
            .then(response => {
                const headers = response.headers['content-disposition'].split('filename=');
                const filename = headers[headers.length-1];
                const blob = new Blob([response.data]);
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
            })
            .catch(async error => {
                classListToggle(errorWrap, ['invisible']);
                await showErrorMessage(error, errorMessage);
                setTimeout(() =>{
                    classListToggle(errorWrap, ['invisible']);
                },5000);
            })
            .finally(() => classListToggle(loadWrap, ['invisible']));
        });

        linkCreateBtn.addEventListener('click', ()=> {
            classListToggle(loadWrap, ['invisible']);

            window.axios({
                url: '{{ route('user.create.download.check') }}',
                method: 'post',
                dataType: 'json',
                data: JSON.stringify({
                    file: [...requestFiles],
                }),
            })
            .then(response => {
                const url = '{{ route('user.create.download') }}';
                sendForm(url, requestFiles);
            })
            .catch(async error => {
                classListToggle(errorWrap, ['invisible']);
                await showErrorMessage(error, errorMessage);
                setTimeout(() =>{
                    classListToggle(errorWrap, ['invisible']);
                },5000);
            })
            .finally(() => classListToggle(loadWrap, ['invisible']));
        });


        fileDeleteBtn.addEventListener('click', ()=> {
            // deleteボタンが押されたら、確認windowを表示
            if(requestFiles.size){
                classListToggle(confirmWrap, ['invisible']);
            } else {
                classListToggle(errorWrap, ['invisible']);
                showErrorMessage('ファイルが選択されていません', errorMessage);
                setTimeout(() =>{
                    classListToggle(errorWrap, ['invisible']);
                },5000);
            }

            confirmBtn.addEventListener('click', () => {
            // deleteするファイルのidを送信
            // formで送信
            classListToggle(confirmWrap, ['invisible']);
            const url = '{{ route('user.delete.file') }}';
            sendForm(url, requestFiles);
            });
        });

        linkDeleteBtns.forEach(btn => {
            btn.addEventListener('click', e => {
                let requestFiles = new Set();
                requestFiles.add(e.currentTarget.dataset.linkId);
                if(requestFiles.size){
                    console.log(e);
                    classListToggle(confirmWrap, ['invisible']);
                } else {
                    classListToggle(errorWrap, ['invisible']);
                    showErrorMessage('ファイルが選択されていません', errorMessage);
                    setTimeout(() =>{
                        classListToggle(errorWrap, ['invisible']);
                    },5000);
                }

                confirmBtn.addEventListener('click', () => {
                // deleteするファイルのidを送信
                // formで送信
                classListToggle(confirmWrap, ['invisible']);
                    const url = '{{ route('user.delete.download') }}';
                    sendForm(url, requestFiles);
                });
            });
        });

        confirmWrapCloseBtn.addEventListener('click', () => {
            classListToggle(confirmWrap, ['invisible']);
        });

        cancelBtn.addEventListener('click', () => {
            classListToggle(confirmWrap, ['invisible']);
        });

        if(errorWrapSession){
            setTimeout(() =>{
                    classListToggle(errorWrapSession, ['invisible']);
                },5000);
        }
    </script>
</x-app-layout>
