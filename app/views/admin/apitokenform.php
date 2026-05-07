<form action="/admin/api/saveform" method="post" data-ajax id="apitokenform">
    <input type="hidden" name="edit"      value="<?= $rec->id ?? '' ?>" />
    <input type="hidden" name="table"     value="api_tokens" />
    <input type="hidden" name="idfield"   value="id" />
    <input type="hidden" name="item_word" value="API Token" />
    <input type="hidden" name="label"     value="default" />

    <?php
        $isOld = $created_at && (time() - strtotime($created_at)) > 300;
    ?>

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">API Token</legend>

        <div class="md:col-span-8 space-y-4 bg-white">
            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label class="text-sm font-medium text-gray-700">
                    <?= $token ? 'Current Token' : 'New Token' ?>
                </label>
                <input
                    type="<?= $isOld ? 'password' : 'text' ?>"
                    name="token"
                    id="token-value"
                    value="<?= htmlspecialchars($token, ENT_QUOTES) ?>"
                    readonly
                    class="w-full border rounded px-3 py-1.5 text-sm bg-gray-100 font-mono focus:outline-none"
                />
                <?php if ($created_at): ?>
                    <span class="text-xs text-gray-400">Generated: <?= htmlspecialchars($created_at, ENT_QUOTES) ?></span>
                <?php endif; ?>
            </div>
        </div>
        </div>
        <div class="md:col-span-4 flex flex-col gap-y-3">
            <?php
            actionButtons([
                'module'  => 'api_tokens',
                'id'      => $rec->id ?? null,
                'targets' => [
                    'save' => 'apitokenform',
                    'token' => 'apitokenform',
                    'refresh' => 'apitokenform',
                ],
            ]);
            ?>
        </div>

    </fieldset>
</form>