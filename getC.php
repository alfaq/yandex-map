<?php

	$mysqli=new mysqli('localhost','root','','yandex_map');
	if(mysqli_connect_errno()){
		printf("Подключение к серверу MySQL невозможно. Код ошибки: %s\n",mysqli_connect_error());
	exit;
	}


	if($result=$mysqli->query('SELECT * from pos;')){
		while($row=$result->fetch_assoc()){
			$pos=askYandex($row["address"]);
			if(count($pos)>0){
				$mysqli->query("update pos set longitude = ".$pos[0].", latitude = ".$pos[1]." where id = ".$row["id"].";");
				echo $row["address"].' - '.$pos[0].' - '.$pos[1].'<br />';
			}
		}
		$result->close();
	}
	$mysqli->close();

	function askYandex($address){
		$params=array(
			'geocode'=>$address, // адрес
			'format'=>'json',    // формат ответа
			'results'=>1,        // количество выводимых результатов
		);
		$response=json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?'.http_build_query($params,'','&')));
		if($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found>0){
		   $pos=explode(" ",$response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
			return $pos;
		}
	}


	?>