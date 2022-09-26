<?php
function set_active($uri, $output = 'active'){
    if( is_array($uri) ) {
        foreach ($uri as $u) {
            if (Route::is($u)) {
                return $output;
            }
        }
    } else {
        if (Route::is($uri)){
            return $output;
        }
    }
}
function formatRupiah($num){
    $hasil_rupiah = number_format($num,2,',','.');
	return $hasil_rupiah;
}
?>
