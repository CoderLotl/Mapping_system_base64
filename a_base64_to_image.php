<?php

function Base64ToImage($tiles)
{
    $map = [];
    $x = 0;
    $xPos = 0;
    $y = 0;
    $yPos = 0;
    $tileHeight = $tiles[0]['y'] * 2;
    $tileWidth= $tiles[0]['x'] * 2;
    $mapHeight = 0;
    $mapWidth = 0;    

    foreach($tiles as $tile)
    {
        if($yPos == 0)
        {
            $yPos = $tile['y'];
            array_push($map, []);
        }
        elseif($tile['y'] > $yPos)
        {
            $yPos = $tile['y'];
            $y++;
            array_push($map, []);
        }

        if($xPos == 0)
        {
            $xPos = $tile['x'];
        }
        elseif($tile['x'] > $xPos)
        {
            $xPos = $tile['x'];
            $x++;
        }
        else
        {
            $xPos = $tile['x'];
            $x = 0;
        }

        array_push($map[$y], $tile['data']);
    }

    $mapHeight = count($map) * $tileHeight;
    $mapWidth = count($map[0]) * $tileWidth;
    $image = imagecreatetruecolor($mapWidth, $mapHeight);
    
    for($yy = 0; $yy < $y + 1; $yy++)
    {
        for($xx = 0; $xx < $x + 1; $xx++)
        {
            $imageData = unserialize(gzuncompress(base64_decode($map[$yy][$xx])));
            $tile = imagecreatefromstring($imageData);
            imagecopy($image, $tile, $xx * $tileWidth, $yy * $tileHeight, 0, 0, $tileWidth, $tileHeight);
            imagedestroy($tile);
        }
    }
    return $image;
}

$file = file_get_contents('./test.json');
$tiles = json_decode($file, true);
$image = Base64ToImage($tiles);
imagejpeg($image, 'a_new.jpeg');
imagedestroy($image);