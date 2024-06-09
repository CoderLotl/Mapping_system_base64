<?php

function ReturnImageByCoords($charX, $charY, $tiles, $distance = 100, $centerDot = false, $compass = false)
{
    $map = [];
    $tileWidth = $tiles[0]['x'] * 2;
    $tileHeight = $tiles[0]['y'] * 2;

    $x = 0;
    $xPos = 0;
    $y = 0;
    $yPos = 0;
    $mapHeight = 0;
    $mapWidth = 0;   

    // Defining the search area as a square. The corners are top left, top right, bottom left, and bottom right.
    // It's important to keep in mind that the top left corner of the real picture is the point 0x,0y, and the bottom right is mapWidth-x,mapHeight-y.
    $topLeft = [$charX - $distance, $charY - $distance];
    $topRight = [$charX + $distance, $charY - $distance];
    $bottomLeft = [$charX - $distance, $charY + $distance];
    $bottomRight = [$charX + $distance, $charY + $distance];
    
    foreach ($tiles as $tile)
    {
      $tileCenterX = $tile['x'];
      $tileCenterY = $tile['y'];      

      $tileTopLeft = [$tileCenterX - ($tileWidth / 2), $tileCenterY - ($tileHeight / 2)];
      $tileTopRight = [$tileCenterX + ($tileWidth / 2), $tileCenterY - ($tileHeight / 2)];
      $tileBottomLeft = [$tileCenterX - ($tileWidth / 2), $tileCenterY + ($tileHeight / 2)];
      $tileBottomRight = [$tileCenterX + ($tileWidth / 2), $tileCenterY + ($tileHeight / 2)];

      if
      (
        (
            // We check if the search area is inside any of the tiles.
            ($topLeft[0] >= $tileTopLeft[0] && $topLeft[0] <= $tileTopRight[0] && $topLeft[1] >= $tileTopLeft[1] && $topLeft[1] <= $tileBottomLeft[1]) ||
            ($topRight[0] <= $tileTopRight[0] && $topRight[0] >= $tileTopLeft[0] && $topRight[1] >= $tileTopLeft[1] && $topRight[1] <= $tileBottomLeft[1]) ||
            ($bottomLeft[0] >= $tileTopLeft[0] && $bottomLeft[0] <= $tileTopRight[0] && $bottomLeft[1] >= $tileTopLeft[1] && $bottomLeft[1] <= $tileBottomLeft[1]) ||
            ($bottomRight[0] <= $tileTopRight[0] && $bottomRight[0] >= $tileTopLeft[0] && $bottomRight[1] >= $tileTopLeft[1] && $bottomRight[1] <= $tileBottomLeft[1])
        )
      )
      {
        // If so, we proceed to push the tiles into the new map.
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

        array_push($map[$y], $tile);
      }
    }

    // We calculate the size of the new map and create the base image.
    $mapHeight = count($map) * $tileHeight;
    $mapWidth = count($map[0]) * $tileWidth;
    $image = imagecreatetruecolor($mapWidth, $mapHeight);
    
    // We proceed to make a collage with the tiles.
    for($yy = 0; $yy < $y + 1; $yy++)
    {
        for($xx = 0; $xx < $x + 1; $xx++)
        {
            $imageData = unserialize(gzuncompress(base64_decode($map[$yy][$xx]['data'])));
            $tile = imagecreatefromstring($imageData);
            imagecopy($image, $tile, $xx * $tileWidth, $yy * $tileHeight, 0, 0, $tileWidth, $tileHeight);
            imagedestroy($tile);
        }
    }

    // Now we cut the area we need to show.
    $mapCenterX = $charX - ($map[0][0]['x'] - ($tileWidth / 2));
    $mapCenterY = $charY - ($map[0][0]['y'] - ($tileHeight / 2));
    $clipWidth = $distance * 2;
    $clipHeight = $distance * 2;
    
    $clip = imagecreatetruecolor($distance * 2, $distance * 2);
    imagecopyresampled($clip, $image, 0, 0, $mapCenterX - $distance, $mapCenterY - $distance, $clipWidth, $clipHeight, $distance * 2, $distance * 2);    
    imagedestroy($image);
    
    if($centerDot)
    {        
        $color_centrepoint = imagecolorallocate ($clip, 255, 165, 0);
        imagefilledellipse ($clip, ($clipWidth / 2), ($clipHeight / 2), 6, 6, $color_centrepoint);
    }


    if($compass)
    {
        $compassImage = imagecreatefromgif('./compassdigit.gif');
        imagecolortransparent ($compassImage, imagecolorat ($compassImage, 100, 100));
        $newCompass = imagecreatetruecolor($distance * 2, $distance * 2);
        imagecopyresampled($newCompass, $compassImage, 0, 0, 0, 0, $distance * 2, $distance * 2, 189, 189);
        imagedestroy($compassImage);
        imagecolortransparent ($newCompass, imagecolorat ($newCompass, 100, 100));
        imagecopymerge ($clip, $newCompass, 0, 0, 0, 0, $clipWidth, $clipHeight, 100);
        imagedestroy($newCompass);
    }

    return $clip;
}

$file = file_get_contents('./test.json');
$tiles = json_decode($file, true);
$distance = 100;

$image = ReturnImageByCoords(320, 460, $tiles, $distance, true, true);
imagejpeg($image, 'a_coords_clip.jpeg');
imagedestroy($image);