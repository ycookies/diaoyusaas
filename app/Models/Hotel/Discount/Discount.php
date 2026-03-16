<?php

namespace App\Models\Hotel\Discount;

interface Discount {

    public function getAmount();

    public function getDescription();
}