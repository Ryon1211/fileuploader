<span id="error_wrap" class="rounded-md px-6 py-5 bg-red-200 fixed top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2 invisible">
  <p class="block w-full text-center text-xl mb-2">エラーが発生しました</p>
  <p id="error_message"></p>
</span>
@if($errors->any())
<span id="error_wrap_session" class="rounded-md px-6 py-5 bg-red-200 fixed top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
  <p class="block w-full text-center text-xl mb-2">エラーが発生しました</p>
  <p id="error_message">{{ $errors->first() }}</p>
</span>
@endif
