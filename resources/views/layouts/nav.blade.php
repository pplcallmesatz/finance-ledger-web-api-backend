<nav x-data="{ open: false }" class="bg-white shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard.index') }}" class="font-bold text-emerald-700	 text-lg">
                        Ledger
                    </a>
                </div>

                @auth
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        @can('view-any', App\Models\SalesLedger::class)
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('sales-ledgers.index') }}" class=" px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 ">
                                Sales Ledgers
                                </a>
                            </div>
                        @endcan
                        @can('view-any', App\Models\ExpenseLedger::class)
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('expense-ledgers.index') }}" class=" px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 ">
                                Expense Ledgers
                                </a>
                            </div>
                        @endcan

                        @can('view-any', App\Models\Transaction::class)
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('transactions.index') }}" class=" px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 ">
                                Transactions
                                </a>
                            </div>
                        @endcan
                        
                        @can('view-any', App\Models\Transaction::class)
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('pending') }}" class=" px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 ">
                                Pending
                                </a>
                            </div>
                        @endcan
                        
                    </div>

                    
                    <x-nav-dropdown title="Apps" align="right" width="48">
                            
                            <x-dropdown-link href="{{ route('users.index') }}">
                            Users
                            </x-dropdown-link>
                            <!-- @can('view-any', App\Models\User::class) -->
                            <!-- @endcan -->
                            
                            @can('view-any', App\Models\Product::class)
                            <x-dropdown-link href="{{ route('products.index') }}">
                            Products
                            </x-dropdown-link>
                            @endcan

                            @can('view-any', App\Models\ProductMaster::class)
                            <x-dropdown-link href="{{ route('product-masters.index') }}">
                            Product Masters
                            </x-dropdown-link>
                            @endcan
                            
                            @can('view-any', App\Models\CategoryMaster::class)
                            <x-dropdown-link href="{{ route('category-masters.index') }}">
                            Category Masters
                            </x-dropdown-link>
                            @endcan
                            
                            
                            
                            
                            
                            
                    </x-nav-dropdown>

                @endauth
                
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
        <div class="pt-2 pb-3 space-y-1">
                @can('view-any', App\Models\SalesLedger::class)
                <x-responsive-nav-link href="{{ route('sales-ledgers.index') }}">
                Sales Ledgers
                </x-responsive-nav-link>
                @endcan
                @can('view-any', App\Models\ExpenseLedger::class)
                <x-responsive-nav-link href="{{ route('expense-ledgers.index') }}">
                Expense Ledgers
                </x-responsive-nav-link>
                @endcan
                @can('view-any', App\Models\Transaction::class)
                <x-responsive-nav-link href="{{ route('transactions.index') }}">
                Transactions
                </x-responsive-nav-link>
                @endcan
                @can('view-any', App\Models\Product::class)
                <x-responsive-nav-link href="{{ route('products.index') }}">
                Products
                </x-responsive-nav-link>
                @endcan
                
                <x-responsive-nav-link href="{{ route('users.index') }}">
                Users
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('pending') }}">
                Pendings
                </x-responsive-nav-link>
                @can('view-any', App\Models\User::class)
                @endcan
                @can('view-any', App\Models\ProductMaster::class)
                <x-responsive-nav-link href="{{ route('product-masters.index') }}">
                Product Masters
                </x-responsive-nav-link>
                @endcan
                @can('view-any', App\Models\CategoryMaster::class)
                <x-responsive-nav-link href="{{ route('category-masters.index') }}">
                Category Masters
                </x-responsive-nav-link>
                @endcan

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="shrink-0">
                    <svg class="h-10 w-10 fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>

                <div class="ml-3">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>