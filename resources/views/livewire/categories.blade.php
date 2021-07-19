@section('title',  __('Categories') )
<div>

    <x-baseview title="{{ __('Categories') }}">
        <livewire:tables.category-table />
    </x-baseview>

    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="Save" action="save">
            <p class="text-xl font-semibold">{{ __('Create Category') }}</p>
            <x-input title="{{ __('Name') }}" name="name" placeholder="Vegetables" />
            <x-media-upload
                        title="{{ __('Photo') }}"
                        name="photo"
                        {{--  preview="{{ $selectedModel->photo ?? '' }}"  --}}
                        :photo="$photo"
                        :photoInfo="$photoInfo"
                        types="PNG or JPEG"
                        rules="image/*" />
            <x-checkbox
                    title="{{ __('Active') }}"
                    name="isActive" :defer="false" />

        </x-modal>
    </div>

    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update">

            <p class="text-xl font-semibold">{{ __('Edit Category') }}</p>
            <x-input title="{{ __('Name') }}" name="name" placeholder="Vegetables" />
            <x-media-upload
                        title="{{ __('Photo') }}"
                        name="photo"
                        preview="{{ $selectedModel->photo ?? '' }}"
                        :photo="$photo"
                        :photoInfo="$photoInfo"
                        types="PNG or JPEG"
                        rules="image/*" />
            <x-checkbox
                    title="{{ __('Active') }}"
                    name="isActive" :defer="false" />


        </x-modal>
    </div>
</div>


