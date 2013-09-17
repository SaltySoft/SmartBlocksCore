<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ImageComponent extends Component
{
    public static function saveSentPicture($formname, $name = "")
    {

        if ((($_FILES[$formname]["type"] == "image/gif")
            || ($_FILES[$formname]["type"] == "image/jpeg")
            || ($_FILES[$formname]["type"] == "image/jpg")
            || ($_FILES[$formname]["type"] == "image/pjpeg")
            || ($_FILES[$formname]["type"] == "image/png"))
            && ($_FILES[$formname]["size"] < 2000000))
        {
            if ($_FILES[$formname]["error"] > 0)
            {
                //echo "Error: " . $_FILES["picture"]["error"] . "<br />";
            }
            else
            {
                $ext = explode("/", $_FILES[$formname]["type"]);
                if ($name == "")
                {
                    $name = rand()*time().".".$ext[1];
                }
                $picture_url = "/images/uploaded/".$name;
                RequestComponent::MoveFile($_FILES[$formname]["tmp_name"], $picture_url);
            }
        }
        return $picture_url;
    }

    public static function filterGrayscale($filename, $dest)
    {
        $absolute_path = DefaultComponent::absolutePath($filename);
        $absolute_dest = DefaultComponent::absolutePath($dest);
        $image_size = getimagesize($absolute_path);
        $image_type = $image_size[2];

        switch($image_type)
        {
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($absolute_path);
                if ($img && imagefilter($img, IMG_FILTER_GRAYSCALE))
                {
                    imagejpeg($img, $absolute_dest);
                    imagedestroy($img);

                }
                else
                {
                    echo "ha";
                }
                break;
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($absolute_path);
                if ($img && imagefilter($img, IMG_FILTER_GRAYSCALE))
                {
                    imagepng($img, $absolute_dest);
                    imagedestroy($img);
                }
                else
                {

                }
                break;
            case IMAGETYPE_BMP:
                $img = imagecreatefromwbmp($absolute_path);
                if ($img && imagefilter($img, IMG_FILTER_GRAYSCALE))
                {
                    imagewbmp($img, $absolute_dest);
                    imagedestroy($img);
                }
                break;
        }

    }
}
