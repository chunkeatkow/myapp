<?php
function generateRandomNo() {
    // Available alpha caracters
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(10000, 99999)
        . mt_rand(10000, 99999)
        . $characters[rand(0, strlen($characters) - 1)];

    // shuffle the result
    $string = str_shuffle($pin);

    return $string;
}
?>
