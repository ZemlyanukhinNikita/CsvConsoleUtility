<?php

return [
    1 => 'chtoETOtakoe', // faker
    2 => null, // set value to null
    3 => function ($value, $rowData, $rowIndex, $faker) {
        return 'привет';
    },
    4 => function ($value, $rowData, $rowIndex, $faker) {
        if ($value == 23) {
            return 1;
        }
        if ($rowData[0] == 101) {
            return 0;
        }
        return 2;
    },
];

