<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700">
                    Webinar Solution
                </a>
            </div>
            
            <!-- Navigation Links -->
            <div class="flex items-center space-x-6">
                @auth
                    <a href="{{ route('webcast') }}" class="text-gray-700 hover:text-blue-600">Live Webcast</a>
                    <a href="{{ route('previous-sessions') }}" class="text-gray-700 hover:text-blue-600">Previous Sessions</a>
                    
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                            Admin Panel
                        </a>
                    @endif
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                            <span>{{ auth()->user()->full_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>