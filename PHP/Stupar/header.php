<?php

require_once "./core/config.php";
require_once CORE_INCLUDE_PATH  . "/generic/PrimitiveUtils.php";
require_once CORE_DATABASE_PATH . "/Database.php";
require_once CORE_DATABASE_PATH . "/Database.logger_system.php";

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

    <!--  template css -->
    <link href="/templates/default/assets/style.css" rel="stylesheet">

    <title>Vizualizare stupar</title>
</head>

<body class="container">

<svg id="svg1" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" class="img-responsive" width="100%" height="100%" version="1.1" >
    <g  id="bee" transform="translate(1400,100)">
        <g transform="matrix(0.26458,0,0,0.26458,-10.137,-3.944)">
            <path d="m108.9 137.3c0-0.3 1.5-2.6 5.2-3.1 15.7-2.2 29.9 28.8 28.9 30.3-1 1.5-8.5 8.6-12.1 9.5-3.6 0.8-7.2 3.2-8.8 3.2-1.7-0.1-7.1 0.5-7.3-0.7-0.1-1.2-2.7-15.4-2.7-20 0-4.6-0.2-13.2-0.9-14.6-0.7-1.4-2.3-4.5-2.3-4.5z" fill="#fff"/>
            <path d="m115.6 138.6c-3.2-3.6-9.8-6.9-18.8-4.6-9 2.3-24.6 13.4-24 31.9 0.5 18.6 10.7 29.8 13.7 32.2 1.8 1.5 3.6-1.1 6.2-4 0 0 0 0 0 0 0.7-0.7 3.9-4.8 5.7-6.7 3.9-4.2 8.9-15.8 8.9-15.8 0 0 0.1 7-5 16.4 7.5-4.8 19.8-15.8 21.2-24.8 2.2-13.6-4.8-21.1-7.9-24.6z"/>
            <path d="m143 137.4c0 0 15.9-0.8 19.4 0 3.4 0.8 17.5 2.8 26.8 17.8 7 11.2 5.3 29.7 3.2 35.2-2 5.4-12.6 20.3-12.6 20.3l-18.8-11.8c-1.2-0.4-9.4-6.4-10.5-8.1-1.1-1.7-2.1-1.3-3.8-4.6-1.8-3.3-2.6-4.5-3.7-6.6-3.6-6.8-6-3.2-7.1-3.2h-4.6c0 0 7.2-22.7 5.9-28.5-1.3-5.8-4.3-12.9-4.3-12.9z" fill="#fc0"/>
            <path d="m85.6 158c0 0-9.3-1-19.1 4.8-13 7.6-19.1 14.4-18.9 17.5 0.2 3.2 3.5 4.1 6.1 0.7 2.6-3.5 1.7-5.8 1.7-5.8 0 0 10.4-11.2 19.3-13.4 8.3-2.1 12.9-1.2 12.9-1.2l3.5-2.4z"/>
            <path d="m91.3 149.8c-4.1 1-7.6 5-8.2 14.7-0.7 11.2-0.4 15.1 5.5 11.7 7.1-4.1 11.1-12.8 10.4-20.2-0.5-5-3.2-7.3-7.6-6.3z" fill="#fff"/>
            <path d="m82.9 142.2c0 0-7.1-6.1-18.5-6.9-15-1.1-23.9 0.9-25.5 3.6-1.6 2.7 0.5 5.4 4.6 4 4.1-1.4 4.7-3.8 4.7-3.8 0 0 14.9-3.3 23.5 0 8 3 11.3 6.3 11.3 6.3h4.3zm28.3-6.9c0 0 13 0.5 17.1 10.2 9.1 21.4-13.8 31.6-13.8 31.6 0 0 18.6 4.9 26.4-2.4 7.8-7.3 14.4-20.9 5.1-34.4-9.3-13.5-24.1-13.5-34.7-4.9zm32.6 2.1c0 0 8.1-1.2 15.5 4.3 7.3 5.5 9.1 22.7 2.8 31.8-6.9 10-16.3 11-16.3 11l-4.2-7.1c0 0 12.7-6 14.5-16.5 1.8-10.5-2.5-20.7-12.4-23.6zm13.2-0.3c0 0 15.2 2.4 17.7 16.9 2.5 14.5-3.3 25-8.6 28.9-10.1 7.5-18.3 5-18.3 5 0 0 7 7.2 9.5 8.7 0.8 0.5 28.5-6.2 28-33.1-0.2-10.5-4-26.7-28.2-26.5zM187 152.2"/>
            <path d="m187 152.2c0 0 9.1 21.1 2.3 33.3-7.1 12.6-15.9 15.5-21.6 15.1-4.6-0.3-4.4-0.5-4.4-0.5 0 0 13.6 14.4 14.4 19.9 0.8 5.5 0 1.7 2.1-2.3 2.1-4 15.9-11.7 17.5-29.4 1.5-17-1.6-24.5-10.3-36.1zm-61.2 25.3c0 0-0.2 4.2-1.1 7.1-0.9 2.8 0 3.8 1.1 8.3 1.1 4.5 1.7 7.8-0.8 9.5-2.5 1.7-2.1 2.7-2.5 1.7-0.4-0.9 0.3-1.4 1.1-3.4 0.8-2 1.5-3.8 0-7-1.5-3.2-4.2-6.1-3.6-10.4 0.3-2.1 0.9-4.9 0.9-4.9l1.5-1.9zm8.9-1.1c0.7 1.3 3.4 5.3 3.8 8.1 0.4 2.8-1.5 4.5-1.9 5.9-0.4 1.3-2.2 4.7 0 7.8 2.2 3 3.2 6.2 3.2 9.8 0 3.6 0.9 5.5 1.9 3.4 0.9-2.1 2.3-1.5 0-7-2.3-5.5-2.3-5.3-2.9-7.7-0.5-2.3 4.1-6.2 4.1-9.3 0.1-3.1-2.6-9.1-3.1-11-0.6-1.9-2.3-4.7-2.3-4.7zm14.8 12.7c0.6 0.6 5.5 9.1 4 16.5-1.5 7.4-4.4 12.9-1.8 16.3 2.6 3.4 6.2 7.4 6.9 7.6 0.8 0.2 1.9-1.1 1.3-2.1-0.6-0.9-2.8-2.6-4-5-1.3-2.6-2.8-5.2-1.1-8.2 1.7-3 3-7 3-9.7 0-2.6-0.1-4.3-0.3-7.7-0.2-3.4-2.7-7.6-2.7-7.6h-5.3zm-47.9-23.3c-1.7 4.8-6 7.7-9.6 6.4-3.6-1.3-5.2-6.2-3.6-11 1.7-4.8 6-7.7 9.6-6.4 3.6 1.3 5.2 6.2 3.6 11z"/>
            <circle r="3.3" cy="161.2" cx="90.5" fill="#fff"/>
        </g>
        <g id="wing1">
            <path   d="M35.4 10.7C28 16.7 24.1 31.2 24.1 31.7a106.4 106.4 0 0 1 1.1 0.6c1.1 0.6 2.5 2.4 2.5 2.4l1-0.3 0 0c2-0.6 5-1.7 8.2-3.2C41.9 28.8 51.6 21.1 51.6 12.7 51.6 4.8 42.9 4.8 35.4 10.7Zm0.4 18.4c-2.9 1.3-5.8 2.4-7.6 3-0.1 0-0.6 0.3-1 0.5-1-1.2-2.1-1.7-2.4-1.8 1.6-3.6 6.7-14 12.1-18.3 2.9-2.3 6.2-3.7 8.7-3.7 1.2 0 2.1 0.3 2.7 0.9 0.7 0.6 1 1.7 1 3.1 0 7.1-8.9 14.3-13.4 16.4z" fill="#000"/>

            <path id="inside-wing1" fill="#EAEAEA" d="m35.9 29.1c-2.9 1.3-5.8 2.4-7.6 3-0.1 0-0.6 0.3-1 0.5-1-1.2-2.1-1.7-2.4-1.8 1.6-3.6 6.7-14 12.1-18.3 2.9-2.3 6.2-3.7 8.7-3.7 1.2 0 2.1 0.3 2.7 0.9 0.7 0.6 1 1.7 1 3.1 0 7.1-8.9 14.3-13.4 16.4z" />
        </g>
        <g id="wing2">
            <path id="wing2" d="m27.3 1.3c-6.5 4.6-8.3 16.8-7.9 22.3 0.3 4.1 1 7.7 1.3 9.5 0.5-0.1 1 0 1.6 0.1 0.1 0.1 0.3 0.1 0.4 0.1 1.4 0.2 1.6 0.8 1.6 0.9l0 0c0 0 0 0 0 0 0.3-0.2 0.5-0.4 0.8-0.7 0 0 9.4-10.8 10.8-20.2C37.3 4 33.5-3.1 27.3 1.3Zm6.6 11.9c-0.8 5.6-4.9 13.3-7.5 17.6l-4.2-0.8A73.9 73.9 0 0 1 21.5 23.2c-0.4-4.9 1.3-16.1 6.8-20 1.1-0.8 2.1-1.1 2.8-0.9 0.7 0.2 1.3 0.7 1.9 1.7 1.1 2.1 1.5 5.5 0.9 9.2z" fill="#000"/>
        </g>
        <!-- Bee moving animation -->
        <animateTransform attributeName="transform" type="translate" dur="30s" values="900,0;800,140;800,150;700,100; 610,160;610,150;610,150;610,160;610,150;600,160;610,330;610,300;610,330;685,470;685,470;560,520;570,530;570,530;480,340;480,330;480,340;400,400;400,400;330,220;330,220;280,180;250,80;225,365;
		    225,365;225,365;200,180;150,150;150,150;0,0;-100,-100"
                          fill="freeze"
                          repeatCount="indefinite" />
    </g>
</svg>

<div id="page-body">