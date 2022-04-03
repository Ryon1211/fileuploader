@props(['linkId', 'favoriteId'])


<button type="button"
    class="favorite_btn cursor-pointer transition duration-150 ease-in-out sm:rounded-md align-middle"
    data-link-id="{{ $linkId ?? '' }}"
    data-favorite-status="{{ !empty($favoriteId) ? "true" : "false" }}">

    <svg class="favorite_star_line h-5 w-5 text-gray-300 hover:text-yellow-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path class="favorite_star" d="M12 17.75l-6.172 3.245 1.179-6.873-4.993-4.867 6.9-1.002L12 2l3.086 6.253 6.9 1.002-4.993 4.867 1.179 6.873z" />
    </svg>
</button>
