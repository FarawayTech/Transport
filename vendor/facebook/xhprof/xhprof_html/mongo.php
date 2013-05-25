<?php

$GLOBALS['XHPROF_LIB_ROOT'] = dirname(__FILE__) . '/../xhprof_lib';
require_once $GLOBALS['XHPROF_LIB_ROOT'].'/display/xhprof.php';

$type = $_GET['source'];
$run_id = $_GET['run'];

// look up the original run
$runs = new XHProfRuns_Mongo();
$collection = $runs->mongoCollection($type);
$run = $collection->findOne(array("_id" => $run_id));

// find related runs
$related = $collection
	->find(array('uri' => $run['uri']))
	->sort(array('time'=>-1))
	->limit(10)
	;

echo "<h4>Related Runs for {$run['uri']}</h4>";
echo "<ol>";

foreach($related as $related_run)
{
	$run_href = sprintf("index.php?run=%s&source=%s", $related_run['_id'], $type);
	$compare_href = sprintf("index.php?run1=%s&run2=%s&source=%s", $run_id, $related_run['_id'], $type);
	$css = ($run_id == $related_run['_id']) ? 'font-weight: bold' : '';

	printf("<li style=\"%s\">Run <a href=\"%s\">%s</a>, started %s <a href=\"%s\">compare</a></li>\n",
		$css,
		$run_href,
		$related_run['_id'],
		date('r', $related_run['time']),
		$compare_href
	);
}

echo "</ol>";
