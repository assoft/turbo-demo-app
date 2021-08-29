<form
    @if($comment->exists)
    action="{{ route('comments.update', $comment) }}"
    @else
    action="{{ route('posts.comments.store', $post) }}"
    @endif
    method="POST"
    class="p-4 my-4 bg-white rounded"
    x-data="{ sending: false }"
    x-init="$refs.contentField.focus()"
    @turbo:submit-start="sending = true"
    @turbo:submit-end="sending = false"
>
    @csrf
    @if($comment->exists)
        @method('PUT')
    @endif

    <div class="block">
        <label class="text-gray-700 {{ $comment->exists ? 'sr-only' : '' }}">
            {{ $comment->exists ? 'Edit Comment' : 'New Comment' }}
        </label>

        <div class="mt-2">
            <x-trix-editor
                :id="\Tonysm\TurboLaravel\dom_id($comment, 'content')"
                value="{{ $comment->content->toTrixHtml() }}"
                name="content"
                x-ref="contentField"
            />
        </div>

        @error('content')
        <span class="mt-2 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>


    <div class="flex items-center justify-between mt-4">
        <x-jet-button x-bind:disabled="sending" data-controller="loading-button">
            <span x-show="sending">{{ __('Sending...') }}</span>
            <span x-show="!sending">{{ __('Save') }}</span>
        </x-jet-button>

        <a
            @if($comment->exists)
            href="{{ route('comments.show', $comment) }}"
            @else
            href="{{ route('posts.show', $post) }}"
            @endif
            class="text-base text-gray-500"
        >
            Cancel
        </a>
    </div>
</form>
