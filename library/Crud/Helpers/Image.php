<?php
/**
 * must define $_metadata and the ctor !!!
 *//**
 * Class Name
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class Crud_Helpers_Image
{
    /**
     * @throws Zend_View_Exception
     * @param array options: sourcePath, outputPath, outputWidth(in pixels),
     * outputHeight=null, method='gd', quality=75, maxWidth=2048
     */
    public static function convertImageJPG(array $options)
    {
        $sourcePath = $options['sourcePath'];
        $outputPath = $options['outputPath'];
        $outputWidth = (int)$options['outputWidth'];
        $outputHeight = isset($options['outputHeight']) ? $options['outputHeight'] : null;
        $method =  isset($options['method']) ? $options['method'] : 'gd'; //default
        $quality = isset($options['quality']) ? $options['quality'] : 75;
        $maxWidth = isset($options['maxWidth']) ? $options['maxWidth'] : 2048;

        //checks
        if ($outputWidth < 1 || $outputWidth > $maxWidth) {
            throw new Zend_View_Exception ('width must be at least 1px and at maximum '.$maxWidth);
        }        
        if (!file_exists($sourcePath)) {
            throw new Zend_View_Exception( 'Input file ['.$sourcePath.'] does not exists' );
        }
        if (!is_writable(dirname($outputPath))) {
            throw new Zend_View_Exception( 'Unable to write into ' . dirname($outputPath) );
        }
        
        if ($method=='commandline') {
           $commandLine = sprintf(
               'convert "%s" -strip -filter Cubic -resize %s -quality %s "%s"',
               $sourcePath, $outputWidth, $quality, $outputPath
           );
           passthru($commandLine);
           clearstatcache();
           return file_exists($outputPath);
        } else {
            if (!extension_loaded('gd')) {
                throw new Zend_View_Exception( 'PHP gd2 extension not loaded');
            }
            //gd conversion
            $inputImageSize = getimagesize($sourcePath);
            $img_src = self::imageCreateFromMime($sourcePath, $inputImageSize['mime']);
            if (!$img_src) {
                throw new Zend_View_Exception('Unable to open the uploaded image');
            }
            
            if (!($inputImageSize[0] * $inputImageSize[1])) {
                throw new Zend_View_Exception(
                        'Unable to calculate the the size of the uploaded image'
                );
            }
            if (!$outputHeight) {  //if height not specified or null (default), calculate it !
                $outputHeight = self::calculateHeightByRatio(
                    $inputImageSize[0],
                    $inputImageSize[1],
                    $outputWidth
                );
            }
            $img_dst = imagecreatetruecolor($outputWidth, $outputHeight);

            if (!imagecopyresampled(
                $img_dst, $img_src, 0, 0, 0, 0,
                $outputWidth, $outputHeight, $inputImageSize[0], $inputImageSize[1]
            )
            ) {
                throw new Zend_View_Exception('Unable to recompress the uploaded image');
            }
            return imagejpeg($img_dst, $outputPath, $quality);
        }
    }


    private static function calculateHeightByRatio($originalW, $originalH, $maxFrameWidth)
    {
        //calculate ratio $originalW : $originalH ==== $maxFrameWidth : <RETURN VAL>
        return round($originalH * $maxFrameWidth / $originalW, 0);
    }

    private static function imageCreateFromMime($sourcePath, $mime )
    {
        
        switch ($mime){
            case 'image/jpeg':
                return imagecreatefromjpeg($sourcePath);
            // case 'image/x-ms-bmp':
            //     return imagecreatefromxbm( $sourcePath );
            case 'image/gif':
                return imagecreatefromgif($sourcePath);
            case 'image/png':
                return imagecreatefrompng($sourcePath);
            default:
                 throw new Zend_View_Exception(sprintf('Type %s not supported', $mime));
        }
        return false;
    }

    /** return file extensions
     *
     * @param <type> $fileName
     * @return <type>
     */
    public static function getExtension($fileName)
    {
        $ret = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
        return $ret;
    }

}