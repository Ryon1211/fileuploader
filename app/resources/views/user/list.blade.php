<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            List
        </h2>
    </x-slot>
    <x-error-message></x-error-message>
    <x-confirm-window></x-confirm-window>
    <div class="py-12 relative">
        <div class="max-w-7xl mb-5 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('user.list.search') }}">
                @csrf
                <div class="border-gray-200 w-full flex">
                    <div class="w-9/12">
                        <x-input id="keyword" name="keyword" class="w-full" type="text" :value=" $keyword ?? ''" />
                    </div>
                    <div class="w-3/12 flex justify-end">
                        <x-button type="submit" class="search-btn ml-4 justify-center w-full" data-search="link">
                            ユーザーの検索
                        </x-button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                @if(!$users->isEmpty())
                <div class="border-gray-200">
                    <div class="pb-3 border-b border-gray-500 flex min-w-70rem">
                        <div class="w-2/12 text-center">
                            登録/解除
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-5/12 flex justify-center items-center cursor-pointer" data-sort="name">
                            ユーザー名
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                        <div class="sort-btn w-5/12 mx-2 flex justify-center items-center cursor-pointer" data-sort="email">
                            ユーザーのメールアドレス
                            <span class="order-arrow hidden ml-2 transform origin-center">
                                <svg class="h-3 w-3 text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="12" y1="5" x2="12" y2="19" />  <polyline points="19 12 12 19 5 12" /></svg>
                            </span>
                        </div>
                    </div>
                    <ul>
                        @foreach ($users as $user)
                        <li class="pt-4 pb-3 list-none border-b border-gray-200 flex min-w-70rem hover:bg-gray-200">
                            <div class="w-2/12 text-center">
                                <button type ="button" class="register-btn hover:bg-gray-100 cursor-pointer transition duration-150 ease-in-out sm:rounded-md" data-user-id="{{ $user->id }}" data-user-status="{{ $user->user_id === Auth::user()->id ? 'true' : 'false' }}">
                                    <svg class="regist-status-icon inline-block align-bottom h-6 w-6"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"/>
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    <span class="regist-status-text pr-1 text-gray-800 text-sm"></span>
                                </button>
                            </div>
                            <div class="w-5/12 ml-3">
                                {{ $user->name }}
                            </div>
                            <div class="w-5/12 mx-2 break-words">
                                {{ $user->email }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-6">
                    {{$users->links()}}
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
        let registerBtns = document.querySelectorAll('.register-btn');
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

        const route = '{{ route('user.list') }}';
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

        sortBtns.forEach(btn => {
            if(btn.dataset.sort === orderParams[0]){
                let arrow = btn.querySelector('.order-arrow');
                arrow.classList.toggle('hidden');
                if(orderParams[1] === 'desc'){
                    arrow.classList.toggle('rotate-180');
                }
            }
        })

        registerBtns.forEach(btn => {
            const REGISTERED = '登録済み';
            const UNREGISTERD = '未登録';
            let registerIcon = btn.querySelector('.regist-status-icon');
            let registerText = btn.querySelector('.regist-status-text');

            if(btn.dataset.userStatus === "true"){
                registerIcon.classList.add('text-green-500');
                registerText.innerText = REGISTERED
            }

            if(btn.dataset.userStatus === "false"){
                registerIcon.classList.add('text-gray-500');
                registerText.innerText = UNREGISTERD
            }

            btn.addEventListener('click', e =>{
                let target = e.currentTarget;
                let userId = target.dataset.userId;

                window.axios({
                    url: '{{ route('user.list.register') }}',
                    method: 'post',
                    dataType: 'json',
                    data: JSON.stringify({
                        id: userId,
                    }),
                })
                .then(response => {
                    registerIcon.classList.toggle('text-green-500');
                    registerIcon.classList.toggle('text-gray-500');
                    if(registerText.innerText === UNREGISTERD){
                        registerText.innerText = REGISTERED;
                    } else {
                        registerText.innerText = UNREGISTERD;
                    }

                })
                .catch(async error => {
                    classListToggle(errorWrap, ['invisible']);

                    await showErrorMessage(error, errorMessage);
                    setTimeout(() =>{
                        classListToggle(errorWrap, ['invisible']);
                    },5000);
                })
            })
        });

        if(errorWrapSession){
            setTimeout(() =>{
                    classListToggle(errorWrapSession, ['invisible']);
                },5000);
        }
    </script>
</x-app-layout>
