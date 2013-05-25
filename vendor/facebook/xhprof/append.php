<?php

if (extension_loaded('xhprof') && $xhprof_data = xhprof_disable())
{
	require_once(__DIR__.'/xhprof_lib/utils/xhprof_lib.php');
	require_once(__DIR__.'/xhprof_lib/utils/xhprof_runs.php');

	$xhprof_runs = xhprof_runs_from_config();
  $xhprof_runs->save_run($xhprof_data, getenv('XHPROF_NAMESPACE') ?: 'default');
}
