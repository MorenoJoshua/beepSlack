<?php

class Dist
{

    /**
     * Para calculo de distancia mas cercana por la superficie de una esfera del tamaño de la tierra.
     * Solo existe la funcion calc()
     */
    public function __construct()
    {

    }
    /**
     * Para calculo de distancia mas cercana por la superficie de una esfera del tamaño de la tierra
     * @param $lat1: Latitud de grupo 1 de coordenadas
     * @param $lng1: Longitud de grupo 1 de coordenadas
     * @param $lat2: Latitud de grupo 2 de coordenadas
     * @param $lng2: Longitud de grupo 2 de coordenadas
     * @return float distancia en metros.fraccion
     */
    public function calc($lat1, $lng1, $lat2, $lng2)
    {
        $radioDeTierra = 12756.274 / 2; // en km
        $radLat1 = $lat1 * pi() / 180;
        $radLat2 = $lat2 * pi() / 180;
        $radLng1 = $lng1 * pi() / 180;
        $radLng2 = $lng2 * pi() / 180;
        $dLat = $radLat2 - $radLat1;
        $dLng = $radLng2 - $radLng1;
        $norm = sin($dLat / 2) * sin($dLat / 2) +
            cos($radLat1) * cos($radLat2) *
            sin($dLng / 2) * sin($dLng / 2);
        $atanNorm = 2 * atan2(sqrt($norm), sqrt(1 - $norm));
        $dKm = $radioDeTierra * $atanNorm;
        return $dKm * 1000; // en metros (1000m x 1km)
    }
}