<?php namespace spitfire\core\async;

class Async
{
	
	public static function defer($defer, $task, $ttl = 10) {
		$copy = db()->table('spitfire\core\async\Async')->newRecord();
		$copy->status = 'pending';
		$copy->ttl = $ttl;
		$copy->scheduled = $defer < 86400 * 365 * 50? time() + $defer : $defer; #It's been the timestamp's 50th aniversary this year
		$copy->task = serialize($task);
		$copy->store();
	}
	
}
