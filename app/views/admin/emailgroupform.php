<form
    action="/admin/api/saveform"
    method="post"
    enctype="multipart/form-data"
    data-ajax
    id="emailgroupform"
>
    <input type="hidden" name="edit"             value="<?= (int)$id ?>">
    <input type="hidden" name="table"            value="email_groups">
    <input type="hidden" name="idfield"          value="id">
    <input type="hidden" name="item_word"        value="Email Group">
    <input type="hidden" name="has_active_field" value="0">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">
            <?= $id ? htmlspecialchars($groupName, ENT_QUOTES) : 'New Email Group' ?>
        </legend>

        <?php /* Main column */ ?>
        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="group_name" class="text-sm font-medium text-gray-700">
                    Group Name
                </label>
                <input
                    id="group_name"
                    name="group_name"
                    value="<?= htmlspecialchars($groupName, ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

        </div>

        <?php /* Sidebar */ ?>
        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">

            <div class="flex flex-col space-y-2 min-w-0">
                <?php actionButtons([
                    'module'  => 'emailgroups',
                    'id'      => $id,
                    'targets' => [
                        'save'    => 'emailgroupform',
                        'back'    => 'emailgrouplist',
                        'new'     => 'emailgroupform',
                        'refresh' => 'emailgroupform',
                        'delete'  => 'emailgrouplist',
                    ],
                ]); ?>
            </div>

            <?php if ($id): ?>
            <div class="border p-3 rounded-sm">
                <p class="text-xs text-gray-500 mb-2">Manage members in this group</p>
                <button
                    type="button"
                    data-route="emailgroupmembers"
                    data-item="<?= (int)$id ?>"
                    class="text-sm text-blue-600 hover:underline"
                >
                    View / Edit Members &rarr;
                </button>
            </div>
            <?php endif; ?>

        </div>
    </fieldset>
</form>