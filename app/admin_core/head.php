<!DOCTYPE html>
<html>
<head>
<title><?= COMPANY_NAME ?? 'Admin' ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-store" />
<?php require_once __DIR__ . "/scripts.php"; ?>
</head>
<body data-flash-message="<?= htmlspecialchars($_SESSION['access_grant_message'] ?? '', ENT_QUOTES) ?>"
      data-flash-type="<?= $_SESSION['access_grant'] ?? null === true ? 'success' : 'error' ?>">
	<div class="px-20 flex flex-col gap-y-10 pb-10">

