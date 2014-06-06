<?php

namespace GildedRose\Test\Console;

use GildedRose\Console\Item;
use GildedRose\Console\Program;
use PHPUnit_Framework_TestCase;

class ProgramTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider itemDescriptionProvider
     */
    public function it_should_decrease_quality_of_item_at_end_of_day($itemProperties, $expectedSellIn, $expectedQuality)
    {
        $this->checkExpectationsForProperties($itemProperties, $expectedSellIn, $expectedQuality);
    }

    /**
     * @test
     */
    public function a_quality_item_is_never_more_than_50()
    {
        $itemProperties = ['name' => 'Aged Brie', "sellIn" => 2, 'quality' => 50];
        $this->checkExpectationsForProperties($itemProperties, 1, 50);
    }

    /**
     * @test
     */
    public function expected_sell_date_passed_quality_degrades_twice_as_fast()
    {
        $itemProperties = ['name' => '+5 Dexterity Vest', 'sellIn' => -1, 'quality' => 20];
        $this->checkExpectationsForProperties($itemProperties, -2, 18);
    }


    /**
     * @test
     */
    public function conjured_item_quality_degrades_twice_faster()
    {
        $itemProperties = ['name' => 'Conjured', "sellIn" => 2, 'quality' => 50];
        $this->checkExpectationsForProperties($itemProperties, 1, 48);
    }

    //"Backstage passes" increases in Quality as it's SellIn value approaches; Quality increases by 2 when there are 10 days or less and by 3 when there are 5 days or less but Quality drops to 0 after the concert

//    public function backstage_passes_increase_qu

   private function checkExpectationsForProperties($itemProperties, $expectedSellIn, $expectedQuality)
   {
        $item = new Item($itemProperties);
        $program = new Program([$item]);
        $program->UpdateQuality();
        $this->assertEquals($expectedQuality, $item->quality);
        $this->assertEquals($expectedSellIn, $item->sellIn);
   }

    public function itemDescriptionProvider()
    {
        return [
                [['name' => '+5 Dexterity Vest', 'sellIn' => 10, 'quality' => 20], 9, 19],
                [['name' => 'Aged Brie', "sellIn" => 2, 'quality' => 0], 1, 1],
                [['name' => 'Elixir of the Mongoose', 'sellIn' => 5, 'quality' => 7], 4, 6],
                [['name' => 'Sulfuras, Hand of Ragnaros', 'sellIn' => 0, 'quality' => 80], 0, 80],
                [['name' => 'Backstage passes to a TAFKAL80ETC concert', 'sellIn' => 15,'quality'   => 20], 14, 21],
                [['name' => 'Conjured Mana Cake','sellIn' => 3,'quality' => 6], 2, 5]
        ];
    }
}