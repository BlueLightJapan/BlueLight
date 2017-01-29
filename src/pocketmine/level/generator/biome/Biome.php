<?php
namespace pocketmine\level\generator\biome;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\normal\biome\SwampBiome;
use pocketmine\level\generator\normal\biome\BeachBiome;
use pocketmine\level\generator\normal\biome\DesertBiome;
use pocketmine\level\generator\normal\biome\ForestBiome;
use pocketmine\level\generator\normal\biome\RoofedForestBiome;
use pocketmine\level\generator\normal\biome\SavannaBiome;
use pocketmine\level\generator\normal\biome\IcePlainsBiome;
use pocketmine\level\generator\normal\biome\MushroomIslandBiome;
use pocketmine\level\generator\normal\biome\MountainsBiome;
use pocketmine\level\generator\normal\biome\OceanBiome;
use pocketmine\level\generator\normal\biome\PlainBiome;
use pocketmine\level\generator\normal\biome\RiverBiome;
use pocketmine\level\generator\normal\biome\FrozenRiverBiome;
use pocketmine\level\generator\normal\biome\SmallMountainsBiome;
use pocketmine\level\generator\normal\biome\JungleBiome;
use pocketmine\level\generator\normal\biome\MesaBiome;
use pocketmine\level\generator\normal\biome\TaigaBiome;
use pocketmine\level\generator\hell\HellBiome;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;
use pocketmine\level\generator\populator\Flower;

abstract class Biome{
	const OCEAN = 0;
	const PLAINS = 1;
	const DESERT = 2;
	const MOUNTAINS = 3;
	const FOREST = 4;
	const TAIGA = 5;
	const SWAMP = 6;
	const RIVER = 7;
	const HELL = 8;
	const END = 9;
	const FROZEN_RIVER = 11;
	const ICE_PLAINS = 12;
	const ICE_MOUNTAINS = 13;
	const MUSHROOM_ISLAND = 14;
	const MUSHROOM_ISLAND_SHORE = 15;
	const BEACH = 16;
	const DESERT_HILLS = 17;
	const FOREST_HILLS = 18;
	const TAIGA_HILLS = 19;
	const SMALL_MOUNTAINS = 20;
	const JUNGLE = 21;
	const JUNGLE_HILLS = 22;
	const JUNGLE_EDGE = 23;
	const DEEP_OCEAN = 24;
	const STONE_BEACH = 25;
	const COLD_BEACH = 26;
	const BIRCH_FOREST = 27;
	const BIRCH_FOREST_HILLS = 28;
	const ROOFED_FOREST = 29;
	const COLD_TAIGA = 30;
	const COLD_TAIGA_HILLS = 31;
	const MEGA_TAIGA = 32;
	const MEGA_TAIGA_HILLS = 33;
	const EXTREME_HILLS_PLUS = 34;
	const SAVANNA = 35;
	const SAVANNA_PLATEAU = 36;
	const MESA = 37;
	const MESA_PLATEAU_F = 38;
	const MESA_PLATEAU = 39;
	const VOID = 127;
	//const MAX_BIOMES = 256; idk is here this. If is implemented FarLand need, but is not implemented. Fix far land in multiplayer
	
	/*
    SWAMPLAND,-> added (6)
    FOREST, -> added (4)
    TAIGA, -> added (5)
    DESERT, -> added (2)
    PLAINS, -> added (1)
    HELL, -> added (8)
    SKY, -> idk is this.. :/
    OCEAN, -> added (0)
    RIVER, -> added (7)
    EXTREME_HILLS, -> in construction (34)
    FROZEN_OCEAN, -> not exit in mcpe
    FROZEN_RIVER, -> in construction (11)
    ICE_PLAINS,
    ICE_MOUNTAINS,
    MUSHROOM_ISLAND,
    MUSHROOM_SHORE,
    BEACH,
    DESERT_HILLS,
    FOREST_HILLS,
    TAIGA_HILLS,
    SMALL_MOUNTAINS,
    JUNGLE,
    JUNGLE_HILLS,
    JUNGLE_EDGE,
    DEEP_OCEAN,
    STONE_BEACH,
    COLD_BEACH,
    BIRCH_FOREST,
    BIRCH_FOREST_HILLS,
    ROOFED_FOREST,
    COLD_TAIGA,
    COLD_TAIGA_HILLS,
    MEGA_TAIGA,
    MEGA_TAIGA_HILLS,
    EXTREME_HILLS_PLUS,
    SAVANNA,
    SAVANNA_PLATEAU,
    MESA, -> in construction (37)
    MESA_PLATEAU_FOREST,
    MESA_PLATEAU,
    SUNFLOWER_PLAINS,
    DESERT_MOUNTAINS,
    FLOWER_FOREST,
    TAIGA_MOUNTAINS,
    SWAMPLAND_MOUNTAINS,
    ICE_PLAINS_SPIKES,
    JUNGLE_MOUNTAINS,
    JUNGLE_EDGE_MOUNTAINS,
    COLD_TAIGA_MOUNTAINS,
    SAVANNA_MOUNTAINS,
    SAVANNA_PLATEAU_MOUNTAINS,
    MESA_BRYCE,
    MESA_PLATEAU_FOREST_MOUNTAINS,
    MESA_PLATEAU_MOUNTAINS,
    BIRCH_FOREST_MOUNTAINS,
    BIRCH_FOREST_HILLS_MOUNTAINS,
    ROOFED_FOREST_MOUNTAINS,
    MEGA_SPRUCE_TAIGA,
    EXTREME_HILLS_MOUNTAINS,
    EXTREME_HILLS_PLUS_MOUNTAINS,
    MEGA_SPRUCE_TAIGA_HILLS,
    */

