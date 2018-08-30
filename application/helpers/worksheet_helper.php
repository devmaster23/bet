<?php 
  defined('BASEPATH') OR exit('No direct script access allowed');
  if ( ! function_exists('getBetArr')){
    function getBetArr($worksheet)
    {
        $bets = [];
        if(isset($worksheet['data']['rr']))
        {
            $bets = array_merge($bets, $worksheet['data']['rr']);
        }

        if(isset($worksheet['data']['crr']))
        {
            $bets = array_merge($bets, $worksheet['data']['crr']);
        }

        if(isset($worksheet['data']['parlay']))
        {
            $bets = array_merge($bets, $worksheet['data']['parlay']);
        }

        if(isset($worksheet['data']['cparlay']))
        {
            $bets = array_merge($bets, $worksheet['data']['cparlay']);
        }

        if(isset($worksheet['data']['single']))
        {
            $bets = array_merge($bets, $worksheet['data']['single']);
        }
        return $bets;
    }
  }

  if ( ! function_exists('roundBetAmount')){
    function roundBetAmount($number)
    {
        $time =1;
        if( $number < 10 )
        {
            $time = 1;
        }else if( $number < 100 ){
            $time = 5;
        }else if( $number < 250 ){
            $time = 10;
        }else if( $number < 1000 ){
            $time = 25;
        }else if( $number < 2500 ){
            $time = 50;
        }else{
            $time = 100;
        }
        $tmp = round($number / $time , 0);
        $result = $tmp * $time;
        return $result;
    }
  }
?>