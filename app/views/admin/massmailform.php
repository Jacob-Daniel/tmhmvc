<?php
// ── Google token status banner ──────────────────────────────────────────────
if ($tokenStatus === 'missing'): ?>
    <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
        <strong>Initial Google Access Required.</strong>
        <a href="<?= htmlspecialchars($authUrl, ENT_QUOTES) ?>"
           class="ml-2 underline font-medium">Grant Gmail Access →</a>
    </div>

<?php elseif ($tokenStatus === 'expiring'): ?>
    <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <strong>Token needs refresh.</strong>
        <button
            type="button"
            class="ml-2 underline font-medium"
            onclick="loadContent('massmailform', 0, 0, 0, 'refresh')"
        >Re-authenticate</button>
    </div>

<?php else: ?>
    <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
        ✓ Google token valid &mdash; expires
        <span class="font-mono"><?= date('Y-m-d H:i', $tokenExpires) ?></span>
    </div>
<?php endif; ?>

<?php
// ── Quota / pending info ─────────────────────────────────────────────────────
if ($remainingToSend > 0): ?>
    <div class="mb-2 rounded border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
        You can still send <strong><?= (int)$remainingToSend ?></strong> emails today.
    </div>
<?php else: ?>
    <div class="mb-2 rounded border border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-800">
        Daily Google Mail limit reached. No more emails can be sent today.
    </div>
<?php endif; ?>

<?php if ($totalPending > 0): ?>
    <div class="mb-4 rounded border border-blue-200 bg-blue-50 px-4 py-2 text-sm text-blue-800">
        <strong><?= (int)$totalPending ?></strong> email(s) pending transmission:
        <?php while ($pt = $pendingTemplates->fetch_object()): ?>
            <span class="ml-1 font-mono">
                [#<?= (int)$pt->id ?> <?= htmlspecialchars($pt->em_name, ENT_QUOTES) ?>]
            </span>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<form
    action="/admin/api/sendmassmail"
    method="post"
    data-ajax
    id="massmailform"
>
    <input type="hidden" name="edit"      value="<?= (int)$id ?>">
    <input type="hidden" name="item_word" value="Mass Mail">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">
            <?= $id ? 'Edit Send' : 'New Mass Mail' ?>
        </legend>

        <?php /* Main column */ ?>
        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="m_subj" class="text-sm font-medium text-gray-700">Subject</label>
                <input
                    id="m_subj"
                    name="m_subj"
                    value="<?= htmlspecialchars($subject, ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="m_from" class="text-sm font-medium text-gray-700">From address</label>
                <input
                    id="m_from"
                    name="m_from"
                    type="email"
                    value="<?= htmlspecialchars($from, ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="email_id" class="text-sm font-medium text-gray-700">Email Template</label>
                <select
                    id="email_id"
                    name="email_id"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">— Select template —</option>
                    <?php while ($t = $emailTemplates->fetch_object()): ?>
                        <option value="<?= (int)$t->id ?>" <?= (int)$emailId === (int)$t->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t->em_name, ENT_QUOTES) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="list_id" class="text-sm font-medium text-gray-700">Send to Group</label>
                <select
                    id="list_id"
                    name="list_id"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">— Select group —</option>
                    <?php while ($g = $groups->fetch_object()): ?>
                        <option value="<?= (int)$g->id ?>" <?= (int)$listId === (int)$g->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g->group_name, ENT_QUOTES) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="campaign" class="text-sm font-medium text-gray-700">
                    Campaign tag <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input
                    id="campaign"
                    name="campaign"
                    value="<?= htmlspecialchars($rec->campaign ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

        </div>

        <?php /* Sidebar */ ?>
        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">

            <div class="flex flex-col space-y-2 min-w-0">
                <?php actionButtons([
                    'module'  => 'massmail',
                    'id'      => $id,
                    'targets' => [
                        'save'    => 'massmailform',
                        'back'    => 'massmaillist',
                        'new'     => 'massmailform',
                        'refresh' => 'massmailform',
                    ],
                ]); ?>
            </div>

            <div class="border p-3 rounded-sm bg-amber-50 text-sm text-amber-800 space-y-1">
                <p class="font-medium">Before sending</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    <li>Select a template and a recipient group</li>
                    <li>Check the subject line</li>
                    <li>Use the Send button only when ready &mdash; this queues a real send</li>
                </ul>
            </div>

            <div class="border p-3 rounded-sm">
                <button
                    type="button"
                    id="send-mass-mail-btn"
                    class="w-full bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700 disabled:opacity-50"
                >
                    Send to Group &rarr;
                </button>
                <p id="send-status" class="mt-2 text-xs text-gray-500"></p>
            </div>

        </div>
    </fieldset>
</form>