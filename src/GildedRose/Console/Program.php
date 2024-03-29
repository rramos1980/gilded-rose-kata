<?php

namespace GildedRose\Console;

/**
 * Hi and welcome to team Gilded Rose.
 *
 * As you know, we are a small inn with a prime location in a prominent city
 * ran by a friendly innkeeper named Allison. We also buy and sell only the
 * finest goods. Unfortunately, our goods are constantly degrading in quality
 * as they approach their sell by date. We have a system in place that updates
 * our inventory for us. It was developed by a no-nonsense type named Leeroy,
 * who has moved on to new adventures. Your task is to add the new feature to
 * our system so that we can begin selling a new category of items. First an
 * introduction to our system:
 *
 * - All items have a SellIn value which denotes the number of days we have to sell the item
 * - All items have a Quality value which denotes how valuable the item is
 * - At the end of each day our system lowers both values for every item
 *
 * Pretty simple, right? Well this is where it gets interesting:
 *
 * - Once the sell by date has passed, Quality degrades twice as fast
 * - The Quality of an item is never negative
 * - "Aged Brie" actually increases in Quality the older it gets
 * - The Quality of an item is never more than 50
 * - "Sulfuras", being a legendary item, never has to be sold or decreases in Quality
 * - "Backstage passes", like aged brie, increases in Quality as it's SellIn
 *   value approaches; Quality increases by 2 when there are 10 days or less and
 *   by 3 when there are 5 days or less but Quality drops to 0 after the concert
 *
 * We have recently signed a supplier of conjured items. This requires an
 * update to our system:
 *
 * - "Conjured" items degrade in Quality twice as fast as normal items
 *
 * Feel free to make any changes to the UpdateQuality method and add any new
 * code as long as everything still works correctly. However, do not alter the
 * Item class or Items property as those belong to the goblin in the corner who
 * will insta-rage and one-shot you as he doesn't believe in shared code
 * ownership (you can make the UpdateQuality method and Items property static
 * if you like, we'll cover for you).
 *
 * Just for clarification, an item can never have its Quality increase above
 * 50, however "Sulfuras" is a legendary item and as such its Quality is 80 and
 * it never alters.
 */
class Program
{
    private $items = array();

    public static function main()
    {
        echo 'OMGHAI!' . PHP_EOL;

        $app = new Program([
            new Item(['name' => '+5 Dexterity Vest', 'sellIn' => 10, 'quality' => 20]),
            new Item(['name' => 'Aged Brie', "sellIn" => 2, 'quality' => 0]),
            new Item(['name' => 'Elixir of the Mongoose', 'sellIn' => 5, 'quality' => 7]),
            new Item(['name' => 'Sulfuras, Hand of Ragnaros', 'sellIn' => 0, 'quality' => 80]),
            new Item([
                'name'      => 'Backstage passes to a TAFKAL80ETC concert',
                'sellIn'    => 15,
                'quality'   => 20
            ]),
            new Item(array('name' => 'Conjured Mana Cake','sellIn' => 3,'quality' => 6)),
        ]);

        $app->UpdateQuality();

        echo sprintf('%50s - %7s - %7s', 'Name', 'SellIn', 'Quality') . PHP_EOL;
        foreach ($app->items as $item) {
            echo sprintf('%50s - %7d - %7d', $item->name, $item->sellIn, $item->quality) . PHP_EOL;
        }
    }

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function UpdateQuality()
    {
        foreach ($this->items as $currentItem) {
            $this->updateItemQuality($currentItem);
        }
    }

    private function updateItemQuality($currentItem)
    {
        $this->decreaseSellInDays($currentItem);

        if ($this->isItemThatIncreasesQualityWithTime($currentItem)) {
            $this->increaseItemQuality($currentItem);
        } else {
            $this->decreaseItemQuality($currentItem);
        }

        if ($this->isBackStagePass($currentItem)) {
            $this->handleBackStagePassQuality($currentItem);
        }

        if ($this->isItemInLastSellInDay($currentItem)) {
            $this->handleLastDayQuality($currentItem);
        }
    }

    private function isAgedBrie($currentItem)
    {
        return $currentItem->name == "Aged Brie";
    }

    private function isBackStagePass($currentItem)
    {
        return $currentItem->name == "Backstage passes to a TAFKAL80ETC concert";
    }

    private function isSulfurasHandOfRagnaros($currentItem)
    {
        return $currentItem->name == "Sulfuras, Hand of Ragnaros";
    }

    private function isConjuredItem($currentItem)
    {
        return $currentItem->name == "Conjured";
    }

    private function decreaseItemQuality($currentItem)
    {
        if ($this->canDecreaseQuality($currentItem)) {
            $currentItem->quality = $currentItem->quality - $this->getDecreaseRateForItem($currentItem);
        }
    }

    private function canDecreaseQuality($currentItem)
    {
        return $currentItem->quality > 0 && !$this->isSulfurasHandOfRagnaros($currentItem);
    }

    private function increaseItemQuality($currentItem)
    {
        if ($this->canIncreaseItemQuality($currentItem)) {
            $currentItem->quality = $currentItem->quality + 1;
        }
    }

    private function canIncreaseItemQuality($currentItem)
    {
        return $currentItem->quality < 50;
    }

    private function resetItemQuality($currentItem)
    {
        $currentItem->quality = 0;
    }

    private function isItemInLastSellInDay($currentItem)
    {
        return $currentItem->sellIn < 0;
    }

    private function decreaseSellInDays($currentItem)
    {
        if (!$this->isSulfurasHandOfRagnaros($currentItem)) {
            $currentItem->sellIn = $currentItem->sellIn - 1;
        }
    }

    private function isItemThatIncreasesQualityWithTime($currentItem)
    {
        return $this->isAgedBrie($currentItem) || $this->isBackStagePass($currentItem);
    }

    private function handleBackStagePassQuality($currentItem)
    {
        if ($currentItem->sellIn < 11) {
            $this->increaseItemQuality($currentItem);
        }
        if ($currentItem->sellIn < 6) {
            $this->increaseItemQuality($currentItem);
        }
    }

    private function handleLastDayQuality($currentItem)
    {
        if ($this->isAgedBrie($currentItem)) {
            $this->increaseItemQuality($currentItem);
            return;
        }

        if ($this->isBackStagePass($currentItem)) {
            $this->resetItemQuality($currentItem);
            return;
        }

        $this->decreaseItemQuality($currentItem);
    }

    private function getDecreaseRateForItem($currentItem)
    {
        $decreasedDayQuality = 1;

        if ($this->isConjuredItem($currentItem)) {
            $decreasedDayQuality = 2;
            return $decreasedDayQuality;
        }
        return $decreasedDayQuality;
    }
}
