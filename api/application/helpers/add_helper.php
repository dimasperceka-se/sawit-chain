<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function SetTanggal($tanggal) {
   $bln=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus',
     'September','Oktober','November','Desember');
   $tlen = sizeof($bln);
   $tgl=explode('-',$tanggal);
   for ($t=0;$t<$tlen;$t++) {
      if ($tgl[1]==$t+1) $tgl[1]=$bln[$t];
   }
   $tanggal=$tgl[2].' '.$tgl[1].' '.$tgl[0];
   if ($tanggal=='00 00 0000') $tanggal = '';
   return $tanggal;
}
function terbilang($satuan){
   $huruf = array ("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh","sebelas");
   if ($satuan < 12)
      return " ".$huruf[$satuan];
   elseif ($satuan < 20)
      return terbilang($satuan - 10)." belas";
   elseif ($satuan < 100)
      return terbilang($satuan / 10)." puluh".terbilang($satuan % 10);
   elseif ($satuan < 200)
      return "seratus".terbilang($satuan - 100);
   elseif ($satuan < 1000)
      return terbilang($satuan / 100)." ratus".terbilang($satuan % 100);
   elseif ($satuan < 2000)
      return "seribu".terbilang($satuan - 1000); 
   elseif ($satuan < 1000000)
      return terbilang($satuan / 1000)." ribu".terbilang($satuan % 1000); 
   elseif ($satuan < 1000000000)
      return terbilang($satuan / 1000000)." juta".terbilang($satuan % 1000000); 
   elseif ($satuan >= 1000000000)
      echo "Angka yang Anda masukkan terlalu besar";
}

if ( ! function_exists('number_to_words'))
{
	function number_to_words($number)
	{
		$before_comma = trim(to_word($number));
		$after_comma = trim(comma($number));
		$koma = explode(',',$number);
		return ucwords($results = $before_comma.($koma[1]<1?'':' koma '.$after_comma));
	}

	function to_word($number)
	{
		$words = "";
		$arr_number = array(
		"",
		"satu",
		"dua",
		"tiga",
		"empat",
		"lima",
		"enam",
		"tujuh",
		"delapan",
		"sembilan",
		"sepuluh",
		"sebelas");

		if($number<12)
		{
			$words = " ".$arr_number[$number];
		}
		else if($number<20)
		{
			$words = to_word($number-10)." belas";
		}
		else if($number<100)
		{
			$words = to_word($number/10)." puluh ".to_word($number%10);
		}
		else if($number<200)
		{
			$words = "seratus ".to_word($number-100);
		}
		else if($number<1000)
		{
			$words = to_word($number/100)." ratus ".to_word($number%100);
		}
		else if($number<2000)
		{
			$words = "seribu ".to_word($number-1000);
		}
		else if($number<1000000)
		{
			$words = to_word($number/1000)." ribu ".to_word($number%1000);
		}
		else if($number<1000000000)
		{
			$words = to_word($number/1000000)." juta ".to_word($number%1000000);
		}
		else
		{
			$words = "undefined";
		}
		return $words;
	}

	function comma($number)
	{
		$after_comma = stristr($number,',');
		$arr_number = array(
		"nol",
		"satu",
		"dua",
		"tiga",
		"empat",
		"lima",
		"enam",
		"tujuh",
		"delapan",
		"sembilan");

		$results = "";
		$length = strlen($after_comma);
		$i = 1;
		while($i<$length)
		{
			$get = substr($after_comma,$i,1);
			$results .= " ".$arr_number[$get];
			$i++;
		}
		return $results;
	}
}
