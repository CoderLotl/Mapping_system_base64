# DOCUMENTATION

## Project: Map to Base64, Base64 to map, Minimap based on coords.

---

## Requirements:
<ul>
    <li>PHP 5 >= 5.20
</ul>

---

### Description:
This project consists of an <span style="color: green; font-weight: bold">initial step</span>: **converting an image to base64**, persisting it in a JSON, the original image separated in **X** for **Y** chunks, file for simplicity...

... And <span style="color: blue; font-weight: bold">two</span> other <span style="color: purple; font-weight: bold; font-style: italic">optional steps</span>: 

**A)** Turning the base64 content back into the original image.

**B)** Getting a snippet of the original image based on coords of **X,Y** of the original image.

---

The purpose of this project is to experiment with storing images as text, useful for a database, and getting either it or a bit of it back, all in the context of working with a map which could be used for a web game.

The particularity of this mapping system is that, while the map is separated in *tiles* and stored that way, by getting a snippet from the map based on coords that are pixels, it allows the *consumer* of this system to navigate the map pixel by pixel.
This is a very important feature, which contrasts with the way many games store their maps as tiles and the player, character, event, etc., are located and take place in whole squared tiles.

Therefore, this system is more oriented for a node system, just like MUDs and the like generally use.

---

### Content:

This repository consists of 3 PHP scripts, each of them pretty much self-explanatory with their names, and the only 2 required files for running them.

The use is simple: You first need to check **a_image_to_base64.php** and change any parameter you may think it's needed to be changed, then run that script.
You can then optionally run any of the other 2 scripts.