	/** @var Biome[] */
	private static $biomes = [];

	private $id;
	private $registered = false;
	/** @var Populator[] */
	private $populators = [];

	private $minElevation;
	private $maxElevation;

	private $groundCover = [];

	protected $rainfall = 0.5;
	protected $temperature = 0.5;
	protected $grassColor = 0;

	protected static function register($id, Biome $biome){
		self::$biomes[(int) $id] = $biome;
		$biome->setId((int) $id);
		$biome->grassColor = self::generateBiomeColor($biome->getTemperature(), $biome->getRainfall());

		$flowerPopFound = false;

		foreach($biome->getPopulators() as $populator){
			if($populator instanceof Flower){
				$flowerPopFound = true;
				break;
			}
		}

		if($flowerPopFound === false){
			$flower = new Flower();
			$biome->addPopulator($flower);
		}
	}
/*
Mountains and Small Mountains is not principal biomes. Fix in future
*/

    /*
        public boolean i() {
        return false;
    }

    public final float j() {
        return this.A;
    }

    public final float getHumidity() {
        return this.D;
    }

    public final String l() {
        return this.z;
    }

    public final float m() {
        return this.B;
    }

    public final float getTemperature() {
        return this.C;
    }

    public final boolean p() {
        return this.F;
    }
    public static void q() {
        BiomeBase.a(0, "ocean", new BiomeOcean(new a("Ocean").c(-1.0f).d(0.1f)));
        BiomeBase.a(1, "plains", new BiomePlains(false, new a("Plains").c(0.125f).d(0.05f).a(0.8f).b(0.4f)));
        BiomeBase.a(2, "desert", new BiomeDesert(new a("Desert").c(0.125f).d(0.05f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(3, "extreme_hills", new BiomeBigHills(BiomeBigHills.Type.NORMAL, new a("Extreme Hills").c(1.0f).d(0.5f).a(0.2f).b(0.3f)));
        BiomeBase.a(4, "forest", new BiomeForest(BiomeForest.Type.NORMAL, new a("Forest").a(0.7f).b(0.8f)));
        BiomeBase.a(5, "taiga", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("Taiga").c(0.2f).d(0.2f).a(0.25f).b(0.8f)));
        BiomeBase.a(6, "swampland", new BiomeSwamp(new a("Swampland").c(-0.2f).d(0.1f).a(0.8f).b(0.9f).a(14745518)));
        BiomeBase.a(7, "river", new BiomeRiver(new a("River").c(-0.5f).d(0.0f)));
        BiomeBase.a(8, "hell", new BiomeHell(new a("Hell").a(2.0f).b(0.0f).a()));
        BiomeBase.a(9, "sky", new BiomeTheEnd(new a("The End").a()));
        BiomeBase.a(10, "frozen_ocean", new BiomeOcean(new a("FrozenOcean").c(-1.0f).d(0.1f).a(0.0f).b(0.5f).b()));
        BiomeBase.a(11, "frozen_river", new BiomeRiver(new a("FrozenRiver").c(-0.5f).d(0.0f).a(0.0f).b(0.5f).b()));
        BiomeBase.a(12, "ice_flats", new BiomeIcePlains(false, new a("Ice Plains").c(0.125f).d(0.05f).a(0.0f).b(0.5f).b()));
        BiomeBase.a(13, "ice_mountains", new BiomeIcePlains(false, new a("Ice Mountains").c(0.45f).d(0.3f).a(0.0f).b(0.5f).b()));
        BiomeBase.a(14, "mushroom_island", new BiomeMushrooms(new a("MushroomIsland").c(0.2f).d(0.3f).a(0.9f).b(1.0f)));
        BiomeBase.a(15, "mushroom_island_shore", new BiomeMushrooms(new a("MushroomIslandShore").c(0.0f).d(0.025f).a(0.9f).b(1.0f)));
        BiomeBase.a(16, "beaches", new BiomeBeach(new a("Beach").c(0.0f).d(0.025f).a(0.8f).b(0.4f)));
        BiomeBase.a(17, "desert_hills", new BiomeDesert(new a("DesertHills").c(0.45f).d(0.3f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(18, "forest_hills", new BiomeForest(BiomeForest.Type.NORMAL, new a("ForestHills").c(0.45f).d(0.3f).a(0.7f).b(0.8f)));
        BiomeBase.a(19, "taiga_hills", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("TaigaHills").a(0.25f).b(0.8f).c(0.45f).d(0.3f)));
        BiomeBase.a(20, "smaller_extreme_hills", new BiomeBigHills(BiomeBigHills.Type.EXTRA_TREES, new a("Extreme Hills Edge").c(0.8f).d(0.3f).a(0.2f).b(0.3f)));
        BiomeBase.a(21, "jungle", new BiomeJungle(false, new a("Jungle").a(0.95f).b(0.9f)));
        BiomeBase.a(22, "jungle_hills", new BiomeJungle(false, new a("JungleHills").c(0.45f).d(0.3f).a(0.95f).b(0.9f)));
        BiomeBase.a(23, "jungle_edge", new BiomeJungle(true, new a("JungleEdge").a(0.95f).b(0.8f)));
        BiomeBase.a(24, "deep_ocean", new BiomeOcean(new a("Deep Ocean").c(-1.8f).d(0.1f)));
        BiomeBase.a(25, "stone_beach", new BiomeStoneBeach(new a("Stone Beach").c(0.1f).d(0.8f).a(0.2f).b(0.3f)));
        BiomeBase.a(26, "cold_beach", new BiomeBeach(new a("Cold Beach").c(0.0f).d(0.025f).a(0.05f).b(0.3f).b()));
        BiomeBase.a(27, "birch_forest", new BiomeForest(BiomeForest.Type.BIRCH, new a("Birch Forest").a(0.6f).b(0.6f)));
        BiomeBase.a(28, "birch_forest_hills", new BiomeForest(BiomeForest.Type.BIRCH, new a("Birch Forest Hills").c(0.45f).d(0.3f).a(0.6f).b(0.6f)));
        BiomeBase.a(29, "roofed_forest", new BiomeForest(BiomeForest.Type.ROOFED, new a("Roofed Forest").a(0.7f).b(0.8f)));
        BiomeBase.a(30, "taiga_cold", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("Cold Taiga").c(0.2f).d(0.2f).a(-0.5f).b(0.4f).b()));
        BiomeBase.a(31, "taiga_cold_hills", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("Cold Taiga Hills").c(0.45f).d(0.3f).a(-0.5f).b(0.4f).b()));
        BiomeBase.a(32, "redwood_taiga", new BiomeTaiga(BiomeTaiga.Type.MEGA, new a("Mega Taiga").a(0.3f).b(0.8f).c(0.2f).d(0.2f)));
        BiomeBase.a(33, "redwood_taiga_hills", new BiomeTaiga(BiomeTaiga.Type.MEGA, new a("Mega Taiga Hills").c(0.45f).d(0.3f).a(0.3f).b(0.8f)));
        BiomeBase.a(34, "extreme_hills_with_trees", new BiomeBigHills(BiomeBigHills.Type.EXTRA_TREES, new a("Extreme Hills+").c(1.0f).d(0.5f).a(0.2f).b(0.3f)));
        BiomeBase.a(35, "savanna", new BiomeSavanna(new a("Savanna").c(0.125f).d(0.05f).a(1.2f).b(0.0f).a()));
        BiomeBase.a(36, "savanna_rock", new BiomeSavanna(new a("Savanna Plateau").c(1.5f).d(0.025f).a(1.0f).b(0.0f).a()));
        BiomeBase.a(37, "mesa", new BiomeMesa(false, false, new a("Mesa").a(2.0f).b(0.0f).a()));
        BiomeBase.a(38, "mesa_rock", new BiomeMesa(false, true, new a("Mesa Plateau F").c(1.5f).d(0.025f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(39, "mesa_clear_rock", new BiomeMesa(false, false, new a("Mesa Plateau").c(1.5f).d(0.025f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(127, "void", new BiomeVoid(new a("The Void").a()));
        BiomeBase.a(129, "mutated_plains", new BiomePlains(true, new a("Sunflower Plains").a("plains").c(0.125f).d(0.05f).a(0.8f).b(0.4f)));
        BiomeBase.a(130, "mutated_desert", new BiomeDesert(new a("Desert M").a("desert").c(0.225f).d(0.25f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(131, "mutated_extreme_hills", new BiomeBigHills(BiomeBigHills.Type.MUTATED, new a("Extreme Hills M").a("extreme_hills").c(1.0f).d(0.5f).a(0.2f).b(0.3f)));
        BiomeBase.a(132, "mutated_forest", new BiomeForest(BiomeForest.Type.FLOWER, new a("Flower Forest").a("forest").d(0.4f).a(0.7f).b(0.8f)));
        BiomeBase.a(133, "mutated_taiga", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("Taiga M").a("taiga").c(0.3f).d(0.4f).a(0.25f).b(0.8f)));
        BiomeBase.a(134, "mutated_swampland", new BiomeSwamp(new a("Swampland M").a("swampland").c(-0.1f).d(0.3f).a(0.8f).b(0.9f).a(14745518)));
        BiomeBase.a(140, "mutated_ice_flats", new BiomeIcePlains(true, new a("Ice Plains Spikes").a("ice_flats").c(0.425f).d(0.45000002f).a(0.0f).b(0.5f).b()));
        BiomeBase.a(149, "mutated_jungle", new BiomeJungle(false, new a("Jungle M").a("jungle").c(0.2f).d(0.4f).a(0.95f).b(0.9f)));
        BiomeBase.a(151, "mutated_jungle_edge", new BiomeJungle(true, new a("JungleEdge M").a("jungle_edge").c(0.2f).d(0.4f).a(0.95f).b(0.8f)));
        BiomeBase.a(155, "mutated_birch_forest", new BiomeForestMutated(new a("Birch Forest M").a("birch_forest").c(0.2f).d(0.4f).a(0.6f).b(0.6f)));
        BiomeBase.a(156, "mutated_birch_forest_hills", new BiomeForestMutated(new a("Birch Forest Hills M").a("birch_forest").c(0.55f).d(0.5f).a(0.6f).b(0.6f)));
        BiomeBase.a(157, "mutated_roofed_forest", new BiomeForest(BiomeForest.Type.ROOFED, new a("Roofed Forest M").a("roofed_forest").c(0.2f).d(0.4f).a(0.7f).b(0.8f)));
        BiomeBase.a(158, "mutated_taiga_cold", new BiomeTaiga(BiomeTaiga.Type.NORMAL, new a("Cold Taiga M").a("taiga_cold").c(0.3f).d(0.4f).a(-0.5f).b(0.4f).b()));
        BiomeBase.a(160, "mutated_redwood_taiga", new BiomeTaiga(BiomeTaiga.Type.MEGA_SPRUCE, new a("Mega Spruce Taiga").a("redwood_taiga").c(0.2f).d(0.2f).a(0.25f).b(0.8f)));
        BiomeBase.a(161, "mutated_redwood_taiga_hills", new BiomeTaiga(BiomeTaiga.Type.MEGA_SPRUCE, new a("Redwood Taiga Hills M").a("redwood_taiga_hills").c(0.2f).d(0.2f).a(0.25f).b(0.8f)));
        BiomeBase.a(162, "mutated_extreme_hills_with_trees", new BiomeBigHills(BiomeBigHills.Type.MUTATED, new a("Extreme Hills+ M").a("extreme_hills_with_trees").c(1.0f).d(0.5f).a(0.2f).b(0.3f)));
        BiomeBase.a(163, "mutated_savanna", new BiomeSavannaMutated(new a("Savanna M").a("savanna").c(0.3625f).d(1.225f).a(1.1f).b(0.0f).a()));
        BiomeBase.a(164, "mutated_savanna_rock", new BiomeSavannaMutated(new a("Savanna Plateau M").a("savanna_rock").c(1.05f).d(1.2125001f).a(1.0f).b(0.0f).a()));
        BiomeBase.a(165, "mutated_mesa", new BiomeMesa(true, false, new a("Mesa (Bryce)").a("mesa").a(2.0f).b(0.0f).a()));
        BiomeBase.a(166, "mutated_mesa_rock", new BiomeMesa(false, true, new a("Mesa Plateau F M").a("mesa_rock").c(0.45f).d(0.3f).a(2.0f).b(0.0f).a()));
        BiomeBase.a(167, "mutated_mesa_clear_rock", new BiomeMesa(false, false, new a("Mesa Plateau M").a("mesa_clear_rock").c(0.45f).d(0.3f).a(2.0f).b(0.0f).a()));
        */
	public static function init(){
		self::register(self::OCEAN, new OceanBiome()); //OceanBiome($temperature = -1.0f && $humidity = 0.1f);
		self::register(self::PLAINS, new PlainBiome());
		self::register(self::DESERT, new DesertBiome());
		self::register(self::MOUNTAINS, new MountainsBiome()); 
		self::register(self::FOREST, new ForestBiome());
		self::register(self::ROOFED_FOREST, new RoofedForestBiome());
		self::register(self::SAVANNA, new SavannaBiome());
		self::register(self::TAIGA, new TaigaBiome());
		self::register(self::SWAMP, new SwampBiome());
		self::register(self::RIVER, new RiverBiome());
		self::register(self::FROZEN_RIVER, new FrozenRiverBiome());
		self::register(self::ICE_PLAINS, new IcePlainsBiome());
		self::register(self::MUSHROOM_ISLAND, new MushroomIslandBiome());
		self::register(self::BEACH, new BeachBiome());
		self::register(self::SMALL_MOUNTAINS, new SmallMountainsBiome()); 
		self::register(self::JUNGLE, new JungleBiome());
		self::register(self::MESA, new MesaBiome());
		self::register(self::HELL, new HellBiome());
		self::register(self::BIRCH_FOREST, new ForestBiome(ForestBiome::TYPE_BIRCH));
	}

