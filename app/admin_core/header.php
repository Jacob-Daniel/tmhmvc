<?php require_once __DIR__ . '/../views/admin/components/messageModal.php';?>	
<?php require_once __DIR__ . '/../views/admin/components/imageModal.php';?>   
<header class="w-full flex items-center justify-between py-4 border-b">

<?php if (isset($_SESSION['admin_name'], $_SESSION['admin_id'])) : ?>

    <!-- Left -->
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-sans">Admin Panel</h1>
        <div id="loading" class="text-sm text-gray-400"></div>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-6 text-sm">

        <!-- Logged in user -->
        <button
            data-route="userform"
            data-item="<?= htmlspecialchars($_SESSION['admin_user_id']) ?>"
            class="hover:underline text-gray-700"
        >
            <?= htmlspecialchars($_SESSION['admin_name']) ?>
        </button>

        <!-- Logout -->
        <a 
            href="<?= BASE_URL; ?>/admin/login.php?logout=1"
            class="flex items-center gap-2 text-red-600 hover:text-red-700"
        >
            <i class="fa fa-sign-out"></i>
            <span>Log Out</span>
        </a>

    </div>

<?php endif; ?>

</header>