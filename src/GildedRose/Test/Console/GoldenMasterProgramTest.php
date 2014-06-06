<?php

namespace GildedRose\Test\Console;

use GildedRose\Console\Item;
use GildedRose\Console\Program;
use PHPUnit_Framework_TestCase;

class GoldenMasterProgramTest extends PHPUnit_Framework_TestCase
{
    public function generateGoldenMaster()
    {
        $itemDescriptors = $this->generateRandomDescriptors();
        $content = $this->generateGoldenMasterOutputContent($itemDescriptors);

        file_put_contents('items.serialized', serialize($itemDescriptors));
        file_put_contents('output.test', $content);
    }

    /**
     * @test
     */
    public function compareWithGoldenMaster()
    {
        $itemDescriptors = unserialize(file_get_contents('items.serialized'));
        $content = $this->generateGoldenMasterOutputContent($itemDescriptors);
        $goldenMaster = file_get_contents('output.test');

        $this->assertSame(md5($goldenMaster), md5($content));
    }

    private function generateGoldenMasterOutputContent($itemDescriptors)
    {
        ob_start();

        $items = [];
        foreach ($itemDescriptors as $itemDescriptor) {
            $items[] = new Item($itemDescriptor);
        }

        $app = new Program($items);
        $app->UpdateQuality();

        echo sprintf('%50s - %7s - %7s', 'Name', 'SellIn', 'Quality') . PHP_EOL;
        foreach ($items as $item) {
            echo sprintf('%50s - %7d - %7d', $item->name, $item->sellIn, $item->quality) . PHP_EOL;
        }

        return ob_get_contents();
    }

    private function generateRandomDescriptors()
    {
        $itemTypes = [
            '+5 Dexterity Vest',
            'Aged Brie',
            'Elixir of the Mongoose',
            'Sulfuras, Hand of Ragnaros',
            'Backstage passes to a TAFKAL80ETC concert'
        ];

        $numberOfRandomItemsPerType = 100;
        $itemDescriptors = [];
        foreach ($itemTypes as $itemType) {
            for ($i = 0; $i < $numberOfRandomItemsPerType; $i++) {
                $itemDescriptors[] = ['name' => $itemType, 'sellIn' => rand(0, 100), 'quality' => rand(0, 100)];
            }
        }
        return $itemDescriptors;
    }
}
