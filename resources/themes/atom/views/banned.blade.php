<x-app-layout>
    @push('title', __('Banned'))

    <div class="col-span-12 flex justify-center dark:text-gray-100">
        <div class="space-y-4 lg:w-1/2">
            <div class="w-full rounded-md bg-red-500 p-2 text-center text-white">
                {{ __('It seems like you are banned off :hotel', ['hotel' => setting('hotel_name')]) }}
            </div>

            <div class="rounded-md p-2 shadow bg-white dark:bg-gray-800">
                <div class="flex justify-between">
                    <div class="flex flex-col px-1">
                        @php
                            $isIpBan = isset($ipBan) && $ban && $ban->is($ipBan);

                            $banTypeLabel = $isIpBan
                                ? __('IP Based')
                                : __('Username blocked');

                            $banReason = $ban?->reason ?? __('No reason provided');

                            if ($ban && $ban->expires_at) {
                                $banExpiration = $ban->expires_at->format('Y/m/d');
                            } else {
                                $banExpiration = __('Never');
                            }
                        @endphp

                        <div class="max-w-[380px]">
                            <p>
                                <strong>{{ __('Ban type:') }}</strong> {{ $banTypeLabel }}
                            </p>

                            <p>
                                <strong>{{ __('Ban reason:') }}</strong> {{ $banReason }}
                            </p>

                            <p>
                                <strong>{{ __('Ban expiration:') }}</strong> {{ $banExpiration }}
                            </p>

                            @if($isIpBan && isset($ban->ip_address))
                                <p>
                                    <strong>{{ __('IP address:') }}</strong> {{ $ban->ip_address }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-4 max-w-[380px]">
                            <p class="mb-4">
                                {{ __('If you believe this is a mistake, please reach out to one of our staff members through our Discord server!') }}
                            </p>

                            <a href="{{ setting('discord_invitation_link') }}" target="_blank">
                                <x-form.primary-button>
                                    {{ __('Join discord') }}
                                </x-form.primary-button>
                            </a>
                        </div>
                    </div>

                    <div class="mr-8 hidden items-center lg:flex">
                        <img src="{{ asset('assets/images/angry_frank.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
