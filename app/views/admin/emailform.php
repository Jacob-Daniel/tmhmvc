<form
    action="/admin/api/saveform"
    method="post"
    enctype="multipart/form-data"
    data-ajax
    id="emailform"
>
    <input type="hidden" name="edit"           value="<?= (int)$id ?>">
    <input type="hidden" name="table"          value="emails">
    <input type="hidden" name="idfield"        value="id">
    <input type="hidden" name="item_word"      value="Email">
    <input type="hidden" name="has_active_field" value="1">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">
            <?= htmlspecialchars($title, ENT_QUOTES) ?>
        </legend>

        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="em_name" class="text-sm font-medium text-gray-700">Name</label>
                <input
                    id="em_name"
                    name="em_name"
                    value="<?= htmlspecialchars($rec->em_name ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="em_body" class="text-sm font-medium text-gray-700">Body</label>
                <textarea
                    id="em_body"
                    name="em_body"
                    class="mce-full w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    rows="14"
                ><?= isset($rec->em_body) ? trim(htmlspecialchars(stripslashes($rec->em_body), ENT_QUOTES)) : '' ?></textarea>
            </div>

        </div>

        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">

            <div class="flex flex-col space-y-2 min-w-0">
                <?php actionButtons([
                    'module'  => 'emails',
                    'id'      => $id,
                    'targets' => [
                        'save'    => 'emailform',
                        'back'    => 'emaillist',
                        'new'     => 'emailform',
                        'refresh' => 'emailform',
                        'delete'  => 'emaillist',
                    ],
                ]); ?>
            </div>

            <div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
                <label for="active" class="text-sm text-gray-700">Active</label>
                <input
                    type="checkbox"
                    id="active"
                    name="active"
                    value="1"
                    <?= !empty($active) ? 'checked' : '' ?>
                >
            </div>

            <div class="flex flex-col gap-y-3 border p-3 rounded-sm text-sm">

                <div class="flex flex-col gap-y-1">
                    <label for="plch" class="font-medium text-gray-700">General placeholders</label>
                    <select id="plch" name="plch" class="border rounded px-2 py-1 text-sm" onchange="insertPlaceholder(this)">
                        <option value="">Select placeholder</option>
                        <option value="{COMPNAME}">Company Name</option>
                    </select>
                </div>

                <div class="flex flex-col gap-y-1">
                    <label for="plche" class="font-medium text-gray-700">Event placeholders</label>
                    <select id="plche" name="plche" class="border rounded px-2 py-1 text-sm" onchange="insertPlaceholder(this)">
                        <option value="">Select placeholder</option>
                        <?php while ($e = $events->fetch_object()): ?>
                            <option value="{EVENT_<?= (int)$e->id ?>_<?= (int)$e->cat_id ?>}">
                                <?= htmlspecialchars(stripslashes($e->title), ENT_QUOTES) ?>
                                <?= date('Y-m-d', $e->start_date) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="flex flex-col gap-y-1">
                    <label for="plchp" class="font-medium text-gray-700">Page placeholders</label>
                    <select id="plchp" name="plchp" class="border rounded px-2 py-1 text-sm" onchange="insertPlaceholder(this)">
                        <option value="">Select placeholder</option>
                        <?php while ($p = $pages->fetch_object()): ?>
                            <option value="{PAGE_<?= (int)$p->id ?>}">
                                <?= htmlspecialchars(stripslashes($p->slug), ENT_QUOTES) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

        </div>
    </fieldset>
</form>