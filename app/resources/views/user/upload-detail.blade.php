<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アップロードの詳細
        </h2>
    </x-slot>
    <div class="py-12">
        <div id="load_wrap" class="absolute z-10 inset-0 bg-gray-500 bg-opacity-50 overflow-hidden invisible">
            <div class="absolute inset-y-2/4 inset-x-2/4">
                <div class="loader"></div>
            </div>
        </div>
        <span id="error_wrap" class="rounded-md px-6 py-5 bg-red-200 absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2 invisible">
            <p class="block w-full text-center text-xl mb-2">エラーが発生しました</p>
            <p id="error_message"></p>
        </span>
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
                            <x-button id="link-create-btn" class="ml-4">
                                    ダウンロードリンクの作成
                            </x-button>
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
            </div>
        </div>
    </div>
    <script>
        let downloadBtns = document.querySelector('#download-btn');
        let linkCreateBtn = document.querySelector('#link-create-btn');
        let fileSelectBtns = document.querySelectorAll('.file-select-btn');
        let copyBtn = document.querySelector('#copy_btn');
        let loadWrap = document.querySelector('#load_wrap');
        let errorWrap = document.querySelector('#error_wrap');
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

        linkCreateBtn.addEventListener('click', ()=> {
            loadWrap.classList.remove('invisible');

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
                errorWrap.classList.remove('invisible');
                if(error.response.data){
                    errorMessage.innerText = await error.response.data.message;
                }
                setTimeout(() =>{
                    errorWrap.classList.add('invisible');
                },5000);
            })
            .finally(() => loadWrap.classList.add('invisible'));
        });


    </script>
</x-app-layout>
