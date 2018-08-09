<?php

return [
    1 => 'streetName', // faker
    2 => null, // set value to null
    3 => function ($value, $rowData, $rowIndex, $faker) {
        return 1;
    },
    4 => function ($value, $rowData, $rowIndex, $faker) {
        if ($value == 23) {
            return $rowIndex + 5;
        }
        if ($rowData[0] == 101) {
            return 0;
        }
        return $faker->randomDigit;
    },
    100 => 1
];

