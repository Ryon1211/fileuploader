<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <x-loading-window></x-loading-window>
    <x-error-message></x-error-message>
    <x-confirm-window></x-confirm-window>
    <x-copy-message></x-copy-message>
    <div class="py-12 relative">
        <div class="max-w-7xl mb-5 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <div class="border-gray-200 w-full flex">
                    <div class="w-9/12">
                        <x-input id="search" class="w-full" type="text" :value="Request::get('search') ?? ''" />
                    </div>
                    <div class="w-3/12 flex justify-end">
                        <x-button class="search-btn ml-4" data-search="link">
                            リンクの検索
                        </x-button>
                        <x-button class="search-btn ml-4" data-search="file">
                            ファイルの検索
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                @if($upload_links && !$upload_links->isEmpty())
                <div class="border-gray-200">
                    <div class="pb-3 border-b border-gray-500 flex min-w-70rem">
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="status">
                            ファイルの状態
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-4/12 mx-2 flex justify-center items-center cursor-pointer" data-sort="title">
                            リンクタイトル
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="create">
                            リンク作成日時
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="expire">
                            リンク有効期限
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="w-1/12 text-center">リンク</div>
                        <div class="w-1/12 text-center">削除</div>
                    </div>
                    <ul>
                        @foreach ($upload_links as $upload_link)
                        <li class="pt-4 pb-3 list-none border-b border-gray-200 flex min-w-70rem hover:bg-gray-200">
                            <div class="w-2/12 ml-3">
                                <x-upload-status
                                class="upload-detail-btn"
                                :href="route('user.show.files', ['key' => $upload_link->path])"
                                :uploadLink="$upload_link"></x-upload-status>
                            </div>
                            <div class="w-4/12 mx-2 break-words"> {{ $upload_link->message }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->created_at }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->expire_date ?? '期限なし' }}</div>
                            <div class="w-1/12 text-center">
                                @if(\ExpireDateUtil::checkExpireDate($upload_link->expire_date) && !$upload_link->upload_id)
                                <x-link-button type="button" class="copy-btn" data-src="{{ route('user.upload', ['key' => $upload_link->path]) }}"></x-link-button>
                                @else
                                <x-link-button type="button" class="" :disabled="true"></x-link-button>
                                @endif
                            </div>
                            <div class="w-1/12 text-center">
                                <x-link-delete-button type="button" class="delete-btn" data-link-id="{{ $upload_link->id }}"></x-link-delete-button>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-6">
                    {{$upload_links->links()}}
                </div>
                @elseif($files && !$files->isEmpty())
                <div class="border-gray-200">
                    <div class="pb-3 border-b border-gray-500 flex min-w-70rem">
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="status">
                            ファイルの状態
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-4/12 mx-2 flex justify-center items-center cursor-pointer" data-sort="title">
                            ファイルタイトル
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="create">
                            リンク作成日時
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-2/12 flex justify-center items-center cursor-pointer" data-sort="expire">
                            リンク有効期限
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="w-2/12 text-center">ダウンロード</div>
                    </div>
                    <ul>
                        @foreach ($files as $file)
                        <li class="pt-4 pb-3 list-none border-b border-gray-200 flex min-w-70rem hover:bg-gray-200">
                            <div class="w-2/12 ml-3">
                                <x-file-status
                                class="upload-detail-btn"
                                :href="route('user.show.files', ['key' => $file->path])"
                                :file="$file"></x-file-status>
                            </div>
                            <div class="w-4/12 mx-2 break-words"> {{ $file->name }}</div>
                            <div class="w-2/12 text-center"> {{ $file->created_at }}</div>
                            <div class="w-2/12 text-center"> {{ $file->expire_date ?? '期限なし' }}</div>
                            <div class="w-2/12 text-center">
                                @if(\ExpireDateUtil::checkExpireDate($file->expire_date))
                                <x-download-button type="button" class="download-btn" data-file="{{ $file->id }}"></x-download-button>
                                @else
                                <x-download-button type="button" class="download-btn" :disabled="true"></x-download-button>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-6">
                    {{$files->links()}}
                </div>
                @else
                    <p>なにも見つかりませんでした。</p>
                @endif
            </div>
        </div>
    </div>
    <script>
        let searchInput = document.querySelector('#search');
        let searchBtns = document.querySelectorAll('.search-btn');
        let sortBtns = document.querySelectorAll('.sort-btn');
        let copyBtns = document.querySelectorAll('.copy-btn');
        let deleteBtns = document.querySelectorAll('.delete-btn');
        let downloadBtns = document.querySelectorAll('.download-btn');
        let message = document.querySelector('#copy_message');
        let confirmWrapCloseBtn = document.querySelector('#confirm_close_btn');
        let confirmBtn = document.querySelector('#confirm-btn');
        let linkSearchBtn = document.querySelector('#link-search-btn');
        let fileSearchBtn = document.querySelector('#file-search-btn');
        let cancelBtn = document.querySelector('#cancel-btn');
        let loadWrap = document.querySelector('#load_wrap');
        let errorWrap = document.querySelector('#error_wrap');
        let errorWrapSession = document.querySelector('#error_wrap_session');
        let confirmWrap = document.querySelector('#confirm_wrap');
        let errorMessage = document.querySelector('#error_message');

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

        const route = '{{ route('user.dashboard') }}';
        const getParams = new URLSearchParams(window.location.search);
        const targetParams = getParams.get('target') ?? '';
        const searchParams = getParams.get('search') ?? '';
        const orderParams = getParams.get('orderby')
                            ? getParams.get('orderby').split('_') : [];

        sortBtns.forEach(btn => {
            btn.addEventListener('click', e => {
                const sortTarget = e.currentTarget.dataset.sort;
                let order = orderParams[0] === sortTarget && orderParams[1] === 'asc'
                    ? 'desc' : 'asc';

                getParams.set('orderby', `${sortTarget}_${order}`);
                window.location = `${route}?${getParams.toString()}`;
            });
        });

        searchBtns.forEach(btn => {
            btn.addEventListener('click', e => {
                const searchTarget = e.currentTarget.dataset.search;
                const searchWords = searchInput.value;

                getParams.set('target', searchTarget);
                getParams.set('search', searchWords);
                getParams.delete('page');
                window.location = `${route}?${getParams.toString()}`;
            });
        })

        sortBtns.forEach(btn => {
            if(btn.dataset.sort === orderParams[0]){
                let arrow = btn.querySelector('.order-arrow');
                arrow.classList.toggle('hidden');
                if(orderParams[1] === 'desc'){
                    arrow.classList.toggle('rotate-180');
                }
            }
        })

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


        downloadBtns.forEach(btn =>btn.addEventListener('click', e =>{
            let requestFiles = new Set();
            let target = e.currentTarget;
            let fileId = target.dataset.file;

            requestFiles.has(fileId)
                ? requestFiles.delete(fileId)
                : requestFiles.add(fileId);

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
                const filename = headers[headers.length-1].replace(/"/g,"");
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
        }));

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', e => {
                let requestFiles = new Set();
                requestFiles.add(e.currentTarget.dataset.linkId);
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
                    const url = '{{ route('user.delete.upload') }}';
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
