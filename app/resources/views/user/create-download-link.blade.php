<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダウンロードリンクの作成
        </h2>
    </x-slot>
    <div class="py-12">
        <x-loading-window></x-loading-window>
        <x-user-search-window></x-user-search-window>
        @if($status)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('user.create.download.link') }}">
                        @csrf
                        <div class="pb-3">
                            <div class="mb-2">
                                <x-user-list-search></x-user-list-search>
                            </div>
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
    let linkWrap = document.querySelector('#show_link_wrap');
    let closeLinkBtn = document.querySelector('#show_link_close_btn');
    let loadWrap = document.querySelector('#load_wrap');
    let errorWrap = document.querySelector('#error_wrap');
    let openSearchBtn = document.querySelector('#open_search_btn');
    let userSearchWrap = document.querySelector('#user_search_wrap');
    let errorMessage = document.querySelector('#error_message');
    let sessionErrors = "{{ $errors->any() }}";
    let searchWord = document.querySelector('#user_search');
    let searchBtn = document.querySelector('#search-btn');
    let inputUser = document.querySelector('#user');
    let userSelector = document.querySelector('#user_selector');
    let userList = document.querySelector('#user_list');
    let userName = document.querySelector('#selected_user_name');
    let userEmail = document.querySelector('#selected_user_email');
    let wrapCloseBtn = document.querySelector('#user_select_close_btn');
    let selectedUser = document.querySelector('#selected_user');
    let messageArea = document.querySelector('#message_area');
    let userDeleteBtn = document.querySelector('#user_delete_btn');

    function classListToggle(target, classNames){
            classNames.forEach(className => {
                target.classList.toggle(className);
            });
    }

    function searchUser(){
        classListToggle(loadWrap, ['invisible']);
        window.axios({
                url: '{{ route('user.list.search.registered') }}',
                method: 'post',
                dataType: 'json',
                data: JSON.stringify({
                    search: searchWord.value,
                }),
            })
            .then(response => {
                let users = response.data.users;
                let ulElm = document.createElement('ul');

                while(userList.firstChild) {
                    userList.firstChild.remove();
                }

                users.forEach(user => {
                    let liElm = document.createElement('li');
                    let nameElm = document.createElement('p');
                    let emailElm = document.createElement('p');
                    nameElm.innerText = user.name;
                    emailElm.innerText = user.email;
                    liElm.appendChild(nameElm);
                    liElm.appendChild(emailElm);
                    liElm.classList.add('user_list','py-4', 'hover:bg-gray-100', 'cursor-pointer');
                    nameElm.classList.add('user_name', 'font-semibold', 'px-4');
                    emailElm.classList.add('user_email', 'pl-6', 'pr-4');
                    liElm.dataset.userId = user.id;
                    ulElm.appendChild(liElm);
                });

                if(users.length !== 0) {
                    userList.appendChild(ulElm);
                }else {
                    let msgElm = document.createElement('p');
                    msgElm.classList.add('text-center', 'p-3');
                    msgElm.innerText = 'ユーザーが見つかりませんでした。';
                    userList.appendChild(msgElm);
                }

                userSelector.classList.remove('invisible');
            })
            .catch(async error => {
                classListToggle(errorWrap, ['invisible']);
                await showErrorMessage(error, errorMessage);
                setTimeout(() =>{
                    classListToggle(errorWrap, ['invisible']);
                },5000);
            })
            .finally(() => classListToggle(loadWrap, ['invisible']));
    }

    openSearchBtn.addEventListener('click', () => {
        classListToggle(userSearchWrap, ['invisible']);
        searchUser();
    });

    searchBtn.addEventListener('click', e => {
        searchUser();
    });

    userSelector.addEventListener('click', e => {
        let parentElm = e.srcElement.parentElement;
        if(parentElm.classList.contains('user_list')){
            inputUser.value = parentElm.dataset.userId;
            userName.innerText = parentElm.querySelector('.user_name').innerText;
            userEmail.innerText = parentElm.querySelector('.user_email').innerText;
            selectedUser.classList.remove('hidden');
            selectedUser.classList.add('flex');
            userSelector.classList.add('invisible');
            userSearchWrap.classList.add('invisible');
            messageArea.classList.remove('hidden');
            while(userList.firstChild) {
                    userList.firstChild.remove();
            }
        }
    });

    wrapCloseBtn.addEventListener('click', () => {
        classListToggle(userSearchWrap, ['invisible']);
        while(userList.firstChild) {
            userList.firstChild.remove();
        }

        searchWord.value = '';
    });

    userDeleteBtn.addEventListener('click', () => {
        selectedUser.classList.remove('flex');
        selectedUser.classList.add('hidden');
        userName.innerText = '';
        userEmail.innerText = '';
        inputUser.value = '';
        messageArea.classList.add('hidden');
    });

    if(errorWrap && sessionErrors){
        classListToggle(errorWrap, ['invisible']);
        setTimeout(() =>{
                    classListToggle(errorWrap, ['invisible']);
                },5000);
    }
    </script>
</x-app-layout>
