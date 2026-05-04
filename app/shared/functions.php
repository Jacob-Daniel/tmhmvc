<?php
$stats = [
	"Invalid 0",
	"New Order",
	"Quote Issued",
	"Quote Accepted",
	"Ordered from Supplier",
	"Dispatched",
	"Paid",
];

function insertRecord($table, $extra = "")
{
	global $db;
	if ($table !== "images") {
		foreach ($_POST as $key => $val) {
			if ($key == "clientsel") {
				continue;
			}
			$val = $db->real_escape_string(trim($val));
			if ($key == "go") {
				continue;
			}
			if ($key == "form") {
				continue;
			}
			if ($key == "logo") {
				continue;
			}
			if ($key == "edit") {
				continue;
			}
			if ($key == "subcat") {
				continue;
			}
			if ($key == "submitted") {
				$val = strtotime($val);
			}
			if (is_array($key)) {
				continue;
			}
			$fldsarr[] = "\"$val\"";
			$keys[] = "`" . $key . "`";
		}
		$fldsarr = implode(",", $fldsarr);
		$keys = implode(",", $keys);
		$sql = "insert into $table ($keys) values($fldsarr)";
		if (!($res = $db->query($sql))) {
			die($db->error . "--" . $sql);
		}
	}
	$id = $db->insert_id;
	if ($table == "images") {
		foreach ($extra as $img) {
			$sql = sprintf("insert into images (id,imagepath) values (%d,'%s')", null, $img);
			if (!($res = $db->query($sql))) {
				die($db->error . "--" . $sql);
			}
		}
	}
	return $id;
}

function updateRecord($table, $idfield, $editid, $extra = "")
{
	global $db;
	if (isset($_POST["submitted"])) {
		$_POST["submitted"] = strtotime($_POST["submitted"]);
	}
	foreach ($_POST as $key => $val) {
		$val = $db->real_escape_string(trim($val));
		if ($key == "submit") {
			continue;
		}
		if ($key == "go") {
			continue;
		}
		if ($key == "form") {
			continue;
		}
		if ($key == "upload_button") {
			continue;
		}
		if ($key == "page") {
			continue;
		}
		if ($key == "edit") {
			continue;
		}
		if ($key == "item") {
			continue;
		}
		if ($key == "created") {
			continue;
		}
		if ($key == "subcat") {
			continue;
		}
		if ($key == "img_id") {
			continue;
		}
		if (is_array($key)) {
			continue;
		}
		$fldsarr[] = "`" . $key . "`=\"$val\"";
	}
	$fldsarr = implode(",", $fldsarr);
	$sql = "update $table set $fldsarr where $idfield=$editid";
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
}
function deleteItem($editid)
{
	global $db;

	$sql = "delete from newsitems where id=$editid";
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
}
function escapeStr($str)
{
	global $db;
	return $db->real_escape_string($str);
}
function checkLogin()
{
	global $db;

	$sql = sprintf(
		"select * from members where email like '%s%%' and password = '%s'",
		$_POST["email"],
		$_POST["password"],
	);
	//echo $sql;
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	if (!$res->num_rows) {
		$msg = "Login failed - Please try again";
	} else {
		$msg = "";
		$row = $res->fetch_row();
		$_SESSION["userid"] = $row[0];
		$_SESSION["username"] = $row[1] . " " . $row[2];
		$_SESSION["active"] = $row[61];
		$_SESSION["admin"] = $row[72];
	}
	return $msg;
}

function incrementViews($table, $id)
{
	global $db;
	$sql = sprintf("update %s set views = views+1 where id=%d", $table, $id);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
}
function getList($table, $cond = "")
{
	global $db;

	$sql = sprintf("select * from %s %s", $table, $cond);
	// echo $sql;
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}

	return $res;
}

function getListWhere(string $table, string $whereSql, string $types, array $params)
{
    global $db;

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        throw new RuntimeException('Invalid table name');
    }

    $sql = "SELECT * FROM {$table} {$whereSql}";
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException($db->error);
    }

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException($stmt->error);
    }

    return $stmt->get_result();
}


function getRow(
    string $table,
    array $fields,
    string $where,
    array $params = []
): ?array {
    $rows = getRows($table, $fields, $where, $params);
    return $rows[0] ?? null;
}


