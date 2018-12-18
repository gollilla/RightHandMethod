<?php 

namespace soradore\ai\Task;

use pocketmine\scheduler\Task;
use pocketmine\level\particle\FlameParticle;

class routeShowTask extends Task{

	public function __construct($plugin, $vector, $level){
		$this->vector = $vector;
		$this->plugin = $plugin;
		$this->level = $level;
	}


	public function onRun(int $tick){
		$level = $this->level;
		$particle = new FlameParticle($this->vector);
        $level->addParticle($particle);
	}

	public function stop(){
    	$this->plugin->getScheduler()->cancelTask($this->getTaskId());
	}
}