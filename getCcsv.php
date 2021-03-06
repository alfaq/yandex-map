﻿<?php
/*
**Init - Считываем адреса из файла и получаем координаты в выходной файл
*/
    function Init($from, $to){
        $file_path = $_SERVER['DOCUMENT_ROOT'].'/'.$from;//входной файл
        $file_to = $_SERVER['DOCUMENT_ROOT'].'/'.$to;//выходной файл
        
        if(!file_exists($file_to)){//если файла с координатами не существует, создадим его
            $address = array();
            $aCoord = array();
            
            //если существует входной файл с адресами
            if (file_exists($file_path)){
                $content = file($file_path);
                if (!empty($content)){
                       foreach ($content as $str){
                            $str = trim($str);
                            $temp = explode(";", $str);
                                if(!empty($temp))
                                    $address[] = $temp[0];//получаем массив адресов из первой ячейки csv файла
                        }
                }
            }
            
            //вызываем функцию получения координат по строке адреса
            $file = fopen($file_to, "w");
            foreach($address as $adr){
                $coord = askYandex($adr);
                if(!empty($coord[0]) && !EMPTY($coord[1])){
                    fwrite($file, $adr.'; '.$coord[1] .', '. $coord[0] . PHP_EOL);
                    
                    //echo $adr.' - ['.$coord[0] .', '. $coord[1].']<br />';
                }
            }
            fclose($file);
        }
        //вызываем функцию вывода координат из выходного файла
        $ars = getCoord($file_to);
        foreach($ars as $ar){
            echo $ar.', ';
        }  
    }
    
/*
**getCoord - Функия для формирования координат в JS
*/
    function getCoord($file){
        $content = file($file);

        if (empty($content))
            return array();

        $aCoord = array();

        foreach ($content as $str) {
            $temp = explode(";", $str);
            if (!empty($temp))
                $aCoord[] = '['.trim($temp[1]).']';
        }
            
        return $aCoord;
    }

/*
**getAddr - Функция получения исходного адреса из файла для балуна
*/    
    function getAddr($file){
        $aAddr = array();
        if(!file_exists($file_to)){
            $content = file($file);
            if (empty($content))
                return array();
    
            foreach ($content as $str) {
                $temp = explode(";", $str);
                if (!empty($temp))
                    $aAddr[] = trim($temp[0]);
            }
        }    
        return $aAddr;
    }

/*
**askYandex - запрос координат по адресу
*/
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