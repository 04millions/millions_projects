<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
$username = $_SESSION["fullname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .sidebar {
            transition: transform 0.3s ease;
        }
        .sidebar-collapsed {
            transform: translateX(-100%);
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-gray-100">

    <!-- Top Nav -->
    <header class="fixed top-0 w-full bg-white dark:bg-gray-800 shadow z-50 flex items-center justify-between px-4 py-3">
        <!-- Left: Hamburger and Back -->
        <div class="flex items-center space-x-4">
            <button id="toggleSidebar" class="text-2xl focus:outline-none">☰</button>
            <a href="javascript:history.back()" class="text-lg">🔙</a>
            <span class="font-semibold">Welcome, <?php echo htmlspecialchars($username); ?></span>
        </div>

        <!-- Right: Scrollable Navigation -->
        <nav class="overflow-x-auto whitespace-nowrap">
            <ul class="flex space-x-4">
                <li><a href="#home" class="hover:underline">Home</a></li>
                <li><a href="profile.php" class="hover:underline">Profile</a></li>
                <li><a href="#settings" class="hover:underline">Settings</a></li>
                <li><form method="POST" action="logout.php"><button type="submit" class="hover:underline text-red-500">Logout</button></form></li>
                <li>
                    <button id="darkModeToggle" class="hover:underline">🌓 Toggle Dark</button>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed top-16 left-0 w-64 h-full bg-white dark:bg-gray-800 shadow-lg p-4 overflow-y-auto">
        <ul class="space-y-4">
            <li><a href="#home" class="block hover:underline">🏠 Home</a></li>
            <li><a href="#profile" class="block hover:underline">👤 Profile</a></li>
            <li><a href="#settings" class="block hover:underline">⚙️ Settings</a></li>
            <li><a href="#analytics" class="block hover:underline">📊 Analytics</a></li>
            <li><a href="#messages" class="block hover:underline">💬 Messages</a></li>
            <li><a href="#api" class="block hover:underline">🔌 API</a></li>
            <li><a href="#help" class="block hover:underline">❓ Help</a></li>
            <li><a href="#feedback" class="block hover:underline">📝 Feedback</a></li>
            <li><a href="#terms" class="block hover:underline">📜 Terms</a></li>
            <li><a href="#updates" class="block hover:underline">🔄 Updates</a></li
            <li><a href="logout.php" class="block hover:underline text-red-500">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 pt-20 p-6 transition-all duration-300 min-h-screen" id="mainContent" style="max-width:100vw;">
        <section id="home" class="mb-10">
            <h2 class="text-2xl font-bold mb-2">🏠 Home</h2>
            <p>Welcome to your dashboard homepage.</p>
        </section>

        <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4 mb-10">
        <a href="#loan-application" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded shadow transition">Apply for Loan</a>
        <a href="#profile" class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-3 rounded shadow transition">Update Profile</a>
        <a href="#messages" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded shadow transition">View Messages</a>
        <a href="#support" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded shadow transition">Contact Support</a>
    </div>

        <!-- Dashboard Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <a href="loans.php" class="block">
                <div class="bg-blue-600 text-white rounded-lg p-6 shadow flex flex-col items-center hover:bg-blue-700 transition">
                    <span class="text-3xl mb-2">💰</span>
                    <div class="text-lg font-bold">Total Loans Limit</div>
                </div>
            </a>
            <a href="#approved" class="block">
                <div class="bg-green-600 text-white rounded-lg p-6 shadow flex flex-col items-center hover:bg-green-700 transition">
                    <span class="text-3xl mb-2">✅</span>
                    <div class="text-lg font-bold">Approved Loans</div>
                </div>
            </a>
            <a href="#pending" class="block">
                <div class="bg-gray-500 text-white rounded-lg p-6 shadow flex flex-col items-center hover:bg-gray-600 transition">
                    <span class="text-3xl mb-2">⏳</span>
                    <div class="text-lg font-bold">Pending Applications</div>
                </div>
            </a>
            <a href="#overdue" class="block">
                <div class="bg-red-500 text-white rounded-lg p-6 shadow flex flex-col items-center hover:bg-red-700 transition">
                    <span class="text-3xl mb-2">⚠️</span>
                    <div class="text-lg font-bold">Overdue Loans</div>
                </div>
            </a>
        </div>


        <!-- Recent Activity Table -->
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-10">
            <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Date</th>
                            <th class="py-2 px-4 border-b">Activity</th>
                            <th class="py-2 px-4 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b">2025-05-14</td>
                            <td class="py-2 px-4 border-b">Loan Application</td>
                            <td class="py-2 px-4 border-b text-green-600">Approved</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">2025-05-10</td>
                            <td class="py-2 px-4 border-b">Profile Updated</td>
                            <td class="py-2 px-4 border-b text-blue-600">Success</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">2025-05-01</td>
                            <td class="py-2 px-4 border-b">Loan Repayment</td>
                            <td class="py-2 px-4 border-b text-yellow-600">Pending</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Announcements -->
        <section class="bg-blue-50 dark:bg-blue-900 rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">📢 Announcements</h3>
            <ul class="list-disc pl-6 space-y-2">
                <li>System maintenance scheduled for May 20, 2025.</li>
                <li>New loan products coming soon!</li>
                <li>Remember to update your profile for faster approvals.</li>
            </ul>
        </section>
    </main>

    <script>
        // Toggle sidebar visibility
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');
        let sidebarOpen = true;

        toggleSidebar.addEventListener('click', () => {
            sidebarOpen = !sidebarOpen;
            if (!sidebarOpen) {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('ml-64');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.add('ml-64');
            }
        });

        // Toggle dark mode
        document.getElementById('darkModeToggle').addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
        });
    </script>

</body>
</html>
