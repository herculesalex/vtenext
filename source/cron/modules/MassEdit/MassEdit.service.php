<?php
/* crmv@91571 */

require_once('include/utils/MassEditUtils.php');

// this is to run the process directly from the terminal
$importid = intval($_REQUEST['massid']);

$MUtils = MassEditUtils::getInstance();

$r = true;
if ($importid > 0) {
	$r = $MUtils->processCron($importid);
} else {
	$r = $MUtils->processCron();
}

if (!$r) {
	echo "Error during the MassEdit.\n";
}