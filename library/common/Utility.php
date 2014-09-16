<?php

namespace common;

use Zend\Crypt\BlockCipher;

class Utility {
	public static function getGUID(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
				.substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12)
				.chr(125);// "}"
			return $uuid;
		}
	}

	public static function getKeyPair(){

		$config = array(
		    "digest_alg" => "sha512",
		    "private_key_bits" => 384,
		    "private_key_type" => OPENSSL_KEYTYPE_RSA,
		);
		// Create private and public key
		$res = openssl_pkey_new($config);

		// Extract private key
		openssl_pkey_export($res, $privKey);

		// Extract public key from $res to $pubKey		
		$pubKey = openssl_pkey_get_details($res);
		$pubKey = $pubKey["key"];

		$keyPair = array(
				'private_key'=> $privKey,
				'public_key' => $pubKey,
		);

		return $keyPair;

		//$data = 'plaintext data goes here';

		// Encrypt the data to $encrypted using the public key
		//openssl_public_encrypt($data, $encrypted, $pubKey);

		// Decrypt the data using the private key and store the results in $decrypted
		//openssl_private_decrypt($encrypted, $decrypted, $privKey);

		//echo $decrypted;
	}
	
	public static function signByHashHMAC($data,$key){
		$Sig = base64_encode(hash_hmac('sha256', $data, $key, true));
		return $Sig;
	}
   
   	public static function getSignedToken($dataToBeSigned,$key)
	{		
		$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$blockCipher->setKey($key);
		$result = $blockCipher->encrypt($dataToBeSigned);
	    return $result;
	}

	public static function decryptSignedToken($token,$key)
	{	
		$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$blockCipher->setKey($key);
		$result = $blockCipher->decrypt($token);
		return $result;
	}

	public static function GetLatLongFromAddress($url){
	
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$geocodeinformation=json_decode($result, true);	
		return $geocodeinformation;
		
	}

	public static function GetAddressFromLatLong($lat, $lng){
		$url="https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng ."&sensor=false&key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc";	
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$addressinformation=json_decode($result, true);	
		return $addressinformation;
		
	}

	public static function GetNearbyPlaces($location){
		$url="https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc&location=" . $location['lat'] . "," . $location['lng'] . "&rankby=distance&types=museum|art_gallery&sensor=false";
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$nearbyplaces=json_decode($result, true);	
		return $nearbyplaces;
		
	}

	public static function GetNearbyPlaces2($lat, $lng){
		$url="https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc&location=" . $lat . "," . $lng . "&rankby=distance&types=museum|art_gallery&sensor=false";
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$nearbyplaces=json_decode($result, true);	
		return $nearbyplaces;
		
	}

	/* USES MQL QUERY */
	public static function LookupAirPortCode($mqlAirportCodeByCity){	
		$url="https://www.googleapis.com/freebase/v1/mqlread?query=".$mqlAirportCodeByCity."&cursor";
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$airports=json_decode($result, true);	
		return $airports;
		
	}

	/* USES Factual DataSet */
	public static function GetHotelInformation($filterHotel){	
		//$url="http://api.v3.factual.com/t/hotels-v3/?filters=".$filterHotel."&limit=5"."&KEY=WZSCSjKmBO3WqBIHfF9dyVhFxpTwsQPzPwKNmQC2";//for ivnavin@hotmail.com
		$url="http://api.v3.factual.com/t/hotels-v3/?filters=".$filterHotel."&limit=5"."&KEY=Df1MSFhBOsXpB40LMJG8yiASAH15B9uZFY66CUzz"; //for ivnavin@gmail.com

		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HEADER, FALSE);	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		
		$airports=json_decode($result, true);	
		return $airports;
		
	}

	//Factual OAuth Key: WZSCSjKmBO3WqBIHfF9dyVhFxpTwsQPzPwKNmQC2, OAuth Secret: 3D2IynKT827FIwlnuGJx52EVZzs6tnRMDO57LEwf
	//http://www.factual.com/data/t/hotels-v3/?filters={"name":{"$bw":"sher"}}&geo={"$locality":{"$bw":"Orlando"}}
	//http://api.v3.factual.com/t/hotels-v3/?filters={"name":{"$bw":"sher"},"locality":{"$bw":"Orlando"}}&KEY=WZSCSjKmBO3WqBIHfF9dyVhFxpTwsQPzPwKNmQC2

	/* get cc information */
	public static function GetCCInformation($token){	
		$url="https://core.spreedly.com/v1/payment_methods/".$token.".xml";
		
		$curl_session=curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_HTTPHEADER, array('Content-Type: application/xml',
	                                            'Connection: Keep-Alive'));	
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_session, CURLOPT_USERPWD, '5MJE9cyJDLXygGYWESsg99l69lu:Rgl2wqBfZJ4d4hPR0iU7H49H3zjO4rI7g8m5a6zMv5ghmushZZrGjKBvJtSLEC4V');
		$result=curl_exec($curl_session);
		curl_close($curl_session);
		$xmlResult = new SimpleXMLElement($result);
		$payment_info['card_type']=(string)$xmlResult->card_type[0];
		$payment_info['card_number']=(string)$xmlResult->number[0];
		return $payment_info;
		
	}
}

?>