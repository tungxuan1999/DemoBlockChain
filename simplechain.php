<?php
require_once("./exchange.php");
/*
Set up a simple chain and mine two blocks.
*/

header("Content-Type: application/json");

//url: https://emergingtechnologies-4e146.firebaseio.com/.json
$testCoin = new Exchange();
// //Create account
// $a = $testCoin->createAccount('Hello');

// //Recharge
// $a = $testCoin->rechargeAccount('Hello',2000);

//TransferAccount
$a = $testCoin->transferAccount('Hello','Hihi',1000);

echo json_encode($a);