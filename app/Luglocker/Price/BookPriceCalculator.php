<?php

namespace App\Luglocker\Price;

use App\Models\Locker;
use App\Models\Settings;

trait BookPriceCalculator
{
    /**
     * @var array
     */
    private $calculatedPrice = [
        'total' => 0,
        'per_hours' => 0,
        "hours" => 0,
        'per_days' => 0,
        "days" => 0,
        "package" => []
    ];

    private $itemCount = 1;
    /**
     * @param $price
     * @param $business_currency
     * @param string $user_currency
     * @param string $admin_currency
     * @param bool $format
     * @return float|int|string
     */
    public static function currencyChangeFromUser($price, $business_currency, $user_currency = "EUR", $admin_currency = "AMD", $format = true)
    {
        $price = (float)$price;
        $currencyArr = [];
        $currency_get = Settings::query()->where('key', 'currency')->first();
        $currency_change = json_decode($currency_get->value);
        foreach ($currency_change as $key => $value) {
            $currencyArr[$key] = $value;
        }
        if ($business_currency !== 'AMD') {
            $businessToAMD = $currencyArr[$business_currency] * $price;
        } else {
            $businessToAMD = $price;
        }
        if ($admin_currency) {
            return $businessToAMD;
        }
        if ($user_currency == 'AMD') {
            $AMDToUser = $businessToAMD;
        } else {
            $AMDToUser = $businessToAMD / $currencyArr[$user_currency];
        }

        if ($format)
            return number_format($AMDToUser, 2);
        else
       return $AMDToUser;

    }

    /**
     * @param Locker $locker
     * @param string $start
     * @param string $end
     * @return array
     */
    public function calculatePrice(Locker $locker, string $start, string $end, int $count = 1): array
    {
        $this->itemCount = $count;
        $diff = strtotime($end) - strtotime($start);
        $hours = ceil($diff / 3600);
        $lockerArray = $locker->toArray();
        $this->calculate($hours, $lockerArray);

        return $this->calculatedPrice;
    }

    /**
     * @param Locker $locker
     * @param int $hours
     * @return array
     */
    public function calculatePriceWithHours(Locker $locker, int $hours): array
    {
        $lockerArray = $locker->toArray();

        $this->calculate($hours, $lockerArray);

        return $this->calculatedPrice;
    }

    /**
     * @param $hours
     * @param $locker
     */
    private function calculate($hours, $locker): void
    {
        if (count($locker['prices']) && ($locker['price_per_day'] || $locker['price_per_hour'])) {

            $this->calculateWithAllPrices($locker['prices'], $hours, $locker['price_per_day'], $locker['price_per_hour']);

        } elseif (count($locker['prices'])) {

            $this->calculateWithOutPerDayAndPerHours($locker['prices'], $hours);
        } else {

            $this->calculateWithOutPackage($locker['price_per_day'], $locker['price_per_hour'], $hours);
        }
    }

    private function calculateWithAllPrices($prices, $hours, $price_per_day, $price_per_hour)
    {
        $price = $this->getRange($prices, $hours);
        if (count($price)) {
            $this->amountRange($price, $hours);
        } else {
            $biggestRange = end($prices);
            $this->amountRange($biggestRange, $hours);
            $otherHours = round($hours - $biggestRange['range_end']);
            $this->calculateWithOutPackage($price_per_day, $price_per_hour, $otherHours);
        }
    }

    /**
     * @param $price_per_day
     * @param $price_per_hour
     * @param $hours
     */
    private function calculateWithOutPackage($price_per_day, $price_per_hour, $hours): void
    {
        if ($price_per_day && $price_per_hour) {
            $days = floor($hours / 24);

            if ($days > 0) {

                $this->amountPerDay($price_per_day, $days);

                $otherHours = $hours - ($days * 24);

                if ($otherHours > 0) {
                    $this->amountPerHour($price_per_hour, $otherHours);
                }
            } else {

                $this->amountPerHour($price_per_hour, $hours);
            }

        } elseif ($price_per_hour) {

            $this->amountPerHour($price_per_hour, $hours);
        } elseif ($price_per_day) {

            $days = ceil($hours / 24);
            $this->amountPerDay($price_per_day, $days);
        }
    }

    /**
     * @param array $prices
     * @param int $hours
     */
    private function calculateWithOutPerDayAndPerHours(array $prices, int $hours): void
    {
        $price = $this->getRange($prices, $hours);
        if (count($price)) {

            $this->amountRange($price, $hours);
        } else {

            $biggestRange = end($prices);

            $this->amountRange($biggestRange, $hours);
            $otherHours = round($hours - $biggestRange['range_end']);

            $this->calculateWithOutPerDayAndPerHours($prices, $otherHours);
        }
    }

    /**
     * @param array $prices
     * @param float $hours
     * @return array
     */
    private function getRange(array $prices, float $hours): array
    {
        foreach ($prices as $price) {

            if ($price['range_start'] < $hours && $price['range_end'] >= $hours) {

                return $price;
            }
        }
        return [];
    }

    /**
     * @param float $price
     * @param int $hours
     */
    private function amountPerHour(float $price, int $hours): void
    {
        $amount = round($hours * $price * $this->itemCount, 2);

        $this->calculatedPrice['total'] = $this->calculatedPrice['total'] + $amount;
        $this->calculatedPrice['per_hours'] = $this->calculatedPrice['per_hours'] + $amount;
        $this->calculatedPrice['hours'] = $this->calculatedPrice['hours'] + $hours;
    }

    /**
     * @param float $price
     * @param int $days
     */
    private function amountPerDay(float $price, int $days): void
    {
        $amount = round($days * $price * $this->itemCount, 2);

        $this->calculatedPrice['total'] = $this->calculatedPrice['total'] + $amount;
        $this->calculatedPrice['per_days'] = $this->calculatedPrice['per_days'] + $amount;
        $this->calculatedPrice['days'] = $this->calculatedPrice['days'] + $days;
    }

    /**
     * @param array $price
     * @param int $hours
     * @return bool
     */
    private function amountRange(array $price, int $hours): bool
    {
//        ATTENTION:: REMOVED AFTER month testing
       foreach ($this->calculatedPrice['package'] as $key => $package) {
           if ($package['id'] === $price['id']) {
               $this->calculatedPrice['package'][$key]['count']++;
               $this->calculatedPrice['total'] = $this->calculatedPrice['total'] + $price['price'];

               return false;
           }
       }
        // ********************************************************************
        $package = array_merge($price, ['count' => $this->itemCount]);
        $this->calculatedPrice['total'] = $this->calculatedPrice['total'] + $price['price'] * $this->itemCount;

        array_push($this->calculatedPrice['package'], $package);
        return true;
    }
}
