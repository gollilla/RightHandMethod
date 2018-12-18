<?php 

namespace soradore\ai\CustomEntities;

use pocketmine\Server;

use pocketmine\entity\Entity;
use pocketmine\entity\Zombie;

use pocketmine\level\Position;
use pocketmine\math\Vector3;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use soradore\ai\Task\TaskManager;

class CustomZombie{

    const SPEED = 0.5;//WalkingSpeed

    public $target = null;
    public $tmpGoal; //Vevtor3
    public $goal; //Vector3

    public function __construct($main, Entity $zombie, Vector3 $goal){
        $this->main = $main;
        $this->zombie = $zombie;
        $this->goal = $goal;
        $this->setNextTmpGoal();
    }


    public function getCenterVector(){
        return new Vector3(floor($this->getX()) + 0.5, floor($this->getY()), floor($this->getZ()) + 0.5);
    }


    public function getX(){
        return $this->zombie->x;
    }

    public function getY(){
        return $this->zombie->y;
    }

    public function getZ(){
        return $this->zombie->z;
    }

    public function setPitch($deg){
        $this->zombie->pitch = $deg;
    }


    public function setYaw($deg){
        $this->zombie->yaw = $deg;
    }


    public function getWorld(){
        return $this->zombie->level;
    }


    public function getDirection(){
        return $this->zombie->getDirection();
    }


    public function isAlive(){
        return $this->zombie->isAlive();
    }


    public function getDistance($target){
        return sqrt($this->zombie->distance($target));
    }


    public function getTmpGoal(){
        return $this->tmpGoal;
    }


    public function isGoal(){
        if(abs($this->goal->x - $this->getX()) <= 0.5 && abs($this->goal->z - $this->getZ()) <= 0.5){
            return true;
        }
        return false;
    }

    public function isTmpGoal(){
        if(abs($this->tmpGoal->x - $this->getX()) <= 0.5 && abs($this->tmpGoal->z - $this->getZ()) <= 0.5){
            TaskManager::queryTask($this->main, $this->tmpGoal, $this->getWorld());
            return true;
        }
        return false;
    }


    public function move($x, $y, $z){
        $this->zombie->move($x, $y, $z);
    }



    public function getName(){
        return "Zombie";
    }


    public function getRight(){
        $dir = $this->getDirection();
        if($dir == 3){
            return 0;
        }
        return $dir+=1;
    }

    public function getLeft(){
        $dir = $this->getDirection();
        if($dir == 0){
            return 3;
        }
        return $dir-=1;
    }


    public function getBack(){
        $dir = $this->getDirection();
        return abs($dir - 2);
    }


    public function getRelLeft(){
        $vector = $this->getCenterVector();
        switch($this->zombie->getDirection()){
            case 0:
                $return = $vector->add(1,1);
                break;
            case 1:
                $return = $vector->add(0,1,1);
                break;
            case 2:
                $return = $vector->add(-1,1);
                break;
            case 3:
                $return = $vector->add(0,1,-1);
                break;
        }
        return $return;
    }

    public function getRelFront(){
        $vector = $this->getCenterVector();
        switch($this->zombie->getDirection()){
            case 0:
                $return = $vector->add(0,1,1);
                break;
            case 1:
                $return = $vector->add(-1,1);
                break;
            case 2:
                $return = $vector->add(0,1,-1);
                break;
            case 3:
                $return = $vector->add(1,1);
                break;
        }
        return $return;
    }


    public function getRelRight(){
        $vector = $this->getCenterVector();
        switch($this->zombie->getDirection()){
            case 0:
                $return = $vector->add(-1,1);
                break;
            case 1:
                $return = $vector->add(0,1,-1);
                break;
            case 2:
                $return = $vector->add(1,1);
                break;
            case 3:
                $return = $vector->add(0,1,1);
                break;
        }
        return $return;
    }


    
    public function frontIsSolid(){
        $return = false;
        $level = $this->zombie->getLevel();
        $relFront = $this->getRelFront();
        $return = $level->getBlock($relFront);
                
        return $return->isSolid();
    }

    
    public function rightIsSolid(){
        $return = false;
        $level = $this->zombie->getLevel();
        $relRight = $this->getRelRight();
        $return = $level->getBlock($relRight);
                
        return $return->isSolid();
    }


    public function leftIsSolid(){
        $return = false;
        $level = $this->zombie->getLevel();
        $relLeft = $this->getRelLeft();
        $return = $level->getBlock($relLeft);
               
        return $return->isSolid();
    }


    public function setDirection(int $dir){
        switch ($dir) {
            case 0:
                $this->setYaw(0);
                break;
            case 1:
                $this->setYaw(90);
                break;
            case 2:
                $this->setYaw(180);
                break;
            case 3:
                $this->setYaw(270);
                break;
        }
    }


    public function setNextTmpGoal(){
        if($this->isGoal()){
            return false;
        }

        if(!$this->rightIsSolid()){ //右が開いてる
            $this->tmpGoal = $this->getRelRight();
            $this->setDirection($this->getRight());
            return true;
        }

        if(!$this->frontIsSolid()){ //前が開いてる
            $this->tmpGoal = $this->getRelFront();
            return true;
        }

        $this->tmpGoal = $this->getCenterVector();
        $this->setDirection($this->getLeft());
    }
 

}