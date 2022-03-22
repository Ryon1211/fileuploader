<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アップロードリンクの作成
        </h2>
    </x-slot>
    @if(session('uploadUrl'))
    <div class="absolute inset-0" x-data="{ modalOpen: true }" x-show="modalOpen">
        <div class="sm:px-6 lg:px-8 mb-5 absolute inset-0 bg-gray-200	bg-opacity-75 transition duration-150 ease-in-out">
            <div class="max-w-7xl mx-auto bg-white shadow-sm sm:rounded-lg absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
                <x-copy-message></x-copy-message>
                <div class="w-full p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end">
                        <button @click="modalOpen = !modalOpen">
                            <svg class="h-5 w-5 text-gray-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div>アップロードリンクを新規作成しました。</div>
                    <span id="copy-text">{{ session('uploadUrl') }}</span>
                    <x-button class="ml-4" id="copy-btn">
                        コピー
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="py-12">
        <x-loading-window></x-loading-window>
        <x-error-message></x-error-message>
        <x-user-search-window></x-user-search-window>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('user.create.upload') }}">
                        @csrf
                        <div class="mb-2">
                            <x-label for="user_search" value="User" />
                            <div class="mb-3">
                                <p id="selected_user_name" class="pl-2 font-semibold"></p>
                                <p id="selected_user_email" class="pl-2 font-semibold"></p>
                            </div>
                            <input type="hidden" id="user" value="" name="user">
                            <x-button type="button" id="open_search_btn" class="mt-1  w-full flex justify-center">
                                ユーザーを検索
                            </x-button>
                        </div>

                        <div class="mt-4">
                            <x-label for="message" :value="__('Message')" />
                            <x-textarea id="message" class="block mt-1 w-full" name="message" :value="old('message')" rows="5" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-label for="expire_date" :value="__('Expired date')" />
                            <x-select id="expire_date" class="block mt-1 w-full" name="expire_date" :value="old('expire_date')" :options="$options" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Register') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    let btn = document.querySelector('#copy-btn');
    let text = document.querySelector('#copy-text');
    let message = document.querySelector('#copy-message');
    let loadWrap = document.querySelector('#load_wrap');
    let errorWrap = document.querySelector('#error_wrap');
    let openSearchBtn = document.querySelector('#open_search_btn');
    let userSearchWrap = document.querySelector('#user_search_wrap');
    let errorMessage = document.querySelector('#error_message');
    let searchWord = document.querySelector('#user_search');
    let searchBtn = document.querySelector('#search-btn');
    let inputUser = document.querySelector('#user');
    let userSelector = document.querySelector('#user_selector');
    let userList = document.querySelector('#user_list');
    let userName = document.querySelector('#selected_user_name');
    let userEmail = document.querySelector('#selected_user_email');
    let wrapCloseBtn = document.querySelector('#user_select_close_btn');

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
            userSelector.classList.add('invisible');
            userSearchWrap.classList.add('invisible');
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


    </script>
</x-app-layout>
