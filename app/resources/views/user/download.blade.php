<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ファイルのダウンロード
        </h2>
    </x-slot>
    <div class="py-12">
        <x-show-link
            :linkUrl="session('url')"
            :title="session('title')"
            :message="session('message')"
            :userMessage="session('userMessage')"  />
        <x-loading-window></x-loading-window>
        <x-error-message></x-error-message>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(!empty($upload))
                    <h3 class="font-semibold text-l text-gray-800 leading-tight">アップロード済みのファイル</h3>
                    <div class="py-3">
                        <!-- Expire Date -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Expire Date</p>
                            <p class="block break-words mx-1 w-full">{{ $expire_date ?? \DateOptionsConstants::EXPIRE_OPTIONS['0'] }}</p>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            @if($expire_status)
                            <p>ファイルを選択してください</p>
                            <x-button id="download-btn" class="ml-4">
                                    ダウンロード
                            </x-button>
                            @else
                            <p>ダウンロード可能期間が終了しました</p>
                            @endif
                        </div>
                    </div>
                    <!-- Files -->
                    @php($i = 1)
                    @foreach($files as $file)
                    <div class="my-2">
                        <div class="rounded-md shadow-sm border border-gray-300 overflow-hidden">
                            <p class="block font-medium text-sm text-gray-50 bg-gray-800 px-3 py-2">File{{$i}}</p>
                            <div class="px-3 pt-2 pb-3">
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Name</p>
                                <p class="block break-words mx-2 mb-2 w-full">{{ $file->file->name }}</p>
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Type</p>
                                <p class="block break-words mx-2 w-full">{{ $file->file->type }}</p>
                                @if($expire_status)
                                <div class="flex items-center justify-end mt-4">
                                    <x-button class="file-select-btn ml-4" data-file="{{$file->file->id}}">
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
            </div>
        </div>
    </div>
    <script>
        let downloadBtns = document.querySelector('#download-btn');
        let linkCreateBtn = document.querySelector('#link-create-btn');
        let fileSelectBtns = document.querySelectorAll('.file-select-btn');
        let copyBtn = document.querySelector('#copy_btn');
        let copyText = document.querySelector('#copy_text');
        let copyMessage = document.querySelector('#copy_message');
        let linkWrap = document.querySelector('#show_link_wrap');
        let closeLinkBtn = document.querySelector('#show_link_close_btn');
        let loadWrap = document.querySelector('#load_wrap');
        let errorWrap = document.querySelector('#error_wrap');
        let errorMessage = document.querySelector('#error_message');
        let requestFiles = new Set();
        let pathName = window.location.pathname.split('/');
        let key = pathName[pathName.length-1];

        function classListToggle(target, classNames){
            classNames.forEach(className => {
                target.classList.toggle(className);
            });
        }

        if(copyBtn){
            copyBtn.addEventListener('click', () => {
            let innerText = copyText.innerText;

            if(navigator.clipboard){
                navigator.clipboard.writeText(innerText);
                copyMessage.classList.remove('invisible');
                setTimeout(() =>{
                    copyMessage.classList.add('invisible');
                },3000);
            }
        });
        }

        if(closeLinkBtn) {
            closeLinkBtn.addEventListener('click', ()=> {
                classListToggle(linkWrap, ['invisible']);
            });
        }

        fileSelectBtns.forEach(btn => btn.addEventListener('click', e => {
            let target = e.currentTarget;
            let fileId = target.dataset.file;

            target.classList.toggle('bg-gray-800');
            target.classList.toggle('text-white');
            target.classList.toggle('bg-gray-100');
            target.classList.toggle('text-green-500');

            if (requestFiles.has(fileId)){
                requestFiles.delete(fileId);
            } else{
                requestFiles.add(fileId);
            }
        }));

        downloadBtns.addEventListener('click', e =>{
            loadWrap.classList.remove('invisible');
            window.axios({
                url: '{{ route('user.file.download') }}',
                method: 'post',
                dataType: 'json',
                data: JSON.stringify({
                    file: [...requestFiles],
                    key: key,
                }),
                responseType: 'blob'
            })
            .then(response => {
                console.log(JSON.stringify({
                    file: [...requestFiles],
                    key: key,
                }));
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
                console.log(JSON.stringify({
                    file: [...requestFiles],
                    key: key,
                }));
                errorWrap.classList.remove('invisible');
                if(error.response.data){
                    const text = JSON.parse(await error.response.data.text());
                    errorMessage.innerText = text.message;
                }
                setTimeout(() =>{
                    errorWrap.classList.add('invisible');
                },5000);
            })
            .finally(() => loadWrap.classList.add('invisible'));
        });
    </script>
</x-app-layout>
