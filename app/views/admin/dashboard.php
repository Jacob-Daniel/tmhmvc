<div class="dashboard space-y-6">

    <div>
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <p class="text-sm text-gray-500">Single page admin system</p>
    </div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    <button 
        data-route="pageform"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Create Page</p>
        <p class="text-sm text-gray-500">Add a new page to the site</p>
    </button>

    <button 
        data-route="prodform"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Create Work Item</p>
        <p class="text-sm text-gray-500">Add a new project</p>
    </button>

    <button 
        data-route="gallery"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Manage Gallery</p>
        <p class="text-sm text-gray-500">Upload or delete images</p>
    </button>

    <button 
        data-route="navform"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Create Navigation Item</p>
        <p class="text-sm text-gray-500">Add a new menu link</p>
    </button>

    <button 
        data-route="catform"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Create Category</p>
        <p class="text-sm text-gray-500">Add a new category</p>
    </button>
    <button 
        data-route="userform"
        class="admin-action p-4 border rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
        <p class="font-semibold">Manage User <?= htmlspecialchars($_SESSION['admin_name'] ?? 'User') ?></p>
        <p class="text-sm text-gray-500">Edit details</p>
    </button>    

</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">
            <p class="font-semibold text-amber-700 mb-2">⚠ Caution</p>
            <p class="text-sm text-amber-800">
                Using your browser's back button will log you out. 
                Any unsaved work will be lost.
            </p>
        </div>

        <div class="border border-green-200 bg-green-50 rounded-lg p-4">
            <p class="font-semibold text-green-700 mb-2">✔ Good to know</p>
            <p class="text-sm text-green-800">
                Whilst in create new mode, each save creates a new item in the system. 
                It does not edit the current item.
            </p>
        </div>

    </div>
    <?php if(isset($_SESSION['access_grant'])) : ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if($_SESSION['access_grant']): ?>
                <div class="border border-green-200 bg-green-50 rounded-lg p-4 relative">
                    <p class="font-semibold text-green-700 mb-2">✔ Google API</p>
                    <p class="text-sm text-green-800">
                        <?= htmlspecialchars($_SESSION['access_grant_message']) ?>
                    </p>
                    <div class="absolute top-2 right-2">                        
                        <span class="relative flex size-3">
                          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex size-3 rounded-full bg-green-500"></span>
                        </span>
                    </div>
                </div>

            <?php else :?>
                <div class="border border-red-200 bg-red-50 rounded-lg p-4 relative">
                    <p class="font-semibold text-red-700 mb-2">⚠ Google API</p>
                    <p class="text-sm text-amber-800">
                        <?= htmlspecialchars($_SESSION['access_grant_message']) ?>
                    </p>
                    <div class="absolute top-2 right-2">                        
                        <span class="relative flex size-3">
                          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex size-3 rounded-full bg-red-500"></span>
                        </span>  
                    </div>              
                </div>
            <?php endif; ?>
        </div>  

    <?php unset($_SESSION['access_grant'], $_SESSION['access_grant_message']); endif; ?>

</div>