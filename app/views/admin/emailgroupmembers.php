<?php if (!$group): ?>
    <p class="p-4 text-gray-500">No group selected.</p>
<?php return; endif; ?>

<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">
        Members &mdash; <?= htmlspecialchars($group->group_name, ENT_QUOTES) ?>
    </legend>
    <div class="flex items-center space-x-2 mb-2 p-3">
        <?php actionButtons([
            'module'  => 'emailgroupmembers',
            'id'      => 0,
            'targets' => [
                'back' => 'emailgrouplist',
                'new'  => 'emailgroupform',
            ]
        ]); ?>
    </div>
    <table class="min-w-full text-sm text-left border-collapse text-zinc-900">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">First</th>
                <th class="px-4 py-3">Last</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Edit</th>
                <th class="px-4 py-3 text-center">Remove</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if ($members && $members->num_rows): ?>
            <?php while ($m = $members->fetch_object()): ?>
            <tr class="hover:bg-gray-50 transition text-zinc-900 <?= (int)$m->unsub === 1 ? 'opacity-50' : '' ?>">
                <td class="px-4 py-3"><?= htmlspecialchars($m->email, ENT_QUOTES) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($m->fname, ENT_QUOTES) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($m->lname,  ENT_QUOTES) ?></td>
                <td class="px-4 py-3 text-center">
                    <?php if ((int)$m->unsub === 1): ?>
                        <span class="text-red-500 text-xs" title="<?= $m->unsubdate ? date('Y-m-d', $m->unsubdate) : '' ?>">
                            Unsubscribed
                        </span>
                    <?php else: ?>
                        <span class="text-green-600 text-xs">Active</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emailgroupmembers',
                        'id'      => $m->id,
                        'targets' => [
                            'edit' => 'emailgroupform',
                        ]
                    ]); ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emailgroupmembers',
                        'id'      => $m->id,
                        'targets' => [
                            'delete' => 'emailgrouplist',
                        ]
                    ]); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                    No members in this group yet.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</fieldset>