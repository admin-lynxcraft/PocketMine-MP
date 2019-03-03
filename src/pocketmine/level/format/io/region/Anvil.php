<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\level\format\io\region;

use pocketmine\level\format\io\ChunkUtils;
use pocketmine\level\format\io\WritableLevelProvider;
use pocketmine\level\format\SubChunk;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use function str_repeat;

class Anvil extends RegionLevelProvider implements WritableLevelProvider{
	use LegacyAnvilChunkTrait;

	protected function serializeSubChunk(SubChunk $subChunk) : CompoundTag{
		return new CompoundTag("", [
			new ByteArrayTag("Blocks", ChunkUtils::reorderByteArray($subChunk->getBlockIdArray())), //Generic in-memory chunks are currently always XZY
			new ByteArrayTag("Data", ChunkUtils::reorderNibbleArray($subChunk->getBlockDataArray())),
			new ByteArrayTag("SkyLight", str_repeat("\x00", 2048)),
			new ByteArrayTag("BlockLight", str_repeat("\x00", 2048))
		]);
	}

	protected function deserializeSubChunk(CompoundTag $subChunk) : SubChunk{
		return new SubChunk(
			ChunkUtils::reorderByteArray($subChunk->getByteArray("Blocks")),
			ChunkUtils::reorderNibbleArray($subChunk->getByteArray("Data"))
			//ignore legacy light information
		);
	}

	protected static function getRegionFileExtension() : string{
		return "mca";
	}

	protected static function getPcWorldFormatVersion() : int{
		return 19133;
	}

	public function getWorldHeight() : int{
		//TODO: add world height options
		return 256;
	}
}
