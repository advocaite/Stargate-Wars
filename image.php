<?
include("config.php");
$s = new Game();
//this function is called recursivelly
function random_string($len=5, $str='')
{
  for($i=1; $i<=$len; $i++)
   {
    //generates a random number that will be the ASCII code of the character. We only want numbers (ascii code from 48 to 57) and caps letters.
    $ord=rand(48, 90);
    if((($ord >= 48) && ($ord <= 57)) || (($ord >= 65) && ($ord <= 90)))
        $str.=chr($ord);
    //If the number is not good we generate another one
    else
        $str.=random_string(1);
  }
  return $str;
}

//create the random string using the upper function (if you want more than 5 characters just modify the parameter)
$rand_str=random_string(5);

//We memorize the md5 sum of the string into a session variable
$_SESSION['image_value'] = md5($rand_str);

//Get each letter in one valiable, we will format all letters different
$letter1=substr($rand_str,0,1);
$letter2=substr($rand_str,1,1);
$letter3=substr($rand_str,2,1);
$letter4=substr($rand_str,3,1);
$letter5=substr($rand_str,4,1);

//Creates an image from a png file. If you want to use gif or jpg images, just use the coresponding functions: imagecreatefromjpeg and imagecreatefromgif.
$image=imagecreatefrompng("images/noise.png");

//Get a random angle for each letter to be rotated with.
$angle1 = rand(-20, 20);
$angle2 = rand(-20, 20);
$angle3 = rand(-20, 20);
$angle4 = rand(-20, 20);
$angle5 = rand(-20, 20);

//Get a random font. (In this examples, the fonts are located in "fonts" directory and named from 1.ttf to 10.ttf)
$font1 = "fonts/".rand(1, 3).".ttf";
$font2 = "fonts/".rand(1, 3).".ttf";
$font3 = "fonts/".rand(1, 3).".ttf";
$font4 = "fonts/".rand(1, 3).".ttf";
$font5 = "fonts/".rand(1, 3).".ttf";

//Define a table with colors (the values are the RGB components for each color).
$colors[0]=array(122,229,112);
$colors[1]=array(85,178,85);
$colors[2]=array(226,108,97);
$colors[3]=array(141,214,210);
$colors[4]=array(214,141,205);
$colors[5]=array(100,138,204);

//Get a random color for each letter.
$color1=rand(0, 5);
$color2=rand(0, 5);
$color3=rand(0, 5);
$color4=rand(0, 5);
$color5=rand(0, 5);

//Allocate colors for letters.
$textColor1 = imagecolorallocate ($image, $colors[$color1][0],$colors[$color1][1], $colors[$color1][2]);
$textColor2 = imagecolorallocate ($image, $colors[$color2][0],$colors[$color2][1], $colors[$color2][2]);
$textColor3 = imagecolorallocate ($image, $colors[$color3][0],$colors[$color3][1], $colors[$color3][2]);
$textColor4 = imagecolorallocate ($image, $colors[$color4][0],$colors[$color4][1], $colors[$color4][2]);
$textColor4 = imagecolorallocate ($image, $colors[$color5][0],$colors[$color5][1], $colors[$color5][2]);
$size = 20;
imagettftext($image, $size, $angle1, 10, $size+15, $textColor1, $font1, $letter1);
imagettftext($image, $size, $angle2, 35, $size+15, $textColor2, $font2, $letter2);
imagettftext($image, $size, $angle3, 60, $size+15, $textColor3, $font3, $letter3);
imagettftext($image, $size, $angle4, 85, $size+15, $textColor4, $font4, $letter4);
imagettftext($image, $size, $angle5, 110, $size+15, $textColor5, $font5, $letter5);

header('Content-type: image/jpeg');
imagejpeg($image);
imagedestroy($image);
?>