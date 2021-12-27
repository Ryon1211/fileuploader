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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                  @if(!empty($upload))
                  <h3 class="font-semibold text-l text-gray-800 leading-tight">アップロード済みのファイル</h3>
                  <!-- Files -->
                  @php($i = 1)
                  @foreach($files as $file)
                  <div class="my-2 relative">
                        <span class="invisible px-6 py-5 bg-red-200 absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2 error-message">
                            ダウンロードできません
                        </span>
                        <div class="rounded-md shadow-sm border border-gray-300 overflow-hidden">
                            <p class="block font-medium text-sm text-gray-50 bg-gray-800 px-3 py-2">File{{$i}}</p>
                            <div class="px-3 pt-2 pb-3">
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Name</p>
                                <p class="block break-words mx-2 mb-2 w-full">{{ $file->name }}</p>
                                <p class="block mx-1 font-medium text-sm text-gray-700">File Type</p>
                                <p class="block break-words mx-2 w-full">{{ $file->type }}</p>
                                <div class="flex items-center justify-end mt-4">
                                    @if($expire_status)
                                    <x-button class="download-btn ml-4" data-file="{{ route('user.file.download', ['id'=>$file->id])}}">
                                            ダウンロード
                                    </x-button>
                                    @else
                                    <x-button class="download-btn ml-4" disabled>
                                            ダウンロード
                                    </x-button>
                                    @endif
                                </div>
                            </div>
                        </div>
                  </div>
                  @php($i++)
                  @endforeach

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
                  @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        let downloadBtns = document.querySelectorAll('.download-btn');
        let loadWrap = document.querySelector('#load_wrap');
        let messages = document.querySelectorAll('.error-message');


        downloadBtns.forEach(btn => btn.addEventListener('click', e =>{
            loadWrap.classList.remove('invisible');
            window.axios({
                    url: e.target.dataset.file,
                    method: 'get',
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
                .catch(error => {
                    let index = Array.prototype.slice.call(downloadBtns).indexOf(btn);
                    console.log(index);
                    messages[index].classList.remove('invisible');
                    setTimeout(() =>{
                        messages[index].classList.add('invisible');
                    },10000);
                })
                .finally(() => loadWrap.classList.add('invisible'));
        }));

    </script>
</x-app-layout>
