<form id="userform" action="/admin/api/saveform" method="post" enctype="multipart/form-data" data-ajax id="userform">
    <input type="hidden" name="edit" value="<?= $id ?? 0 ?>" />
    <input type="hidden" name="table" value="adminusers" />
    <input type="hidden" name="idfield" value="id" />
    <input type="hidden" name="item_word" value="Admin User" />

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border p-4 rounded">
        <legend class="font-semibold text-gray-700">
            <?= htmlspecialchars($username ?? 'Admin User', ENT_QUOTES) ?>
        </legend>

        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="username" class="w-full text-sm font-medium text-gray-700">Name</label>
                <input name="username" id="username" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="email" class="w-full text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="password" class="w-full text-sm font-medium text-gray-700">
                    Password <?= $id ? '(leave blank to keep current)' : '' ?>
                </label>
                <input type="password" name="password" id="password"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="password_confirm" class="w-full text-sm font-medium text-gray-700">
                    Confirm Password
                </label>
                <input type="password" name="password_confirm" id="password_confirm"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

        </div>

        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">
            <div class="flex flex-col space-y-2 min-w-0">
<?php
            actionButtons([
                'module' => 'adminusers',
                'id' => $id,
                'targets' => [
                    'save'    => 'userform',
                    'back'    => 'dashboard',
                    'refresh' => 'userform',
                ]
            ]);
?>
            </div>

        </div>
    </fieldset>
</form>