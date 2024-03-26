<?php

namespace Terpz710\LumberJack;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;
use pocketmine\item\ItemTypeIds;

class Loader extends PluginBase implements Listener {

    private $logBlocks = [
        BlockTypeIds::OAK_LOG,
        BlockTypeIds::SPRUCE_LOG,
        BlockTypeIds::BIRCH_LOG,
        BlockTypeIds::JUNGLE_LOG,
        BlockTypeIds::ACACIA_LOG,
        BlockTypeIds::DARK_OAK_LOG,
    ];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $tool = $event->getItem();

        if ($tool->getTypeId() === ItemTypeIds::WOODEN_AXE ||
            $tool->getTypeId() === ItemTypeIds::STONE_AXE ||
            $tool->getTypeId() === ItemTypeIds::IRON_AXE ||
            $tool->getTypeId() === ItemTypeIds::GOLDEN_AXE ||
            $tool->getTypeId() === ItemTypeIds::DIAMOND_AXE ||
            $tool->getTypeId() === ItemTypeIds::NETHERITE_AXE) {

            if (in_array($block->getTypeId(), $this->logBlocks)) {
                $chance = 10;

                if (mt_rand(1, 100) <= $chance) {
                    $drops = $block->getDrops($tool);
                    if (!empty($drops)) {
                        $player->getInventory()->addItem(array_shift($drops));
                    }
                    $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
                    $event->setDrops([]);
                    $adjacentLogs = $this->getAdjacentLogs($block, 2, 2, 10);

                    foreach ($adjacentLogs as $adjLog) {
                        $drops = $adjLog->getDrops($tool);
                        if (!empty($drops)) {
                            $player->getInventory()->addItem(array_shift($drops));
                        }
                        $adjLog->getPosition()->getWorld()->setBlock($adjLog->getPosition(), VanillaBlocks::AIR());
                    }
                }
            }
        }
    }

    private function getAdjacentLogs(Block $block, int $dxRange, int $dyRange, int $dzRange): array {
        $adjacentLogs = [];
        $world = $block->getPosition()->getWorld();
        $x = $block->getPosition()->getX();
        $y = $block->getPosition()->getY();
        $z = $block->getPosition()->getZ();

        for ($dx = -$dxRange; $dx <= $dxRange; $dx++) {
            for ($dy = -$dyRange; $dy <= $dyRange; $dy++) {
                for ($dz = -$dzRange; $dz <= $dzRange; $dz++) {
                    $adjBlock = $world->getBlockAt($x + $dx, $y + $dy, $z + $dz);
                    if (in_array($adjBlock->getTypeId(), $this->logBlocks)) {
                        $adjacentLogs[] = $adjBlock;
                    }
                }
            }
        }
        return $adjacentLogs;
    }
}