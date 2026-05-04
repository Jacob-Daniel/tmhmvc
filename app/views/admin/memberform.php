<form
    action="/admin/api/saveform"
    method="post"
    enctype="multipart/form-data"
    data-ajax
    id="emailgroupmemberform"
>
    <input type="hidden" name="edit"      value="<?= (int)$id ?>">
    <input type="hidden" name="table"     value="email_group_members">
    <input type="hidden" name="idfield"   value="id">
    <input type="hidden" name="item_word" value="Member">
    <input type="hidden" name="group_id"  value="<?= (int)$groupId ?>">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">
            <?= $id ? 'Edit Member' : 'New Member' ?>
            <?php if ($group): ?>
                &mdash; <span class="font-normal"><?= htmlspecialchars($group->group_name, ENT_QUOTES) ?></span>
            <?php endif; ?>
        </legend>

        <?php /* Main column */ ?>
        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-y-1">
                    <label for="fname" class="text-sm font-medium text-gray-700">First Name</label>
                    <input
                        id="fname"
                        name="fname"
                        value="<?= htmlspecialchars($fname, ENT_QUOTES) ?>"
                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                </div>
                <div class="flex flex-col gap-y-1">
                    <label for="lname" class="text-sm font-medium text-gray-700">Last Name</label>
                    <input
                        id="lname"
                        name="lname"
                        value="<?= htmlspecialchars($lname, ENT_QUOTES) ?>"
                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                </div>
            </div>

            <?php if ($id && isset($rec->unsub)): ?>
            <div class="flex flex-col gap-y-1 text-sm text-gray-500">
                <span>
                    Unsubscribed:
                    <?php if ((int)$rec->unsub === 1): ?>
                        <strong class="text-red-500">Yes</strong>
                        <?= $rec->unsubdate ? '(' . date('Y-m-d', $rec->unsubdate) . ')' : '' ?>
                    <?php else: ?>
                        <strong class="text-green-600">No</strong>
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

        </div>

        <?php /* Sidebar */ ?>
        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">
            <div class="flex flex-col space-y-2 min-w-0">
                <?php actionButtons([
                    'module'  => 'emailgroupmembers',
                    'id'      => $id,
                    'targets' => [
                        'save'    => 'emailgroupmemberform',
                        'back'    => 'emailgroupmembers',
                        'new'     => 'emailgroupmemberform',
                        'refresh' => 'emailgroupmemberform',
                        'delete'  => 'emailgroupmembers',
                    ],
                    'condition' => $groupId,
                ]); ?>
            </div>
        </div>

    </fieldset>
</form>