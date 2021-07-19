@section('title',  __('Coupons') )
<div>

    <x-baseview title="{{ __('Coupons') }}">
        <livewire:tables.coupon-table />
    </x-baseview>

    {{--  new form  --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Save') }}" action="save" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Create Coupon') }}</p>

            <x-input title="{{ __('Code') }}" name="code" />
            <x-input title="{{ __('Description') }}" name="description" />

            <div class="grid gap-4 md:grid-cols-2">
                <x-input title="{{ __('Discount') }}" name="discount" />
                <x-input title="{{ __('Expires On') }}" name="expires_on" type="date" />

                <x-checkbox
                    title="{{ __('Is Percentage?') }}"
                    name="percentage" :defer="false" />

                <x-checkbox
                    title="{{ __('Active') }}"
                    name="isActive" :defer="false" />

            </div>

            {{-- products --}}
            <x-select2
                title="{{ __('Products') }}"
                :options="$products"
                name="productsIDs"
                id="productsSelect2"
                :multiple="true"
                width="100"
                :ignore="true"
                 />

            {{-- vendors --}}
            <x-select2
                title="{{ __('Vendors') }}"
                :options="$vendors"
                name="vendorsIDs"
                id="vendorsSelect2"
                :multiple="true"
                width="100"
                :ignore="true"
                 />


        </x-modal>
    </div>

    {{--  update form  --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update"  :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Update Option') }}</p>
            <x-input title="{{ __('Code') }}" name="code" />
            <x-input title="{{ __('Description') }}" name="description" />

            <div class="grid gap-4 md:grid-cols-2">
                <x-input title="{{ __('Discount') }}" name="discount" />
                <x-input title="{{ __('Expires On') }}" name="expires_on" type="date" />

                <x-checkbox
                    title="{{ __('Is Percentage?') }}"
                    name="percentage" :defer="false" />

                <x-checkbox
                    title="{{ __('Active') }}"
                    name="isActive" :defer="false" />

            </div>

            {{-- products --}}
            <x-select2
                title="{{ __('Products') }}"
                :options="$products"
                name="productsIDs"
                id="editProductsSelect2"
                :multiple="true"
                width="100"
                :ignore="true"
                 />

            {{-- vendors --}}
            <x-select2
                title="{{ __('Vendors') }}"
                :options="$vendors"
                name="vendorsIDs"
                id="editVendorsSelect2"
                :multiple="true"
                width="100"
                :ignore="true"
                 />


        </x-modal>
    </div>

    {{--  details moal  --}}
    <div x-data="{ open: @entangle('showDetails') }">
        <x-modal-lg>

            <p class="text-xl font-semibold">{{ __('Coupon Details') }}</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-details.item title="{{ __('Code') }}" text="{{ $selectedModel->code ?? '' }}" />
                <x-details.item title="{{ __('Description') }}" text="{{ $selectedModel->description ?? '' }}" />

                {{-- discount --}}
                <div>
                    <x-label title="{{ __('Discount') }}" />
                    @if( $selectedModel )
                        @include('components.table.coupon_discount_price', [ "model" => $selectedModel ] )
                    @endif
                </div>
                <x-details.item title="{{ __('Expires On') }}" text="{{ $selectedModel->formatted_expires_on ?? ''}}" />
                <x-details.item title="{{ __('Useable Times') }}" text="{{ $selectedModel->times ?? 'Unlimited'}}" />
                <div>
                    <x-label title="{{ __('Status') }}" />
                    <x-table.active :model="$selectedModel" />
                </div>



            </div>
            <div class="grid grid-cols-1 gap-4 pt-4 mt-4 border-t lg:grid-cols-2">

                <div>
                    <x-label title="{{ __('Products') }}" />
                    {{-- chips --}}
                    <div class="flex flex-wrap justify-start">
                        @if ( $selectedModel )
                            @foreach ($selectedModel->products as $product)
                                <x-table.chip text="{{ $product->name }}"/>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div>
                    <x-label title="{{ __('Vendors') }}" />
                    <div class="flex flex-wrap justify-start">
                        @if ( $selectedModel )
                            @foreach ($selectedModel->vendors as $vendor)
                                <x-table.chip text="{{ $vendor->name }}"/>
                            @endforeach
                        @endif
                    </div>
                </div>


            </div>

        </x-modal-lg>
    </div>
</div>

