<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <x-error-message></x-error-message>
    <x-confirm-window></x-confirm-window>
    <x-copy-message></x-copy-message>
    <div class="py-12 relative">
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
                                class="upload-detail-btn"
                                :href="route('user.show.files', ['key' => $upload_link->query])"
                                :uploadLink="$upload_link->upload->upload_status ?? null"></x-upload-status>
                            </div>
                            <div class="w-4/12 mx-2 break-words"> {{ $upload_link->message }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->created_at }}</div>
                            <div class="w-2/12 text-center"> {{ $upload_link->expire_date }}</div>
                            <div class="w-1/12 text-center">
                                <x-link-button type="button" class="copy-btn" data-src="{{ route('user.upload', ['key' => $upload_link->query]) }}"></x-link-button>
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
            </div>
        </div>
    </div>
    <script>
        let copyBtns = document.querySelectorAll('.copy-btn');
        let deleteBtns = document.querySelectorAll('.delete-btn');
        let message = document.querySelector('#copy-message');
        let confirmWrapCloseBtn = document.querySelector('#confirm_close_btn');
        let confirmBtn = document.querySelector('#confirm-btn');
        let cancelBtn = document.querySelector('#cancel-btn');
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
