<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow sm:rounded-lg p-6">

                @if($notifications->count() === 0)
                    <p class="text-gray-600">No notifications yet.</p>
                @endif

                @foreach($notifications as $n)
                    <div class="border-b py-4 flex justify-between items-start gap-3">

                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $n->data['message'] ?? 'Notification' }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $n->created_at->diffForHumans() }}
                            </p>
                        </div>

                        @if(is_null($n->read_at))
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button class="text-sm text-blue-600 underline">
                                    Mark as read
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-green-600 font-semibold">
                                Read
                            </span>
                        @endif

                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>

                <form method="POST" action="{{ route('notifications.readAll') }}" class="mt-4">
                    @csrf
                    <button class="px-4 py-2 border rounded-md text-sm hover:bg-gray-50">
                        Mark All as Read
                    </button>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>
