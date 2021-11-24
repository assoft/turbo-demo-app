<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <x-slot name="header">
        <a href="{{ route('posts.index') }}"
           class="flex items-center space-x-1 text-xl font-semibold leading-tight text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span>back</span>
        </a>
    </x-slot>

    <turbo-echo-stream-source channel="App.Models.Post.{{ $post->id }}"></turbo-echo-stream-source>

    <div class="py-2 md:py-12">
        <div class="mx-auto space-y-12 max-w-7xl sm:px-6 lg:px-8">
            <div class="p-2 m-4 bg-white rounded shadow md:p-8 lg:p-16">
                @include('posts._post', ['post' => $post])

                <turbo-frame id="@domid($post->entry, 'reactions')" src="{{ route('entries.reactions.index', $post->entry) }}" class="block mt-4"></turbo-frame>

                <div class="w-2/12 mx-auto mt-8 border-b"></div>

                <div class="pt-8">
                    <h3 class="flex items-center justify-center mb-8 space-x-1 text-xl font-semibold leading-tight text-gray-800">
                        <div>Comments</div>
                        <div id="@domid($post->entry, 'comments_count')">
                            @include('entry_comments._entry_comments_count', ['entry' => $post->entry])
                        </div>
                    </h3>

                    <turbo-frame id="@domid($post->entry, 'comments')" src="{{ route('entries.comments.index', $post->entry) }}" class="flex flex-col">
                    </turbo-frame>

                    <div>
                        <turbo-frame id="@domid($post->entry, 'create_comment')">
                            @include('entries._create_comment_trigger', ['entry' => $post->entry])
                        </turbo-frame>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
