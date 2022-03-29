<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アップロードリンクの作成
        </h2>
    </x-slot>
    <div class="py-12">
        <x-loading-window></x-loading-window>
        <x-show-link
            :linkUrl="session('url')"
            :title="session('title')"
            :message="session('message')"
            :userMessage="session('userMessage')"  />
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
                            <x-user-list-search></x-user-list-search>
                        </div>
                        <div class="mt-4">
                            <x-label for="title" :value="__('Title')" />

                            <x-input id="title" class="block mt-1 w-full"
                                            type="text"
                                            name="title"
                                            :value="old('title')"
                                            required />
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
    let copyBtn = document.querySelector('#copy_btn');
    let copyText = document.querySelector('#copy_text');
    let copyMessage = document.querySelector('#copy_message');
    let linkWrap = document.querySelector('#show_link_wrap');
    let closeLinkBtn = document.querySelector('#show_link_close_btn');
    let loadWrap = document.querySelector('#load_wrap');
    let errorWrap = document.querySelector('#error_wrap');
    let errorWrapSess = document.querySelector('#error_wrap_session');
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

    if(closeLinkBtn) {
        closeLinkBtn.addEventListener('click', ()=> {
            classListToggle(linkWrap, ['invisible']);
        });
    }

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
