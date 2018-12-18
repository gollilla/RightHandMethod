<?php



namespace soradore\ai;


/* Base */
use pocketmine\plugin\PluginBase;

/* Events */
use pocketmine\event\Listener;


/* Level and Math */
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

use pocketmine\entity\Entity;
use pocketmine\utils\Config;

use soradore\ai\Task\ZombieTask;
use soradore\ai\Task\SpawnTask;
use soradore\ai\CustomEntities\CustomZombie;

class main extends PluginBase implements Listener{

    public static $entityCount = 0;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->id = [];
    }

    public function onDamage(\pocketmine\event\entity\EntityDamageEvent $ev){
        $entity = $ev->getEntity();
        if($entity instanceof \pocketmine\entity\Zombie && $ev instanceof \pocketmine\event\entity\EntityDamageByEntityEvent){
            $id = $entity->getId();
            if(!isset($this->id[$id])){
                $zombie = new CustomZombie($this, $entity, $ev->getDamager());
                $task = new ZombieTask($this, $zombie);
                $this->getScheduler()->scheduleRepeatingTask($task, 1);
                $this->id[$id] = $task;
            }else{
            }
            //$ev->setKnockBack(0);

        }
    }
    
}

