@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'disabled:opacity-25']) }}
    {{ $disabled ? 'disabled' : '' }} >
    <svg class="h-5 w-5 hover:text-gray-500 active:text-gray-700 transition ease-in-out duration-150"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" />  <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" />  <line x1="16" y1="21" x2="16" y2="19" />  <line x1="19" y1="16" x2="21" y2="16" />  <line x1="3" y1="8" x2="5" y2="8" />  <line x1="8" y1="3" x2="8" y2="5" /></svg>
</button>
