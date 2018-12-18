<?php

namespace soradore\ai\Task;

use pocketmine\math\Vector3;
class TaskManager{

    public static $tasks = [];

	public static function queryTask($plugin, $vector, $level){
		$id = $vector->x . ":" . $vector->z;
		if(isset(self::$tasks[$id])){
			self::$tasks[$id]->stop();
			unset(self::$tasks[$id]);
		}else{
			$task = new routeShowTask($plugin, $vector, $level);
			$plugin->getScheduler()->scheduleRepeatingTask($task, 15);
			self::$tasks[$id] = $task;
		}
	}
}