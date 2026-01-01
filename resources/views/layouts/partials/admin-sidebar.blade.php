<aside class="w-64 bg-gray-900 text-white min-h-screen">
    <div class="p-6 text-xl font-bold border-b border-gray-700">
        💼 Admin Panel
    </div>

    <nav class="mt-4">
        <a href="/admin/dashboard"
           class="block px-6 py-3 hover:bg-gray-800 {{ request()->is('admin/dashboard') ? 'bg-gray-800' : '' }}">
            📊 Dashboard
        </a>

        <a href="/admin/categories"
           class="block px-6 py-3 hover:bg-gray-800 {{ request()->is('admin/categories*') ? 'bg-gray-800' : '' }}">
            🗂 Categories
        </a>

        <a href="/admin/sources"
           class="block px-6 py-3 hover:bg-gray-800 {{ request()->is('admin/sources*') ? 'bg-gray-800' : '' }}">
            💳 Sources
        </a>

        <a href="/admin/reports"
           class="block px-6 py-3 hover:bg-gray-800 {{ request()->is('admin/reports*') ? 'bg-gray-800' : '' }}">
            📄 Reports
        </a>
    </nav>
</aside>
