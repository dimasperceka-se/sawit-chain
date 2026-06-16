<?php
function tanggal_indo($tanggal)
{
    $bulan = array (1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
}

function indonesian_date ($timestamp = '', $date_format = 'l, j F Y | H:i', $suffix = 'WIB') {
	if (trim ($timestamp) == '')
	{
		$timestamp = time ();
	}
	elseif (!ctype_digit ($timestamp))
	{
		$timestamp = strtotime ($timestamp);
	}
    # remove S (st,nd,rd,th) there are no such things in indonesia :p
	$date_format = preg_replace ("/S/", "", $date_format);
	$pattern = array (
		'/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
		'/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
		'/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
		'/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
		'/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
		'/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
		'/April/','/June/','/July/','/August/','/September/','/October/',
		'/November/','/December/',
		);
	$replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
		'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
		'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des',
		'Januari','Februari','Maret','April','Juni','Juli','Agustus','September',
		'Oktober','November','Desember',
		);
	$date = date ($date_format, $timestamp);
	$date = preg_replace ($pattern, $replace, $date);
	$date = "{$date} {$suffix}";
	return $date;
}

function romawi($angka){
	$hsl = "";
	if($angka<1||$angka>3999){
		$hsl = "Batas Angka 1 s/d 3999";
	}else{
		while($angka>=1000){
			$hsl .= "M";
			$angka -= 1000;
		}
		if($angka>=500){
			if($angka>500){
				if($angka>=900){
					$hsl .= "CM";
					$angka-=900;
				}else{
					$hsl .= "D";
					$angka-=500;
				}
			}
		}
		while($angka>=100){
			if($angka>=400){
				$hsl .= "CD";
				$angka-=400;
			}else{
				$angka-=100;
			}
		}
		if($angka>=50){
			if($angka>=90){
				$hsl .= "XC";
				$angka-=90;
			}else{
				$hsl .= "L";
				$angka-=50;
			}
		}
		while($angka>=10){
			if($angka>=40){
				$hsl .= "XL";
				$angka-=40;
			}else{
				$hsl .= "X";
				$angka-=10;
			}
		}
		if($angka>=5){
			if($angka==9){
				$hsl .= "IX";
				$angka-=9;
			}else{
				$hsl .= "V";
				$angka-=5;
			}
		}
		while($angka>=1){
			if($angka==4){
				$hsl .= "IV";
				$angka-=4;
			}else{
				$hsl .= "I";
				$angka-=1;
			}
		}
	}
	return ($hsl);
}

function convertdate($d)
{
    //dd-mm-yyyy -> yyyy-mm-dd
    $tgl = explode("-", $d);
    return $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
}

function CekValidDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function tanggal_dwibahasa($tanggal){
    if($_SESSION['language'] == "Indonesia"){
        $bulan = array (1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
    }else{
        $bulan = array (1 =>   'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            );
    }

    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
}