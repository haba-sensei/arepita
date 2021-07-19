<ul class="mt-6">


    {{-- dashboard --}}
    <x-menu-item title="{{__('Dashboard')}}" route="dashboard">
        <x-heroicon-o-template class="w-5 h-5" />
    </x-menu-item>

    @role('admin')
        <x-menu-item title="{{__('Banners')}}" route="banners">
            <x-heroicon-o-photograph class="w-5 h-5" />
        </x-menu-item>


        <x-group-menu-item routePath="categories*" title="{{__('Categories')}}" categories="true">

            <x-menu-item title="{{__('Categories')}}" route="categories">
                <x-heroicon-o-folder class="w-5 h-5" />
            </x-menu-item>
            <x-menu-item title="{{__('SubCategories')}}" route="subcategories">
                <x-heroicon-o-document-duplicate class="w-5 h-5" />
            </x-menu-item>
        </x-group-menu-item>
    @endrole

    {{-- Vendors --}}
    <x-menu-item title="{{__('Vendors')}}" route="vendors">
        <x-heroicon-o-shopping-cart class="w-5 h-5" />
    </x-menu-item>
    @role('manager')
        <x-menu-item title="{{__('Delivery Boys')}}" route="drivers">
            <x-heroicon-o-user-group class="w-5 h-5" />
        </x-menu-item>
    @endhasanyrole

    @role('admin')
        <x-menu-item title="{{__('Reviews')}}" route="reviews">
            <x-heroicon-o-thumb-up class="w-5 h-5" />
        </x-menu-item>
    @endrole



    {{-- Products --}}
    @showProduct
        <x-group-menu-item routePath="product/*" title="{{__('Products')}}" products="true">

            <x-menu-item title="{{__('Products')}}" route="products">
                <x-heroicon-o-archive class="w-5 h-5" />
            </x-menu-item>

            <x-menu-item title="{{__('Menus')}}" route="products.menus">
                <x-heroicon-o-book-open class="w-5 h-5" />
            </x-menu-item>

            <x-menu-item title="{{__('Option Groups')}}" route="products.options.group">
                <x-heroicon-o-collection class="w-5 h-5" />
            </x-menu-item>

            <x-menu-item title="{{__('Options')}}" route="products.options">
                <x-heroicon-o-dots-horizontal class="w-5 h-5" />
            </x-menu-item>
            @role('admin')
                <x-menu-item title="{{__('Favourites')}}" route="favourites">
                    <x-heroicon-o-star class="w-5 h-5" />
                </x-menu-item>
            @endrole
        </x-group-menu-item>
    @endshowProduct

    {{-- Package --}}
    @showPackage
        <x-group-menu-item routePath="package/*" title="{{__('Package Delivery')}}" package="true">

            @hasanyrole('city-admin|admin')
                <x-menu-item title="{{__('Package Types')}}" route="package.types">
                    <x-heroicon-o-archive class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('Countries')}}" route="package.countries">
                    <x-heroicon-o-globe class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('States')}}" route="package.states">
                    <x-heroicon-o-globe-alt class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('Cities')}}" route="package.cities">
                    <x-heroicon-o-map class="w-5 h-5" />
                </x-menu-item>
            @endhasanyrole

            {{-- manager package delivery options --}}
            @role('manager')
                <x-menu-item title="{{__('Pricing')}}" route="package.pricing">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('Cities')}}" route="package.cities.my">
                    <x-heroicon-o-location-marker class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('States')}}" route="package.states.my">
                    <x-heroicon-o-globe-alt class="w-5 h-5" />
                </x-menu-item>

                <x-menu-item title="{{__('Countries')}}" route="package.countries.my">
                    <x-heroicon-o-globe class="w-5 h-5" />
                </x-menu-item>

            @endhasanyrole

        </x-group-menu-item>

    @endshowPackage

    {{-- orders --}}
    <x-group-menu-item routePath="order/*" title="{{__('Orders')}}" orders="true">

        <x-menu-item title="{{__('Orders')}}" route="orders">
            <x-heroicon-o-shopping-bag class="w-5 h-5" />
        </x-menu-item>
        @hasanyrole('city-admin|admin')
            <x-menu-item title="{{__('Delivery Address')}}" route="delivery.addresses">
                <x-heroicon-o-location-marker class="w-5 h-5" />
            </x-menu-item>
        @endhasanyrole

    </x-group-menu-item>

    @hasanyrole('city-admin|admin')
        <x-menu-item title="{{__('Coupons')}}" route="coupons">
            <x-heroicon-o-receipt-tax class="w-5 h-5" />
        </x-menu-item>

    @endhasanyrole

    {{-- Users --}}
    @hasanyrole('city-admin|admin')
        <x-menu-item title="{{__('Users')}}" route="users">
            <x-heroicon-o-user-group class="w-5 h-5" />
        </x-menu-item>
    @endhasanyrole

    @hasanyrole('admin')

        {{-- wallet transactions --}}
        <x-menu-item title="{{__('Wallet Transactions')}}" route="wallet.transactions">
            <x-heroicon-o-collection class="w-5 h-5" />
        </x-menu-item>

    @endhasanyrole

    {{-- Earings --}}
    <x-group-menu-item routePath="earnings/*" title="{{__('Earnings')}}" earnings="true">
        @hasanyrole('city-admin|admin')
            <x-menu-item title="{{__('Vendor Earnings')}}" route="earnings.vendors">
                <x-heroicon-o-shopping-bag class="w-5 h-5" />
            </x-menu-item>
        @endhasanyrole

        <x-menu-item title="{{__('Driver Earnings')}}" route="earnings.drivers">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>

    {{-- Payouts --}}
    <x-group-menu-item routePath="payouts*" title="{{__('Payouts')}}" payouts="true">
        @hasanyrole('city-admin|admin')
            <x-menu-item title="{{__('Vendor Payouts')}}" route="payouts"
                rawRoute="{{ route('payouts', ['type' => 'vendors']) }}">
                <x-heroicon-o-shopping-bag class="w-5 h-5" />
            </x-menu-item>
        @endhasanyrole
        <x-menu-item title="{{__('Driver Payouts')}}" route="payouts"
            rawRoute="{{ route('payouts', ['type' => 'drivers']) }}">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>

    {{-- Payment methods --}}
    @hasanyrole('manager')
        <x-menu-item title="{{__('Payment Methods')}}" route="payment.methods.my">
            <x-heroicon-o-cash class="w-5 h-5" />
        </x-menu-item>
    @endhasanyrole


    @hasanyrole('admin')
        {{-- notifications --}}
        <x-menu-item title="{{__('Notifications')}}" route="notification.send">
            <x-heroicon-o-bell class="w-5 h-5" />
        </x-menu-item>
        <x-group-menu-item routePath="operations/*" title="{{__('Operations')}}" icon="heroicon-o-server">

            {{-- backups --}}
            <x-menu-item title="{{__('Backup')}}" route="backups">
                <x-heroicon-o-database class="w-5 h-5" />
            </x-menu-item>

            {{-- import --}}
            <x-menu-item title="{{__('Import')}}" route="imports">
                <x-heroicon-o-cloud-upload class="w-5 h-5" />
            </x-menu-item>

            {{-- logs --}}
            <x-menu-item title="{{__('Logs')}}" route="logs" ex="true">
                <x-heroicon-o-shield-exclamation class="w-5 h-5" />
            </x-menu-item>
        </x-group-menu-item>


        {{-- Settings --}}
        <x-group-menu-item routePath="setting/*" title="{{__('Settings')}}" icon="heroicon-o-cog">

            {{-- Currencies --}}
            <x-menu-item title="{{__('Currencies')}}" route="currencies">
                <x-heroicon-o-currency-dollar class="w-5 h-5" />
            </x-menu-item>

            {{-- Payment methods --}}
            <x-menu-item title="{{__('Payment Methods')}}" route="payment.methods">
                <x-heroicon-o-cash class="w-5 h-5" />
            </x-menu-item>

            {{-- App Settings --}}
            <x-menu-item title="{{__('Mobile App Settings')}}" route="settings.app">
                <x-heroicon-o-device-mobile class="w-5 h-5" />
            </x-menu-item>

            {{-- Settings --}}
            <x-menu-item title="{{__('General Settings')}}" route="settings">
                <x-heroicon-o-cog class="w-5 h-5" />
            </x-menu-item>

            {{-- Mail Settings --}}
            <x-menu-item title="{{__('Server Settings')}}" route="settings.server">
                <x-heroicon-o-server class="w-5 h-5" />
            </x-menu-item>

            {{-- translation --}}
            <x-menu-item title="{{__('Translation')}}" route="translation">
                <x-heroicon-o-translate class="w-5 h-5" />
            </x-menu-item>

            {{-- upgrade --}}
            <x-menu-item title="{{__('Upgrade')}}" route="upgrade">
                <x-heroicon-o-cloud-upload class="w-5 h-5" />
            </x-menu-item>
        </x-group-menu-item>
    @endhasanyrole



</ul>
