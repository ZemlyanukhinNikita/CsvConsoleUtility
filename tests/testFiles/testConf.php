<?php
return [
    1 => 1, // faker
    2 => null, // set value to null
    3 => function ($value, $rowData, $rowIndex, $faker) {
        if ($value == 23) {
            return $rowIndex + 5;
        }
        if ($rowData[0] == 101) {
            return 0;
        }
        return 2;
    },
];
