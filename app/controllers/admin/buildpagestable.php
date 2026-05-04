<section class="text-body">
  <fieldset class="bg-white overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-left border-collapse text-zinc-900">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3 text-center">Sequence</th>
            <th class="px-4 py-3 text-center">Active</th>
            <th class="px-4 py-3 text-center">View/Edit</th>
            <th class="px-4 py-3 text-center">Delete</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
<?php
        if($records->num_rows)
        {
          while($page = $records->fetch_object())
          {
?>
          <tr class="hover:bg-gray-50 transition text-zinc-900">
            <td class="px-4 py-3">
              <?= $page->id;?>
            </td>

            <td class="px-4 py-3">
              <?php createEditField('pages','title','pn',$page->id,stripslashes($page->title ?? 0),'200px');?>
            </td>

            <td class="px-4 py-3 text-center">
              <?php createEditField('pages','sequence','pr',$page->id,$page->sequence ?? 0,'40px');?>
              <input hidden type="text" name="sequence[]" value="<?= $page->sequence; ?>">
            </td>

            <td class="px-4 py-3 text-center">
              <span class="cursor-pointer font-medium text-blue-600 hover:text-blue-800"
                onclick="flipField('pages','active',<?= $page->id;?>)"
                id="active_<?= $page->id;?>">
                <?= $page->active ? 'Y' : 'N'; ?>
              </span>
            </td>

            <td class="px-4 py-3 text-center">
<?php
                actionButtons([
                    'module' => 'pages',
                    'id' => $page->id,
                    'targets' => [
                        'edit'  => 'pageform',
                    ]
                ]);
?>
            </td>

            <td class="px-4 py-3 text-center">
<?php
                actionButtons([
                    'module' => 'pages',
                    'id' => $page->id,
                    'targets' => [
                        'delete'  => 'pagelist',
                    ]
                ]);
?>
            </td>
          </tr>

<?php
        $children = getList('pages',' where pageparent ='.$page->id.' order by sequence');
        if ($children->num_rows != 0)
        {
          while ($child = $children->fetch_object())
          {
?>
          <tr class="bg-gray-50 hover:bg-gray-100 transition">
            <td class="px-4 py-3">
              <?= $child->id;?>
            </td>

            <td class="px-4 py-3 pl-10 text-gray-700">
              – <a class="hover:text-blue-600"
                href="javascript:void(0)"
                onclick="loadContent('pageform',<?= $child->id;?>)">
                <?= stripslashes($child->title);?>
              </a>
            </td>

            <td class="px-4 py-3 text-center">
              <?php createEditField('pages','sequence','pr',$child->id,$child->sequence ?? 0,'40px');?>
              <input hidden type="text" name="sequence[]" value="<?= $child->sequence;?>">
            </td>

            <td class="px-4 py-3 text-center">
              <span class="cursor-pointer font-medium text-blue-600 hover:text-blue-800"
                onclick="flipField('pages','a',<?= $child->id;?>)"
                id="active_<?= $child->id;?>">
                <?= $child->active ? 'Y' : 'N'; ?>
              </span>
            </td>

            <td class="px-4 py-3 text-center">
              <button class="text-gray-600 hover:text-blue-600 transition"
                onclick="loadContent('pageform',<?= $child->id;?>)">
                <i class="fas fa-edit"></i>
              </button>
            </td>

            <td class="px-4 py-3 text-center">
              <button class="text-gray-600 hover:text-red-600 transition"
                onclick="loadContent('pagelist',0,<?= $child->id;?>)">
                <i class="fas fa-times"></i>
              </button>
            </td>
          </tr>
<?php
          }
        }
      }
    }
    else {
        echo '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">None found</td></tr>';
    }
?>
        </tbody>
      </table>
    </div>

  </fieldset>
</section>