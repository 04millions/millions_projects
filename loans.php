<!-- filepath: c:\wamp64\www\millions_project\loans.php -->
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
    <title>Your Loans</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-gray-100">
    <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
        <span class="font-semibold text-lg">Loans Overview</span>
        <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </header>
    <main class="max-w-4xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">💰 Total Loans Limit</h2>
        <div class="bg-blue-600 text-white rounded-lg p-6 shadow mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="text-lg font-bold mb-1">Current Limit</div>
                    <div class="text-2xl mb-2">KES 5,000</div>
                    <div class="w-full bg-blue-300 rounded-full h-2 mb-2">
                        <div class="bg-blue-900 h-2 rounded-full" style="width:50%"></div>
                    </div>
                    <div class="text-sm">Available: <span class="font-semibold">KES 5,000</span></div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="#request-limit" class="inline-block bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded transition">Request Limit Increase</a>
                </div>
            </div>
        </div>

        <h3 class="text-xl font-bold mb-4">Your Loans</h3>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <table class="min-w-full text-left">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Loan ID</th>
                        <th class="py-2 px-4 border-b">Amount</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 px-4 border-b">LN-001</td>
                        <td class="py-2 px-4 border-b">KES 30,000</td>
                        <td class="py-2 px-4 border-b text-green-600">Approved</td>
                        <td class="py-2 px-4 border-b">2025-04-10</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b">LN-002</td>
                        <td class="py-2 px-4 border-b">KES 30,000</td>
                        <td class="py-2 px-4 border-b text-yellow-600">Pending</td>
                        <td class="py-2 px-4 border-b">2025-05-01</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b">LN-003</td>
                        <td class="py-2 px-4 border-b">KES 60,000</td>
                        <td class="py-2 px-4 border-b text-red-600">Overdue</td>
                        <td class="py-2 px-4 border-b">2025-03-15</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <section id="request-limit" class="bg-blue-50 dark:bg-blue-900 rounded-lg shadow p-6">
            <h4 class="text-lg font-bold mb-2">Request Limit Increase</h4>
            <form method="POST" action="">
                <label for="new-limit" class="block mb-1 font-semibold">New Limit Amount (KES)</label>
                <input type="number" id="new-limit" name="new-limit" min="120001" required class="w-full px-3 py-2 border rounded mb-3 dark:bg-gray-700 dark:border-gray-600">
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">Submit Request</button>
            </form>
        </section>
    </main>
</body>
</html>