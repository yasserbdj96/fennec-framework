<?php
/*s*/
//include the script:
    //include_once('plugin_img_rsz.php');

//Usage:
    //Img::resizeImage('<SOURCE>','<DESTINATION>','<OPTIONS>');

//EX:
    //Img::resizeImage('noname.jpg',null,array('x'=>1050,'y'=>150));//Outputs Image.
    //Img::resizeImage('test.png','crop.png',array('x'=>200,'y'=>200));//Save Image.
final class Img{
    public static function resizeImage($source,$destination = null, $options = array() ) {
        if(!file_exists($source) || ( is_string( $destination ) && !is_writable( dirname( $destination ) ) ) ) {
            throw new Exception( 'Quelldatei existiert nicht oder Zielverzeichnis ist nicht beschreibbar.' );
        }
        #@ini_set ('memory_limit', '64M' );
        $defaultOptions = array(
            'x' => 100,
            'y' => 100,
            'maxX' => 1000,
            'maxY' => 1000,
            'zoom_crop' => 1,
            'quality' => 90,
            'align' => 'c', // [c]enter, [b]ottom, [t]op, [l]eft, [r]ight
            'filters' => '',
            'sharpen' => 0,
            'canvas' => '',
        );
        $options = array_merge( $defaultOptions, $options );
        $sData = getimagesize( $source );
        $origType = $sData[2];
        $mimeType = $sData['mime'];
        if( !preg_match( '/^image\/(?:gif|jpg|jpeg|png)$/i', $mimeType ) ) {
            throw new Exception( 'The image being resized is not a valid gif, jpg or png.' );
        }
        if( !function_exists( 'imagecreatetruecolor' ) ) {
            throw new Exception( 'GD Library Error: imagecreatetruecolor does not exist' );
        }
        if( function_exists( 'imagefilter' ) && defined( 'IMG_FILTER_NEGATE' ) ) {
            $imageFilters = array (
                    1 => array (IMG_FILTER_NEGATE, 0),
                    2 => array (IMG_FILTER_GRAYSCALE, 0),
                    3 => array (IMG_FILTER_BRIGHTNESS, 1),
                    4 => array (IMG_FILTER_CONTRAST, 1),
                    5 => array (IMG_FILTER_COLORIZE, 4),
                    6 => array (IMG_FILTER_EDGEDETECT, 0),
                    7 => array (IMG_FILTER_EMBOSS, 0),
                    8 => array (IMG_FILTER_GAUSSIAN_BLUR, 0),
                    9 => array (IMG_FILTER_SELECTIVE_BLUR, 0),
                    10 => array (IMG_FILTER_MEAN_REMOVAL, 0),
                    11 => array (IMG_FILTER_SMOOTH, 0),
            );
        }
        $destX = min( $options['x'], $options['maxX'] );
        $destY = min( $options['y'], $options['maxY'] );
        switch( $mimeType ) {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjgpg':
                $image = imagecreatefromjpeg( $source );
                break;
            case 'image/png':
                $image = imagecreatefrompng( $source );
                break;
            case 'image/gif':
                $image = imagecreatefromgif( $source );
                break;
        }
        if( !isset( $image ) ) {
            throw new Exception( 'Could not open Image' );
        }
        $width = imagesx( $image );
        $height = imagesy( $image );
        $originX = $originY = 0;
        if( $destX > 0 && $destY == 0 ) {
            $destY = floor( $height * ( $destX / $width ) );
        } else if( $destY > 0 && $destX == 0 ) {
            $destX = floor( $width * ( $destY / $height ) );
        }
        // scale down and add borders
        if( $options['zoom_crop'] == 3 ) {
            $finalY = $height * ( $destX / $width );
            if( $finalY > $destY ) {
                $destX = $width * ( $destY / $height );
            } else {
                $destY = $finalY;
            }
        }
        $canvas = imagecreatetruecolor( $destX, $destY );
        imagealphablending( $canvas, false );
        if( strlen( $options['canvas'] ) < 6 ) {
            $options['canvas'] = 'ffffff';
        }
        $canvasColorR = hexdec( substr( $options['canvas'], 0, 2 ) );
        $canvasColorG = hexdec( substr( $options['canvas'], 2, 2 ) );
        $canvasColorB = hexdec( substr( $options['canvas'], 2, 2 ) );
        // transparentes bild erstellen
        $color = imagecolorallocatealpha( $canvas, $canvasColorR, $canvasColorG, $canvasColorB, 127 );
        imagefill( $canvas, 0, 0, $color );
        // scale down and add borders
        if( $options['zoom_crop'] == 2 ) {
            $finalY = $height * ( $destX / $width );
            if( $finalY > $destY ) {
                $originX = $destX / 2;
                $destX = $width * ( $destY / $height );
                $originX = round( $originX - ( $destX / 2 ) );
            } else {
                $originY = $destY / 2;
                $destY = $finalY;
                $originY = round( $originY - ( $destY / 2 ) );
            }
        }
        // restore transparency blending
        imagesavealpha( $canvas, true );
        if( $options['zoom_crop'] > 0 ) {
            $srcX = $srcY = 0;
            $srcW = $width;
            $srcH = $height;
            $cmpX = $width / $destX;
            $cmpY = $height / $destY;
            // calculate x or y coordinate and width or height of source
            if( $cmpX > $cmpY ) {
                // breitformat
                $srcW = round( $width / $cmpX * $cmpY );
                $srcX = round( ( $width - ( $width / $cmpX * $cmpY ) ) / 2 );
            } elseif( $cmpY > $cmpX ) {
                $srcH = round( $height / $cmpY * $cmpX );
                $srcY = round( ( $height - ( $height / $cmpY * $cmpX ) ) / 2 );
            }
            // pos cropping
            if( strlen( $options['align'] ) ) {
                if( strpos( $options['align'], 't') !== false) {
                    $srcY = 0;
                }
                if( strpos( $options['align'], 'b') !== false) {
                    $srcY = $height - $srcH;
                }
                if( strpos( $options['align'], 'l') !== false) {
                    $srcX = 0;
                }
                if( strpos( $options['align'], 'r') !== false) {
                    $srcX = $width - $srcW;
                }
            }
            imagecopyresampled( $canvas, $image, $originX, $originY, $srcX, $srcY, $destX, $destY, $srcW, $srcH );
        } else {
            imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $destX, $destY, $width, $height );
        }
        // @todo filtermöglichkeit über optionen ausbessern
        if( strlen( $options['filters'] ) && function_exists( 'imagefilter' ) && defined( 'IMG_FILTER_NEGATE' ) ) {
            // apply filters to image
            $filterList = explode( '|', $options['filters'] );
            foreach( $filterList as $fl ) {
                $filterSettings = explode (',', $fl);
                if (isset ($imageFilters[$filterSettings[0]])) {
                    for ($i = 0; $i < 4; $i ++) {
                        if (!isset ($filterSettings[$i])) {
                            $filterSettings[$i] = null;
                        } else {
                            $filterSettings[$i] = (int) $filterSettings[$i];
                        }
                    }
                    switch ($imageFilters[$filterSettings[0]][1]) {
                        case 1:
                            imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1]);
                            break;
                        case 2:
                            imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2]);
                            break;
                        case 3:
                            imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3]);
                            break;
                        case 4:
                            imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3], $filterSettings[4]);
                            break;
                        default:
                            imagefilter ($canvas, $imageFilters[$filterSettings[0]][0]);
                            break;
                    }
                }
            }
        }
        if( $options['sharpen'] > 0 && function_exists( 'imageconvolution' ) ) {
            $sharpenMatrix = array (
                            array (-1,-1,-1),
                            array (-1,16,-1),
                            array (-1,-1,-1),
                            );
            $divisor = 8;
            $offset = 0;
            imageconvolution( $canvas, $sharpenMatrix, $divisor, $offset );
        }
        //Straight from Wordpress core code. Reduces filesize by up to 70% for PNG's
        if( ( IMAGETYPE_PNG == $origType || IMAGETYPE_GIF == $origType ) &&
            function_exists( 'imageistruecolor' ) && !imageistruecolor( $image ) &&
            imagecolortransparent( $image ) > 0 ) {
            imagetruecolortopalette( $canvas, false, imagecolorstotal( $image ) );
        }
        if( null === $destination ) {
            header( "Cache-Control: no-store, no-cache, must-revalidate" );
            header( "Cache-Control: post-check=0, pre-check=0", false);
            header( "Pragma: no-cache" );
            header( "Expires: Sat, 25 Jan 1996 05:00:00 GMT" );
            header( "Last-Modified: " . date( "D, d M Y H:i:s" ) . " GMT" );

        }
        switch( $mimeType ) {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjgpg':
                if( null === $destination ) {
                    header("Content-type: image/jpeg");
                }
                @imagejpeg( $canvas, $destination, $options['quality'] );
                break;
            case 'image/png':
                if( null === $destination ) {
                    header("Content-type: image/png");
                }
                @imagepng( $canvas, $destination, floor( $options['quality'] * 0.09 ) );
                break;
            case 'image/gif':
                if( null === $destination ) {
                    header("Content-type: image/gif");
                }
                @imagegif( $canvas, $destination );
                break;
            default:
                throw new Exception( 'Fehler beim schreiben' );
                break;
        }
        imagedestroy( $canvas );
        imagedestroy( $image );
    }
}
/*e*/
?>