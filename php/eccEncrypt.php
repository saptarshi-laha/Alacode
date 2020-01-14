<?php
define('P',gmp_sub(gmp_pow(2,521),1));
define('N','6864797660130609714981900799081393217269435300143305409394463459185543183397655394245057746333217197532963996371363321113864768612440380340372808892707005449');
define('A',-3);
define('B',gmp_init('0x051953eb9618e1c9a1f929a21a0b68540eea2da725b99b315f3b8b489918ef109e156193951ec7e937b1652c0bd3bb1bf073573df883d2c34f1ef451fd46b503f00',16));
define('Gx',gmp_init('0xc6858e06b70404e9cd9e3ecb662395b4429c648139053fb521f828af606b4d3dbaa14b5e77efe75928fe1dc127a2ffa8de3348b3c1856a429bf97e7e31c2e5bd66',16));
define('Gy',gmp_init('0x11839296a789a3bc0045c8a5fb42c7d1bd998f54449579b446817afbd17273e662c97ee72995ef42640c550b9013fad0761353c7086a272c24088be94769fd16650',16));
define('STOREBASE',62);

class eccEncrypt{

private static function Osszead($x1,$y1,$x2,$y2) {
	$i=gmp_mod(gmp_mul(gmp_mod(gmp_sub($y2,$y1),P),gmp_invert(gmp_sub($x2,$x1),P)),P);
	$x3=gmp_mod(gmp_sub(gmp_sub(gmp_pow($i,2),$x1),$x2),P);
	$y3=gmp_mod(gmp_sub(gmp_mul($i,gmp_sub($x1,$x3)),$y1),P);
	return(array($x3,$y3));
}

private static function Duplaz($x1,$y1) {
	$i=gmp_mod(gmp_mul(gmp_mod(gmp_add(gmp_mul(gmp_pow($x1,2),3),A),P),gmp_invert(gmp_mul($y1,2),P)),P);
	$x3=gmp_mod(gmp_sub(gmp_pow($i,2),gmp_mul($x1,2)),P);
	$y3=gmp_mod(gmp_sub(gmp_mul($i,gmp_sub($x1,$x3)),$y1),P);
	return(array($x3,$y3));
}

private static function Szoroz($kx,$ky,$s) {
	$bitek=gmp_strval($s,2);
	$hany=strlen($bitek)-1;
	$x=$kx;
	$y=$ky;
	for ($i=0;$i<=$hany;$i++) {
		if (($i!=0)&&($bitek{$i}=='1')) {
			$hozzaad=self::Osszead($x,$y,$kx,$ky);
			$x=$hozzaad[0];
			$y=$hozzaad[1];
		}
		if ($i!=$hany) {
			$dupla=self::Duplaz($x,$y);
			$x=$dupla[0];
			$y=$dupla[1];
		}
	}
	return(array($x,$y));
}

private static function GetRight($x) {
	return(gmp_mod(gmp_add(gmp_add(gmp_pow($x,3),gmp_mul($x,A)),B),P));
}

private static function GetY($x) {
	$y=gmp_powm(self::GetRight($x),gmp_divexact(gmp_add(P,1),4),P);
	return($y);
}

private static function rnd($hatar) {
	$random=gmp_strval(gmp_random());
	$small_rand=rand();
	while (gmp_cmp($random,$hatar)>0) {
		$random=gmp_div($random,$small_rand,GMP_ROUND_ZERO);
	}
	return(gmp_strval($random));
}

public static function run($argv){
switch ($argv[0]) {
	case 'e':
	$message = $argv[1];
		do {
			$d=self::rnd(gmp_sub(N,1));
			$Q=self::Szoroz(Gx,Gy,$d);
		} while (gmp_cmp(self::GetY($Q[0]),$Q[1])!=0);
		$sec=gmp_strval($d,STOREBASE);
		$pub=gmp_strval($Q[0],STOREBASE);
					do {
							$k=self::rnd(gmp_sub(N,1));
							$kG=self::Szoroz(Gx,Gy,$k);
						} while (gmp_cmp(self::GetY($kG[0]),$kG[1])!=0);
						$bGx=gmp_init($pub,STOREBASE);
						$kbG=self::Szoroz($bGx,self::GetY($bGx),$k);
						do {
							do {
								$x=self::rnd(gmp_sub(N,1));
							} while (gmp_legendre(self::GetRight($x),P)!=1);
							$y=self::GetY($x);
							$M_kbG=self::Osszead($kbG[0],$kbG[1],$x,$y);
						} while (gmp_cmp(self::GetY($M_kbG[0]),$M_kbG[1])!=0);
						$keyfile=gmp_strval($kG[0],STOREBASE).'x$__..__$x'.gmp_strval($M_kbG[0],STOREBASE);
						$crypt_key=hash('sha256',gmp_strval($x,STOREBASE),TRUE);
						$crypt_iv=substr(hash('sha256',gmp_strval($y,STOREBASE),TRUE), 0, 16);
						$encrypted = openssl_encrypt($message,'aes256',$crypt_key,0,$crypt_iv);
						return $encrypted.'$$.__.$$'.$sec.'$$.__.$$'.$keyfile;
	break;
	case 'd':
	if(strpos($argv[1], '$$.__.$$') != false && strpos($argv[1], 'x$__..__$x') != false){
							$d=gmp_init(explode('$$.__.$$',$argv[1])[1],STOREBASE);
							$key=explode('x$__..__$x',explode('$$.__.$$',$argv[1])[2]);
							$kGx=gmp_init($key[0],STOREBASE);
							$dkG=self::Szoroz($kGx,self::GetY($kGx),$d);
							$M_kbGx=gmp_init($key[1],STOREBASE);
							$M=self::Osszead($M_kbGx,self::GetY($M_kbGx),$dkG[0],gmp_mod(gmp_neg($dkG[1]),P));
							$crypt_key=hash('sha256',gmp_strval($M[0],STOREBASE),TRUE);
							$crypt_iv=substr(hash('sha256',gmp_strval($M[1],STOREBASE),TRUE), 0, 16);
							$message = openssl_decrypt(explode('$$.__.$$',$argv[1])[0],'aes256',$crypt_key,0,$crypt_iv);
							return $message;
						}
	break;
	default:
		header("Location: ../index.php");
	break;
	}
}

}

?>