	/**
	 * @param $id
	 *
	 * @return Biome
	 */
	public static function getBiome($id){
		return isset(self::$biomes[$id]) ? self::$biomes[$id] : self::$biomes[self::OCEAN];
	}

	public function clearPopulators(){
		$this->populators = [];
	}

	public function addPopulator(Populator $populator){
		$this->populators[get_class($populator)] = $populator;
	}

	public function removePopulator($class){
		if(isset($this->populators[$class])){
			unset($this->populators[$class]);
		}
	}

	public function populateChunk(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		foreach($this->populators as $populator){
			$populator->populate($level, $chunkX, $chunkZ, $random);
		}
	}

	public function getPopulators(){
		return $this->populators;
	}

	public function setId($id){
		if(!$this->registered){
			$this->registered = true;
			$this->id = $id;
		}
	}

	public function getId(){
		return $this->id;
	}

	public abstract function getName();

	public function getMinElevation(){
		return $this->minElevation;
	}

	public function getMaxElevation(){
		return $this->maxElevation;
	}

	public function setElevation($min, $max){
		$this->minElevation = $min;
		$this->maxElevation = $max;
	}

	/**
	 * @return Block[]
	 */
	public function getGroundCover(){
		return $this->groundCover;
	}

	/**
	 * @param Block[] $covers
	 */
	public function setGroundCover(array $covers){
		$this->groundCover = $covers;
	}

	public function getTemperature(){
		return $this->temperature;
	}

	public function getRainfall(){
		return $this->rainfall;
	}

	private static function generateBiomeColor($temperature, $rainfall){
		$x = (1 - $temperature) * 255;
		$z = (1 - $rainfall * $temperature) * 255;
		$c = self::interpolateColor(256, $x, $z, [0x47, 0xd0, 0x33], [0x6c, 0xb4, 0x93], [0xbf, 0xb6, 0x55], [0x80, 0xb4, 0x97]);
		return ((int) ($c[0] << 16)) | (int) (($c[1] << 8)) | (int) ($c[2]);
	}


	private static function interpolateColor($size, $x, $z, $c1, $c2, $c3, $c4){
		$l1 = self::lerpColor($c1, $c2, $x / $size);
		$l2 = self::lerpColor($c3, $c4, $x / $size);

		return self::lerpColor($l1, $l2, $z / $size);
	}

	private static function lerpColor($a, $b, $s){
		$invs = 1 - $s;
		return [$a[0] * $invs + $b[0] * $s, $a[1] * $invs + $b[1] * $s, $a[2] * $invs + $b[2] * $s];
	}


	/**
	 * @return int (Red|Green|Blue)
	 */
	abstract public function getColor();
}
