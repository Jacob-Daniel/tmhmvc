<form action="/admin/api/saveform" method="post" enctype="multipart/form-data" data-ajax id="catform">
    <input type="hidden" name="edit" value="<?= $id; ?>" />
    <input type="hidden" name="table" value="categories" />
    <input type="hidden" name="idfield" value="id" />
    <input type="hidden" name="item_word" value="Category" />
    <input type="hidden" name="has_active_field" value="1">
    <input type="hidden" id="imagepath" name="imagepath" value="<?= $imagepath ?? '' ?>"/>
    
    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-x-6 rounded p-4 border">
        <legend class="font-semibold text-gray-700">
            <?= $title ? htmlspecialchars($title, ENT_QUOTES) : 'New Category' ?>
        </legend>
        <div class="md:col-span-8 space-y-4">
            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="title" class="w-full text-sm font-medium text-gray-700">
                    Title
                </label>
                <input name="title" id="title" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>" class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="slug" class="w-full text-sm font-medium text-gray-700">
                    Slug: SEO-friendly version of the category title. Used in the category URL.
                </label>
                <input name="slug" id="slug" value="<?= htmlspecialchars($slug, ENT_QUOTES) ?>" class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="content" class="w-full text-sm font-medium text-gray-700 pt-2">
                    Content
                </label>
                <textarea id="content" name="content" class="mce-full w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200" rows="12"><?= trim(htmlspecialchars(stripslashes($content), ENT_QUOTES)) ?>
                </textarea>
            </div>
        </div>

        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">
            <div class="flex flex-col space-y-2 min-w-0">
<?php
                actionButtons([
                    'module' => 'categories',
                    'id' => $id,
                    'targets' => [
                        'save'    => 'catform',
                        'back'    => 'catlist',
                        'new'     => 'catform',
                        'refresh' => 'catform',
                        'delete'  => 'catlist',
                    ]
                ]);
?>
            </div>

            <div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
                <label for="parent_id">Parent</label>
                <select id="parent_id" name="parent_id" class="widesel">
                    <option value="">Select...</option>
                    <option value="0" <?= !$parent ? 'selected' : '' ?>>None (Top level)</option>
<?php
                    while ($pcat = $pcats->fetch_object()) {
                        $pcatId = (int)$pcat->id;
                        $selected = ($pcatId === $parent) ? ' selected' : '';
                        $selcat = ($pcatId === $parent) ? $pcatId : '';
                        
                        echo "<option value=\"{$pcatId}\"{$selected}>" . htmlspecialchars(stripslashes($pcat->slug ?? ''), ENT_QUOTES) . "</option>\n";
                        
                        getCatChildren($pcatId, 0, $selcat);
                    }
                    
                    $pcats->data_seek(0);
?>
                </select>
            </div>

            <div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
                <label for="sequence">Sequence</label>
                <input type="number" name="sequence" id="sequence" value="<?= (int)$sequence ?>">
            </div>

            <div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
                <label for="active">Active</label>
                <input type="checkbox" id="active" name="active" value="1" <?= !empty($active) ? 'checked' : '' ?>>
            </div>
            <div>
                <label for="metad">Meta Description SEO</label>
                <textarea id="metad" name="metad" class="border border-gray-300"><?= htmlspecialchars($metad, ENT_QUOTES) ?></textarea>
            </div>

            <div>
                <label for="metak">Meta Keywords SEO</label>
                <textarea id="metak" name="metak" class="border border-gray-300"><?= htmlspecialchars($metak, ENT_QUOTES) ?></textarea>
            </div>
        </div>
    </fieldset>
</form>