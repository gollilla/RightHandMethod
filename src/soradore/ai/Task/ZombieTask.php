<?php

namespace soradore\ai\Task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\particle\FlameParticle;
use soradore\ai\CustomEntities\CustomZombie;

class ZombieTask extends Task{

    public $zombie = null;
    public $move = true;


    public function __construct(PluginBase $plugin, CustomZombie $zombie){
        $this->zombie = $zombie;
        $this->owner = $plugin;
    }

    public function getOwner(){
        return $this->owner;
    }

    public function onRun(int $currentTick){
        if(!$this->zombie->isAlive()){
            $this->stop();
        }
        $target = $this->zombie->getTmpGoal();
        if(is_null($target)){
            $this->stop();
        } 
        $level = $this->zombie->getWorld();

        $tx = $target->x;
        $tz = $target->z;

        $cx = $this->zombie->getX();
        $cz = $this->zombie->getZ();

        if((0 <= $cx && 0 <= $tx) || ($cx < 0 && $tx < 0)){
            if($tx < $cx){
                $x = -($cx - $tx);
            }else{
                $x = $tx - $cx;
            }
        }else if(0 <= $cx && $tx < 0){
            $x = -(abs($tx) + $cx);
        }else if($cx < 0 && 0 <= $tx){
            $x = abs($cx) + $tx;
        }


        if((0 <= $cz && 0 <= $tz) || ($cz < 0 && $tz < 0)){
            if($tz < $cz){
                $z = -($cz - $tz);
            }else{
                $z = $tz - $cz;
            }
        }else if(0 <= $cz && $tz < 0){
            $z = -(abs($tz) + $cz);
        }else if($cz < 0 && 0 <= $tz){
            $z = abs($cz) + $tz;
        }
        
        $rad = atan2($x, $z);
        $x = CustomZombie::SPEED * sin($rad);
        $y = 0;
        $z = CustomZombie::SPEED * cos($rad);
        $this->zombie->move($x,$y,$z);

        $this->zombie->setYaw(-rad2deg($rad));
        if($this->zombie->isTmpGoal()){
        	$this->zombie->setNextTmpGoal();
            //var_dump("TmpGoal\n");
        }

        if($this->zombie->isGoal()){
            //var_dump("Goal");
        	$this->stop();
        }

        $particle = new FlameParticle($target);
        $level->addParticle($particle);
    }


    public function stop(){
    	$this->getOwner()->getScheduler()->cancelTask($this->getTaskId());
    }

}