<?php
class tools{

    public function __construct(){
    }

    public function check_time($time, $request){
        if($request == 'since'){
            $theTime = time() - $time;
        } elseif($request == 'until'){
            $theTime = $time - time();
        }

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            //1 => 'second'
        );

        foreach($tokens as $unit => $text){
            if($theTime < $unit) continue;
            $duration = floor($theTime / $unit);
            return $duration.' '.$text.(($duration>1)?'s':'');
        }
        
        return '0 minute';
    }
}// EoF tools class

$tools = new tools();

//print_r($tools->check_time('2012-08-22 20:11:20', 'since'));
//print_r($tools->check_time('2019-11-27 23:59:20', 'until'));
// 1574853986
print_r($tools->check_time(1574854786, 'until'));