function getRows(
    string $table,
    array $fields,
    string $where = '',
    array $params = []
): array {
    global $db;

    $fieldList = implode(', ', $fields);
    $sql = "SELECT $fieldList FROM $table";

    if ($where) {
        $sql .= " WHERE $where";
    }

    $stmt = $db->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getGrandchildCategories(int $parentId): array
{
    global $db;

    $sql = "
        SELECT title, image_id,id,slug
        FROM categories
        WHERE parent_id IN (
            SELECT id
            FROM categories
            WHERE id = ?
        )
        ORDER BY sequence
    ";

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('i', $parentId);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


function getDistinctList($table, $field, $cond = "")
{
	global $db;

	$sql = sprintf("select distinct %s from %s %s", $field, $table, $cond);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}

	return $res;
}

function getRecord(string $table, string $field, $value, string $extraCond = "")
{
	global $db;

	$sql = "SELECT * FROM `$table` WHERE `$field` = ? $extraCond LIMIT 1";

	$stmt = $db->prepare($sql);
	if (!$stmt) {
		die("Prepare failed: " . $db->error . " -- SQL: $sql");
	}

	if (is_int($value)) {
		$stmt->bind_param("i", $value);
	} else {
		$stmt->bind_param("s", $value);
	}

	$stmt->execute();
	$res = $stmt->get_result();
	$rec = $res->fetch_object();

	$stmt->close();
	return $rec ?: null;
}

function getValue($table,$condfield,$condvalue,$fld,$cond='')
{
	global $db;
	$info = explode("=",$cond);
	$sqlwhere = sprintf("where %s = '%s' %s",
        $condfield,
        $db->real_escape_string($condvalue),
        $db->real_escape_string($cond)
	                );
	$sql = sprintf("select %s from %s %s",
       $fld,
       $table,
       $sqlwhere
   );
	if(!$res = $db->query($sql)) die($db->error.'--'.$sql);
	$rec = $res->fetch_object();
	if(isset($rec))
	{
		return $rec->$fld;
	}	
}

function getValues($table,$condfield,$condvalue,$flds,$cond='',$list=0)
{
	global $db;
	$info = explode("=",$cond);
	$sqlwhere = sprintf("where %s = '%s' %s",
        $condfield,
         $db->real_escape_string($condvalue),
         $db->real_escape_string($cond)
    );
	$sql = sprintf("select %s from %s %s",$flds,$table,$sqlwhere);
	$reslist=array();
	$i=0;
	if(!$res = $db->query($sql)) die($db->error.'--'.$sql);
	if($list==1)
	{
		while($rec = $res->fetch_assoc())
		{
			if(isset($rec))
			{
				foreach ($rec as $k => $v) 
				{
					$reslist[$i][$k] = $v;
				}
			$i++;	
			}			
		}
		return $reslist;
	}
	else
	{
		$rec = $res->fetch_assoc();
		if(isset($rec))
		{
			return $rec;
		}	
	}
}

function getRandomRecord($table, $idfield, $id)
{
	global $db;

	$cond = " order by rand()";
	$sql = sprintf("select * from %s where %s=%d %s", $table, $idfield, $id, $cond);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	$rec = $res->fetch_object();

	return $rec;
}
function getRandomRecord2($table)
{
	global $db;

	$sql = sprintf("select * from %s order by rand() limit 1", $table);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	$rec = $res->fetch_object();

	return $rec;
}

function timeago($time)
{
	$periods = ["second", "minute", "hour", "day", "week", "month", "year", "decade"];
	$lengths = ["60", "60", "24", "7", "4.35", "12", "10"];
	$now = time();
	$difference = $now - $time;
	$tense = "ago";
	for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if ($difference != 1) {
		$periods[$j] .= "s";
	}
	return "$difference $periods[$j] $tense";
}

function getCatlistURL($id, $catname)
{
	global $config;

	if ($config->SEO == 1) {
		$catname = str_replace(" ", "_", $catname);
		$urlend = $catname . "-" . $id;
	} else {
		$urlend = "index.php?cat=" . $id;
	}
	$url = $config->base_url . $urlend;
	return $url;
}
function getCatChildren($cat, $level, $selcat)
{
	global $db, $offsetadder, $depth;

	$sql = sprintf("select * from categories where parent_id = %d", $cat);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	while ($scat = $res->fetch_object()) {
		if ($level > 0) {
			for ($i = 0; $i < $level; $i++) {
				$offsetadder .= "&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		//$selected = ($scat->id == $selcat)?' selected="selected"':'';
		if (is_array($selcat)) {
			$selected = in_array($scat->id, $selcat) ? ' selected="selected"' : "";
		} else {
			$selected = $scat->id == $selcat ? ' selected="selected"' : "";
		}
		echo '<option value="' .
			$scat->id .
			'" ' .
			$selected .
			">" .
			$offsetadder .
			" - " .
			stripslashes($scat->slug) .
			"</option>" .
			"\n";
		getCatChildren($scat->id, $level + 1, $selcat);
		$offsetadder = "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "level " . $level;
	}
}
function getCatChildrenList($cat, $level)
{
	global $db, $count;

	$sql = sprintf("select * from categories where parent_id = %d", $cat);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	if ($res->num_rows) {
		echo '<ul style="list-style:none">';
		while ($scat = $res->fetch_object()) {
			echo '<li style="list-style:none"><a href="' .
				getCatlistURL($scat->id, $scat->catname) .
				'">' .
				stripslashes($scat->catname) .
				"</a>\n";
			getCatChildrenList($scat->id, $level + 1);
		}
		echo "</ul>";
	}
	echo "</li>";
}
function getCatChildrenTable($cat, $level, $usercats)
{
	global $db, $offsetadder, $depth, $i;

	$sql = sprintf("select * from categories where parent_id = %d", $cat);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	while ($scat = $res->fetch_object()) {
		if ($level > 0) {
			for ($i = 0; $i < $level; $i++) {
				$offsetadder .= "&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		$chkd = "";
		$bg = $i % 2 ? "#ccc" : "#fff";
		if (isset($usercats)) {
			$chkd = in_array($scat->id, $usercats) ? 'checked="checked"' : "";
		}
		echo '<tr style="background-color:' . $bg . '">';
		echo "<td>" . $offsetadder . " - " . stripslashes($scat->catname) . "</td>" . "\n";
		echo '<td><input type="checkbox" name="cataccess[]" value="' .
			$scat->id .
			'" ' .
			$chkd .
			" /></td>" .
			"\n";
		echo "</tr>\n";
		$i++;
		getCatChildrenTable($scat->id, $level + 1, $usercats);
		$offsetadder = "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
}

function setupPaging(string $object, int $itemsperpage, string $cond = ""): array
{
    global $db;
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
    
    $sql = "SELECT COUNT(*) FROM {$object} {$cond}";

    if (!($res = $db->query($sql))) {
        throw new RuntimeException($db->error . " -- " . $sql);
    }
    
    $row = $res->fetch_row();
    $cnt = (int)$row[0];
    $pages = (int)ceil($cnt / $itemsperpage);
    $offset = ($page - 1) * $itemsperpage;

    return [
        'records' => $cnt,
        'page' => $page,
        'pages' => $pages,
        'offset' => $offset
    ];
}
function draw_pager($content, $total_pages, $current_page = 1, $extra = "")
{
    if ($total_pages <= 1) return;

    if ($current_page <= 0 || $current_page > $total_pages) {
        $current_page = 1;
    }

    $btn = fn($page, $label, $title = '') =>
        sprintf(
            // '<span class="pager-btn%s" data-content="%s" data-page="%d" title="%s">%s</span>',
			'<span class="me-2 pager-btn%s" data-route="%s" data-page="%d" title="%s">%s</span>',
            $page === $current_page ? ' pager-active font-bold' : '',
            htmlspecialchars($content),
            $page,
            htmlspecialchars($title),
            $label
        );

    if ($current_page > 1) {
        echo $btn(1,              '&#171;', 'First');   // «
        echo $btn($current_page - 1, '&#8249;', 'Previous'); // ‹
    }

    for ($i = max(1, $current_page - 5); $i <= min($total_pages, $current_page + 5); $i++) {
        echo $btn($i, $i);
    }

    if ($current_page < $total_pages) {
        echo $btn($current_page + 1, '&#8250;', 'Next'); // ›
        echo $btn($total_pages,      '&#187;', 'Last');  // »
    }
}

function draw_modal_pager($total_pages, $current_page = 1) {
    if ($total_pages <= 1) return;

    $btn = fn($page, $label, $title = '') =>
        sprintf(
            '<span class="me-2 pager-btn%s" data-page="%d" title="%s">%s</span>',
            $page === $current_page ? ' pager-active font-bold' : '',
            $page,
            htmlspecialchars($title),
            $label
        );

    if ($current_page > 1) {
        echo $btn(1, '&#171;', 'First');
        echo $btn($current_page - 1, '&#8249;', 'Previous');
    }

    for ($i = max(1, $current_page - 5); $i <= min($total_pages, $current_page + 5); $i++) {
        echo $btn($i, $i);
    }

    if ($current_page < $total_pages) {
        echo $btn($current_page + 1, '&#8250;', 'Next');
        echo $btn($total_pages, '&#187;', 'Last');
    }
}

function convertDate($indate, $dsep = "/")
{
	if (!isset($indate) || strlen($indate) < 8) {
		return 0;
	}
	$s1 = explode(" ", $indate);
	$s2 = explode($dsep, $s1[0]);
	if (!isset($s1[1])) {
		$s1[1] = "00:00:00";
	}
	$dout = $s2[2] . "-" . $s2[1] . "-" . $s2[0] . " " . $s1[1];
	return strtotime($dout);
}

function getCategories($id)
{
	global $db;
	$cats = $id . ",";
	$sql = sprintf("select * from categories where parent_id=%d", $id);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	while ($c = $res->fetch_object()) {
		$cats .= $c->id . ",";
		$sql2 = sprintf("select count(*) from categories where parent_id=%d", $c->id);
		if (!($res2 = $db->query($sql2))) {
			die($db->error . "--" . $sql2);
		}
		$cnt = $res2->num_rows;
		if ($cnt) {
			$cats .= getCategories($c->id);
		}
	}
	return $cats;
}

function getPageParents($id)   // recursive function to get category tree - used by drilldown
{
	global $db;
	$pages = $id.',';
	$sql = sprintf("select * from pages where pageparent=%d",$id);	
	if(!$res = $db->query($sql)) die($db->error.'--'.$sql);
	while($c = $res->fetch_object())
	{
		$pages .= $c->id.',';
		$sql2 = sprintf("select count(*) from pages where pageparent=%d",$c->id);
		if(!$res2 = $db->query($sql2)) die($db->error.'--'.$sql2);
		$cnt = $res2->num_rows;
		if($cnt)
		{
			$pages .= getPageParents($c->id);
		}	
	}
	return $pages;
}

function getpagelistdrilldown($id,$cond='')
{
	global $db;
	$pages = getPageParents($id);
	$pages = rtrim($pages, ",");
	$sql = sprintf("select * from pages where id in(%s) %s",
	$pages,$cond);
	if(!$res = $db->query($sql)) die($db->error.'--'.$sql);
	return $res;
}

function getprodlistdrilldown($id, $cond = "", $offset = 0, $perPage = PER_PAGE) {
    global $db;
    $cats = rtrim(getCategories($id), ",");
    $sql = sprintf(
        "SELECT * FROM products WHERE cat_id IN (%s) AND active=1 %s LIMIT %d,%d",
        $cats,
        $cond,
        $offset,
        $perPage
    );

    if (!($res = $db->query($sql))) die($db->error . "--" . $sql);
    return $res;
}

function getprodlistdrilldownsubcat($id)
{
	global $db;
	$cats = getCategories($id);
	$cats = rtrim($cats, ",");
	$sql = sprintf(
		"select *,p.id as pid from products p,prod_cat c where p.id=c.prod_id and p.onweb=1 and c.cat_id in(%s)",
		$cats,
	);
	$res = $db->query($sql);
	return $res;
}

function getPageChildren($page,$level,$selpage)
{
	global $db,$offsetadder,$depth;
	$sql = sprintf("select * from pages where pageparent = %d",$page);
	if(!$res = $db->query($sql)) die($db->error.'--'.$sql);
	while($spage = $res->fetch_object()) 
	{
		if($level > 0)
		{
			for($i=0;$i<$level;$i++)
			{
				$offsetadder .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
		if(is_array($selpage))
		{
			$selected = (in_array($spage->id,$selpage))?' selected="selected"':'';
		}
		else
		{
			$selected = ($spage->id == $selpage)?' selected="selected"':'';
		}
		echo '<option value="'.$spage->id.'" '.$selected.'>'.$offsetadder.' - '.stripslashes($spage->slug).'</option>'."\n";
		getPageChildren($spage->id, $level+1,$selpage);
		$offsetadder = '&nbsp;&nbsp;&nbsp;&nbsp;';
	} 
}

function rupd($amount, $rval)
{
	$inc = 1 / $rval;
	return ceil($amount * $inc) / $inc;
}

function createEditField($table, $field, $shortcode, $iid, $ival, $inwidth)
{
    $spanId = $shortcode . $iid;
    $divId  = $shortcode . "Dv" . $iid;
    $inputId = "n" . $shortcode . $iid;

    echo '
    <span 
        id="'.$spanId.'" 
        class="cursor-pointer text-blue-600 hover:text-blue-800"
        onclick="editF(\''.$shortcode.'\','.$iid.')">
        '.htmlspecialchars($ival).'
    </span>

    <div id="'.$divId.'" class="hidden mt-1 space-x-2">
        <input
            id="'.$inputId.'"
            class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring focus:ring-blue-200"
            style="width:'.$inwidth.'"
            value="'.htmlspecialchars($ival).'"
        />

        <button
            class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition"
            onclick="goEdit(\''.$table.'\',\''.$shortcode.'\',\''.$field.'\','.$iid.')">
            Save
        </button>

        <button
            class="bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs hover:bg-gray-400 transition"
            onclick="hideEdit(\''.$shortcode.'\','.$iid.')">
            Cancel
        </button>
    </div>
    ';
}

function multiexplode($delimiters, $string)
{
	$master = str_replace($delimiters, $delimiters[0], $string);
	$op = explode($delimiters[0], $master);
	return $op;
}

function getRecordCount($table, $cond = "")
{
	global $db;
	$sql = sprintf("select count(*) as cnt from %s %s", $table, $cond);
	if (!($res = $db->query($sql))) {
		die($db->error . "--" . $sql);
	}
	$rec = $res->fetch_object();
	return $rec->cnt;
}

function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . " GB";
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . " MB";
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . " KB";
	} elseif ($bytes > 1) {
		$bytes = $bytes . " bytes";
	} elseif ($bytes == 1) {
		$bytes = $bytes . " byte";
	} else {
		$bytes = "0 bytes";
	}
	return $bytes;
}

function make_thumb($src, $dest, $desired_widths, $extension)
{

	$file = [];
	$paths_to_file_size = [];
	$widths = [];
	foreach ($dest as $thumb_path => $img) {
		$i = 0;
		foreach ($desired_widths as $value) {
			$widths[] = $value;
			$paths_to_file_size[] = $thumb_path . $desired_widths[$i] . "/" . $img;
			$i++;
		}
	}
	$c = 0;
	foreach ($src as $upload_path => $img) {
		$src = $upload_path . $img;
		foreach ($paths_to_file_size as $path_to_file_size) {
			switch ($extension) {
				case "jpeg":
				case "jpg":
				case "jpe":
					$source_image = imagecreatefromjpeg($src);
					break;
				case "gif":
					$source_image = imagecreatefromgif($src);
					break;
				case "png":
					$source_image = imagecreatefrompng($src);
					break;
			}
			$width = imagesx($source_image);
			$height = imagesy($source_image);
			$desired_height = floor($height * ($widths[$c] / $width));
			$virtual_image = imagecreatetruecolor($widths[$c], $desired_height);
			imagecopyresampled(
				$virtual_image,
				$source_image,
				0,
				0,
				0,
				0,
				$widths[$c],
				$desired_height,
				$width,
				$height,
			);

			switch ($extension) {
				case "jpeg":
				case "jpg":
				case "jpe":
					imagejpeg($virtual_image, $path_to_file_size, 100);
					break;
				case "gif":
					imagejpeg($virtual_image, $path_to_file_size, 100);
					break;
				case "png":
					imagejpeg($virtual_image, $path_to_file_size, 100);
					break;
			}
			$c++;
			chmod($path_to_file_size, 0644);
		}
	}
}

function renderChooseImage(array $opts = []): void
{
    $fieldId      = $opts['fieldId']      ?? 'imagepath';
    $boxId        = $opts['boxId']        ?? 'main-img-box';
    $imgId        = $opts['imgId']        ?? 'main-img';
    $type         = $opts['type']         ?? 'single';
    $content      = $opts['content']      ?? 'pageform';
    $existingPath = $opts['existingPath'] ?? '';
    $label        = $opts['label']        ?? 'Select Image';

    $src = '';
    if ($existingPath) {
        $src = file_exists(PUBLIC_UPLOADS_PATH . '/thumbs/200/' . $existingPath)
            ? BASE_URL_IMG_DIR . '/thumbs/200/' . $existingPath
            : BASE_URL_IMG_DIR .'/'. $existingPath;
    }
    ?>
    <div class="choose-image flex flex-col w-full gap-y-2">
        <button type="button"
                data-open-images
                data-content="<?= htmlspecialchars($content) ?>"
                data-type="<?= htmlspecialchars($type) ?>"
                data-box-id="<?= htmlspecialchars($boxId) ?>"
                data-img-id="<?= htmlspecialchars($imgId) ?>"
                data-field-id="<?= htmlspecialchars($fieldId) ?>"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition w-full">
            <?= htmlspecialchars($label) ?>
        </button>

        <div id="<?= htmlspecialchars($boxId) ?>">
            <?php if ($src): ?>
                <img id="<?= htmlspecialchars($imgId) ?>"
                     src="<?= htmlspecialchars($src) ?>"
                     class="rounded border max-w-[200px]"
                     alt="<?= htmlspecialchars($label) ?>">
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function actionButtons(array $config = []) {
    $module = $config['module'] ?? 'page';
    $id = $config['id'] ?? '';
    $targets = $config['targets'] ?? []; 
?>
    <div 
      class="flex flex-col space-y-2 min-w-0"
      data-module="<?= htmlspecialchars($module) ?>"
      data-id="<?= htmlspecialchars($id) ?>"
      <?php foreach ($targets as $action => $target) : ?>
        data-<?= $action ?>="<?= htmlspecialchars($target) ?>"
      <?php endforeach; ?>
    >

        <?php if (isset($targets['save'])) : ?>
            <button type="button" data-action="save" class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition">
                Save
            </button>
        <?php endif; ?>

        <?php if (isset($targets['back'])) : ?>
            <button type="button" data-action="back" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">
                Back
            </button>
        <?php endif; ?>

        <?php if (isset($targets['new'])) : ?>
            <button type="button" data-action="new" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">
                New
            </button>
        <?php endif; ?>

        <?php if (isset($targets['refresh']) && !empty($id)) : ?>
            <button type="button" data-action="refresh" class="bg-yellow-400 px-2 py-1 rounded hover:bg-yellow-500 transition">
                Refresh
            </button>
        <?php endif; ?>

        <?php if (isset($targets['delete']) && !empty($id)) : ?>
            <button type="button" data-action="delete" data-table="<?= $module;?>" data-id="<?= $id; ?>" data-target="<?= $targets['delete'] ?? 'pagelist' ?>" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition">
                Delete
            </button>
        <?php endif; ?>

        <?php if (isset($targets['edit'])) : ?>
            <button type="button" data-action="edit" class="bg-yellow-400 px-2 py-1 rounded hover:bg-yellow-500 transition">
                Edit
            </button>
        <?php endif; ?>

    </div>
<?php
}

function buildProductTable($products) {
    ob_start();
    ?>

<div>
    <div class="overflow-x-auto">
      <table id="table-1" class="min-w-full text-sm text-left border-collapse text-zinc-900">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
          <tr>
            <th class="px-4 py-3">Row</th>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Slug</th>
            <th class="px-4 py-3 text-center">Active</th>
            <th class="px-4 py-3 text-center">View/Edit</th>
            <th class="px-4 py-3 text-center">Delete</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
<?php
$i=1;
if ($products->num_rows):
    while ($product = $products->fetch_object()):
        $cat = getRecord('categories', 'id', $product->cat_id);
?>
          <tr class="hover:bg-gray-50 transition text-zinc-900">
            <td class="px-4 py-3">
              <?= $i ?>
            </td>
            <td class="px-4 py-3">
              <?php createEditField('products', 'title', 'pn', $product->id, stripslashes($product->title), '200px'); ?>
            </td><td class="px-4 py-3">
              <?php echo stripslashes($product->slug); ?>
            </td>

            <td class="px-4 py-3 text-center">
              <span class="cursor-pointer font-medium text-blue-600 hover:text-blue-800"
                    onclick="flipField('products','active',<?= $product->id ?>)"
                    id="active_<?= $product->id ?>">
                <?= $product->active ? 'Y' : 'N' ?>
              </span>
            </td>

            <td class="px-4 py-3 text-center">
<?php
                actionButtons([
                    'module' => 'products',
                    'id' => $product->id,
                    'targets' => [
                        'edit'  => 'prodform',
                    ]
                ]);
?>
            </td>
            <td class="px-4 py-3 text-center">
<?php
                actionButtons([
                    'module' => 'products',
                    'id' => $product->id,
                    'targets' => [
                        'delete'  => 'prodlist',
                    ]
                ]);
?>
            </td>
          </tr>
<?php
$i++;
    endwhile;
else:
?>
          <tr>
            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No items currently in system</td>
          </tr>
<?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<div id="flagpanel"></div>
<?php
    return ob_get_clean();
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);

    require APP_PATH . "/views/admin/{$view}.php";
}

function validatePassword(string $password, string $confirm = null): array {
    $errors = [];

    if ($confirm !== null && $password !== $confirm) {
        $errors[] = "Password must match.";
    }
    if (strlen($password) < 10) $errors[] = "Password must be at least 10 characters.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Must contain at least one uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Must contain at least one lowercase letter.";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "Must contain at least one number.";
    if (!preg_match('/[`!@#$%^&*()_+\-=\[\]{}|;:\'"<>,.?\/~]/', $password)) {
        $errors[] = "Must contain at least one special character.";
    }

    return $errors;
}

function img_stem(string $imagepath): array
{
    $stem = pathinfo($imagepath, PATHINFO_FILENAME);
    $base = BASE_URL . '/uploads/';
    return [
        'src'  => $base . $stem,           // original: uploads/image
        'webp' => $base . 'webp/' . $stem, // webp copy: uploads/webp/image
    ];
}


// declare(strict_types=1);

/**
 * Deletes uploaded image files (and all derivative copies) that have no
 * corresponding record in the images table, matched by filename stem
 * (without extension) so stray derivatives are caught regardless of format.
 *
 * @param  mysqli $db      Active database connection
 * @param  bool   $dryRun  If true, logs what would be deleted without touching files
 * @return array           ['deleted' => [...], 'errors' => [...]]
 */
function cleanupOrphanImages(mysqli $db, bool $dryRun = false): array
{
    $deleted = [];
    $errors  = [];

    // --- 1. Build a set of known stems from the database ---
    $result = $db->query("SELECT imagepath FROM images");

    if (!$result) {
        return ['deleted' => [], 'errors' => ["DB query failed: " . $db->error]];
    }

    $knownStems = [];
    while ($row = $result->fetch_assoc()) {
        $stem = pathinfo($row['imagepath'], PATHINFO_FILENAME);
        $knownStems[$stem] = true;
    }
    $result->free();

    // --- 2. Scan all relevant directories for any image file ---
    $base = rtrim(PUBLIC_UPLOADS_PATH, '/') . '/';

    $scanDirs = [
        $base,
        $base . 'facebook/',
        $base . 'twitter/',
        $base . 'thumbs/200/',
        $base . 'thumbs/60/',
        $base . 'webp/',
    ];

    foreach ($scanDirs as $dir) {
        $pattern = $dir . '*.{jpg,jpeg,jpe,gif,png,ico,webp,avif,svg}';
        $files   = glob($pattern, GLOB_BRACE) ?: [];

        foreach ($files as $file) {
            $stem = pathinfo($file, PATHINFO_FILENAME);

            if (isset($knownStems[$stem])) {
                continue; // Recognised — leave it
            }

            if ($dryRun) {
                $deleted[] = '[DRY RUN] ' . $file;
                continue;
            }

            if (unlink($file)) {
                $deleted[] = $file;
            } else {
                $errors[] = "Failed to delete: " . $file;
            }
        }
    }

    return ['deleted' => $deleted, 'errors' => $errors];
}
