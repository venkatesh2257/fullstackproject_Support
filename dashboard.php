<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Auction Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="min-h-screen flex">

  <!-- SIDEBAR -->
  <aside class="w-72 bg-white border-r hidden md:block">
    <div class="h-full flex flex-col">
      <div class="px-6 py-6 border-b">
        <a href="#" class="flex items-center gap-3">
          <div class="w-10 h-10 bg-indigo-600 text-white rounded-md flex items-center justify-center font-bold">A</div>
          <div>
            <h4 class="font-semibold">Auction Admin</h4>
            <p class="text-xs text-gray-500">Dashboard</p>
          </div>
        </a>
      </div>
      <nav class="p-4 overflow-y-auto flex-1">
        <ul class="space-y-1">
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#overview">Overview</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#auctions">Auctions</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#bids">Bids</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#users">Users</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#sellers">Sellers</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#payments">Payments</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#categories">Categories</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#reports">Reports</a></li>
          <li><a class="block px-3 py-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600" href="#settings">Settings</a></li>
        </ul>
      </nav>
      <div class="p-4 border-t">
        <button class="w-full text-sm bg-red-50 text-red-700 px-3 py-2 rounded-md">Logout</button>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="flex-1">
    <header class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <button class="md:hidden p-2 rounded-md bg-gray-100" x-data @click="$dispatch('toggle-sidebar')">☰</button>
            <h1 class="text-lg font-semibold">Admin Dashboard</h1>
            <div class="text-sm text-gray-500">Manage auctions, users, bids and payments</div>
          </div>
          <div class="flex items-center gap-4">
            <div class="relative">
              <input placeholder="Search auctions, users..." class="w-64 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200" />
            </div>
            <button class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-md">New Auction</button>
            <div class="flex items-center gap-3">
              <img src="https://i.pravatar.cc/40" alt="admin" class="w-9 h-9 rounded-full" />
              <div class="text-right">
                <div class="text-sm font-medium">Admin</div>
                <div class="text-xs text-gray-500">superadmin@example.com</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <section id="overview" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Overview Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-4 rounded-md shadow">
          <h3 class="text-sm font-medium text-gray-500">Total Auctions</h3>
          <p class="mt-2 text-2xl font-semibold text-gray-800">125</p>
        </div>
        <div class="bg-white p-4 rounded-md shadow">
          <h3 class="text-sm font-medium text-gray-500">Active Bids</h3>
          <p class="mt-2 text-2xl font-semibold text-gray-800">87</p>
        </div>
        <div class="bg-white p-4 rounded-md shadow">
          <h3 class="text-sm font-medium text-gray-500">Users</h3>
          <p class="mt-2 text-2xl font-semibold text-gray-800">240</p>
        </div>
        <div class="bg-white p-4 rounded-md shadow">
          <h3 class="text-sm font-medium text-gray-500">Revenue</h3>
          <p class="mt-2 text-2xl font-semibold text-gray-800">$12,450</p>
        </div>
      </div>

      <!-- Auctions Table -->
      <div id="auctions" class="bg-white p-4 rounded-md shadow mb-6 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-4">Auctions</h3>
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left text-sm text-gray-500">ID</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Title</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Seller</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Starting Price</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm">1</td>
              <td class="px-4 py-2 text-sm">Luxury Watch</td>
              <td class="px-4 py-2 text-sm">John Doe</td>
              <td class="px-4 py-2 text-sm">$500</td>
              <td class="px-4 py-2 text-sm"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Active</span></td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm">2</td>
              <td class="px-4 py-2 text-sm">Antique Vase</td>
              <td class="px-4 py-2 text-sm">Jane Smith</td>
              <td class="px-4 py-2 text-sm">$1,200</td>
              <td class="px-4 py-2 text-sm"><span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Closed</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bids Table -->
      <div id="bids" class="bg-white p-4 rounded-md shadow mb-6 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-4">Recent Bids</h3>
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Bid ID</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Auction</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">User</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Amount</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Time</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm">101</td>
              <td class="px-4 py-2 text-sm">Luxury Watch</td>
              <td class="px-4 py-2 text-sm">Alice</td>
              <td class="px-4 py-2 text-sm">$550</td>
              <td class="px-4 py-2 text-sm">2025-12-02 15:30</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Users Table -->
      <div id="users" class="bg-white p-4 rounded-md shadow mb-6 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-4">Users</h3>
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left text-sm text-gray-500">ID</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Name</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Email</th>
              <th class="px-4 py-2 text-left text-sm text-gray-500">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm">1</td>
              <td class="px-4 py-2 text-sm">Alice</td>
              <td class="px-4 py-2 text-sm">alice@example.com</td>
              <td class="px-4 py-2 text-sm"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Active</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Sellers, Payments, Categories, Reports, Settings Sections can be duplicated similarly -->

    </section>

    <footer class="border-t py-4 text-center text-sm text-gray-500">
      © 2025 Auction Admin Panel
    </footer>
  </main>

</div>

</body>
</html>
