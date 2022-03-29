<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ファイルのアップロード
        </h2>
    </x-slot>
    <div class="py-12">
        <div id="load_wrap" class="absolute inset-0 bg-gray-500 bg-opacity-50 overflow-hidden invisible">
            <div class="absolute inset-y-2/4 inset-x-2/4">
                <div class="loader"></div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($showForm === true)
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('user.upload',['key' => $path]) }}" enctype="multipart/form-data" class="border-0"
                        id ="upload" multiple>
                        @csrf
                        <!-- Files -->
                        @for($i = 1; $i <= 5; $i++)
                        <div class="mb-2">
                            <x-label for="file{{$i}}" value="File{{$i}}" />
                            @if($i === 1)
                            <x-file-input id="file{{$i}}" class="files block mt-1 w-full" name="file[]" :value="old('file[$i]')" required/>
                            @else
                            <x-file-input id="file{{$i}}" class="files block mt-1 w-full" name="file[]" :value="old('file[$i]')" />
                            @endif
                        </div>
                        @endfor

                        <div class="mb-2 ">
                            <p class="block font-medium text-sm text-gray-700">
                                ファイルサイズの合計 <span id="upload_size">0</span> MB / <span id="limit_size"></span> MB
                            </p>
                        </div>

                        <!-- Sender -->
                        <div>
                            <x-label for="sender" :value="__('Sender')" />

                            <x-input id="sender" class="block mt-1 w-full" type="text" name="sender" :value="old('sender')" required />
                        </div>
                        <!-- Message -->
                        <div>
                            <x-label for="message" :value="__('Message')" />

                            <x-textarea id="message" class="block mt-1 w-full" name="message" :value="old('message')" rows="5" required />
                        </div>

                        <!-- Expire Date -->
                        <div>
                            <x-label for="expire_date" :value="__('Expired date')" />

                            <x-select id="expire_date" class="block mt-1 w-full" name="expire_date" :value="old('expire_date')" :options="$options" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <div id="filesize_message" class="text-red-900"></div>
                            <x-button id="register_btn" class="ml-4">
                                {{ __('Register') }}
                            </x-button>
                        </div>
                    </form>
                    <script>
                        const MAX_FILESIZE = 314572800;
                        let form = document.querySelector('#upload');
                        let files = document.querySelectorAll('.files');
                        let fileSizeSum = 0;

                        function clacUploaedFileSize(nodeFiles){
                            let total = 0
                            nodeFiles.forEach(file => {
                                if(0 < file.files.length){
                                    total += file.files[0].size;
                                }
                            });
                            return total;
                        }

                        function conversionByteToMegabyte(intBytes){
                            let mbytesVal = intBytes / (1024 ** 2);
                            return 1 < mbytesVal
                                ? Math.floor(mbytesVal)
                                : mbytesVal.toFixed(2);
                        }

                        function toggleDisabledRegisterBtn(intMaxFileSize, intUploadFileSize, elmRegisterBtn, elmMsgBox){
                                if(intMaxFileSize <= intUploadFileSize) {
                                    elmRegisterBtn.setAttribute('disabled', true);
                                    elmMsgBox.innerText = `ファイルサイズが${conversionByteToMegabyte(intMaxFileSize)} MBを超過しています。`;
                                }else {
                                    elmRegisterBtn.removeAttribute('disabled', false);
                                    elmMsgBox.innerText = '';
                                }
                        }

                        document.addEventListener('DOMContentLoaded', () => {
                            let uploadSize = document.querySelector('#upload_size');
                            let limitSize = document.querySelector('#limit_size');
                            let message = document.querySelector('#filesize_message');
                            let registerBtn = document.querySelector('#register_btn');
                            let loadWrap = document.querySelector('#load_wrap');

                            limitSize.innerText = conversionByteToMegabyte(MAX_FILESIZE);

                            form.addEventListener('change', () => {
                                fileSizeSum = clacUploaedFileSize(files);
                                uploadSize.innerText = conversionByteToMegabyte(fileSizeSum);
                                toggleDisabledRegisterBtn(MAX_FILESIZE, fileSizeSum, registerBtn, message)
                            });

                            form.addEventListener('submit', () => {
                                loadWrap.classList.remove('invisible');
                            })
                        });
                    </script>
                    @else
                        @if(!empty($upload_information) && !empty($files))
                        <h3 class="font-semibold text-l text-gray-800 leading-tight">アップロード済みのファイル</h3>
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
                                </div>
                            </div>
                        </div>
                        @php($i++)
                        @endforeach

                        <!-- Sender -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Sender</p>
                            <p class="block break-words mx-1 w-full">{{ $upload_information->sender }}</p>
                        </div>
                        <!-- Message -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Message</p>
                            <p class="block break-words mx-1 w-full">{{ $upload_information->message }}</p>
                        </div>

                        <!-- Expire Date -->
                        <div>
                            <p class="block w-full font-medium text-sm text-gray-700">Expire Date</p>
                            <p class="block break-words mx-1 w-full">{{ $upload_information->expire_date ?? \DateOptionsConstants::EXPIRE_OPTIONS['0']}}</p>
                        </div>
                        @else
                        <p>{{ $message }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
