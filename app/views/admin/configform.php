<form action="/admin/api/saveform" method="post" enctype="multipart/form-data" data-ajax id="configform" class="max-w-7xl mx-auto">
	<input type="hidden" name="id" id="id" value="<?= $config->id ?>">
	<input type="hidden" name="table" value="config">
	<input type="hidden" name="item_word" value="Settings">
	<input type="hidden" name="imagepath" id="imagepath" value="<?= $config->imagepath ?? '' ?>">
	<input type="hidden" name="idfield" id="idfield" value="id">
	<input type="hidden" name="edit" value="<?= $config->id ?>">
	
	<fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">	
		    <legend class="font-semibold text-gray-700">
		        <?= htmlspecialchars($title, ENT_QUOTES) ?>
		    </legend>
		<div class="md:col-span-8 space-y-4 bg-white">
				<div id="message" class="message mb-4 hidden">
					<pre id="r" class="text-sm"></pre>
				</div>

				<div class="space-y-4 mb-6">
					<div>
						<label for="comp_name" class="block text-sm font-medium text-gray-700 mb-1">Heading</label>
						<input type="text" name="comp_name" id="comp_name" value="<?= $config->comp_name ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>

					<div>
						<label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
						<input type="text" name="site_tagline" id="site_tagline" value="<?= $config->site_tagline ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>

					<div>
						<label for="domain" class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
						<input type="text" name="domain" id="domain" value="<?= $config->domain ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>
				</div>

				<?php if ($_SESSION['admin_user_id'] == 1): ?>
				<div class="border-t pt-6 mb-6">
					<h3 class="text-lg font-semibold mb-4 text-gray-900">Email</h3>
					<div class="space-y-4">
						<div>
							<label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
							<input autocomplete="nope" type="text" name="site_email" id="site_email" value="<?= $config->site_email ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>

						<div>
							<label for="email_host" class="block text-sm font-medium text-gray-700 mb-1">Email Password</label>

							<input type="text" name="email_password" placeholder="Leave blank to keep current password" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>

						<div>
							<label for="email_username" class="block text-sm font-medium text-gray-700 mb-1">Email Username</label>
							<input type="text" name="email_username" id="email_username" value="<?= $config->email_username ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>

						<div>
							<label for="email_host" class="block text-sm font-medium text-gray-700 mb-1">Email Host</label>
							<input type="text" name="email_host" id="email_host" value="<?= $config->email_host ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>

						<div>
							<label for="email_port" class="block text-sm font-medium text-gray-700 mb-1">Email Port</label>
							<input type="text" name="email_port" id="email_port" value="<?= $config->email_port ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>
					</div>
				</div>
				<?php endif; ?>

		</div>

		<div class="md:col-span-4 flex flex-col gap-y-0 min-w-0">

			<div class="flex flex-col space-y-2 min-w-0">
<?php
				actionButtons([
				    'module' => 'page',
				    'id' => $config->id,
				    'targets' => [
				        'save'    => 'configform',
				        'refresh' => 'configform',
				    ]
				]);
?>
			</div>

				<div class="space-y-4 border-t pt-6 mt-6">
					<h3 class="text-lg font-semibold mb-4 text-gray-900">SEO</h3>
					<div>
						<label for="metak" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
						<textarea id="metak" name="metak" rows="3" class="w-full px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= $config->metak ?? '' ?></textarea>
					</div>

					<div>
						<label for="metad" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
						<textarea id="metad" name="metad" rows="3" class="w-full px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= $config->metad ?? '' ?></textarea>
					</div>
				</div>
				<div class="pt-6 space-y-4">
					<h3 class="text-lg font-semibold mb-4 text-gray-900">Social</h3>

					<div>
						<label for="fb_url" class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
						<input type="text" name="fb_url" id="fb_url" value="<?= $config->fb_url ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>

					<div>
						<label for="inst_url" class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
						<input type="text" name="inst_url" id="inst_url" value="<?= $config->inst_url ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>

					<div>
						<label for="tw_url" class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
						<input type="text" name="tw_url" id="tw_url" value="<?= $config->tw_url ?? '' ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
					</div>
				</div>
			</div>
	</div>
			</fieldset>
</form>
