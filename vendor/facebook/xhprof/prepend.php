<?php

if((getenv('XHPROF_ENABLE') || !empty($_GET['xhprof'])) && extension_loaded('xhprof'))
{
	xhprof_enable(XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU);

	// set an identifier for the app to reference
	$runId = substr(md5(uniqid(@$_SERVER['SERVER_ADDR'], true)),0,14);
	putenv("XHPROF_RUNID=$runId");
}

