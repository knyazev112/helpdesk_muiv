<?php

class time {
    public function ago($time) {
        $periods = array("с.", "мин.", "ч.", "д.", "нед.", "мес.", "г.");
        $lengths = array("60","60","24","7","4.35","12");
        $now = time();

        $difference     = $now - $time;
      
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
        
        $difference = round($difference);      
        return $difference . " " . $periods[$j] . " назад";
    }     
}