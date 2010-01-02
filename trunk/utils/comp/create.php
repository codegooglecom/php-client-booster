<?php

/** PHP Client Booster
 * @description create utility to easily create files to serve
 * @author      Andrea Giammarchi
 * @license     Mit Style License
 * @version     1.0
 */

// server.php contains some useful function
require 'serve.php';

/** utils_comp_createCompressedVersion
 * @description     providing a single file (string) or multiple files, creates automatically
 *                  a single file to serve. Based on YUI Compressor, this function should be used
 *                  only with JavaScript or CSS files (no fonts or images)
 * @param       mixed       string/array with files to merge. Please note these should have
 *                          an absolute path or a relative one, accordingly with the path
 *                          of the file that is includng this one
 * @param       string      the destination name of the file. Please note that this could contain
 *                          subdirectories, it must be absolute or relative accordingly with the
 *                          file path includer, and the suffix is necessary to let php
 *                          easily understand what kind of file we are serving (js,css,png,ttf, ..etc..)
 * @param       [array]     optional YUI Compressor configuration associative array. Available options are:
 *                          - type, js by default, css as second option
 *                          - charset, different charset if necessary
 *                          - line-break, different line break if necessary
 *                          - nomunge, if present and true(able) produced file won't be munged (default false)
 *                          - preserve-semi, preserves all semicolon (default false)
 *                          - disable-optimizations, disable micro optimizations (default false)
 */
function utils_comp_createCompressedVersion($list, $dest, $options = array()){
    $dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
    do{$tmp = $dir.'build'.DIRECTORY_SEPARATOR.uniqid('yui-', true);}while(file_exists($tmp));
    flock($fp = fopen($tmp, 'wb'), LOCK_EX);
    if(!is_array($list))
        $list = array($list);
    for($i = 0, $length = count($list); $i < $length; ++$i)
        fwrite($fp, utils_comp_readFile($list[$i]))
    ;
    flock($fp, LOCK_UN);
    fclose($fp);
    exec(vsprintf('java -jar %s %s%s%s%s%s%s%s -o %s', array(
        escapeshellarg($dir.'yuicompressor-2.4.2.jar'),
        isset($options['type']) && in_array($options['type'], array('css', 'js'))? '--type='.$options['type'].' ' : '--type=js ',
        isset($options['charset']) ? '--charset='.escapeshellarg($options['charset']).' ' : '',
        isset($options['line-break']) ? '--line-break='.escapeshellarg($options['line-break']).' ' : '',
        isset($options['nomunge']) && $options['nomunge'] ? '--nomunge ' : '',
        isset($options['preserve-semi']) && $options['preserve-semi'] ? '--preserve-semi ' : '',
        isset($options['disable-optimizations']) && $options['disable-optimizations'] ? '--disable-optimizations ' : '',
        escapeshellarg($tmp),
        escapeshellarg($tmp.'.min')
    )));
    utils_comp_createFiles($dest, utils_comp_readFile($tmp.'.min'), isset($options['level']) ? $options['level'] : 9);
    unlink($tmp.'.min');
    unlink($tmp);
}

/** utils_comp_createFile
 * @description     specific for this namespace purpose, this function
 *                  creates two files via provided path and content.
 *                  Please note these files will contain the output,
 *                  and the sha1 of the output itself in order to make
 *                  the file Etag unique and spreadable in a load balanced
 *                  environment (ETag is not the automatic Apache generated one)
 *                  Saved files will have src.ext suffix and sha1.ext one.
 * @param       string      the file name to create with two suffixes.
 *                          Please note the path should be absolute or relative
 *                          to the file path which includes this function.
 * @param       string      extension. This is generally deflate, gzip, or txt
 * @param       string      output to store into the file
 */
if(function_exists('file_put_contents')){
    function utils_comp_createFile($fileName, $ext, $output){
        file_put_contents($fileName.'.src.'.$ext, $output);
        file_put_contents($fileName.'.sha1.'.$ext, '"'.sha1($output).'"');
    }
} else {
    function utils_comp_createFile($fileName, $ext, $output){
        flock($fp = fopen($fileName.'.src.'.$ext, 'wb'), LOCK_EX);
        fwrite($fp, $output);
        flock($fp, LOCK_UN);
        fclose($fp);
        flock($fp = fopen($fileName.'.sha1.'.$ext, 'wb'), LOCK_EX);
        fwrite($fp, '"'.sha1($output).'"');
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

/** utils_comp_createFiles
 * @description     this is basically the same of utils_comp_createFile function except
 *                  it accepts both single string or array as destination file
 *                  (if we need to spread the file over every server under same domain/load balance)
 *                  and it creates autmatically deflate, gzip, and plain textversion of provided output.
 * @param       mixed       the file name (string or array) to create with two suffixes.
 *                          Please note the path should be absolute or relative
 *                          to the file path which includes this function.
 * @param       string      output to store into created files, cmpressed or plain
 * @param       int         0-9 compression level, 9 by defaults
 */
function utils_comp_createFiles($dest, $output, $compression = 9){
    if(!is_array($dest))
        $dest = array($dest);
    foreach($dest as $fileName){
        utils_comp_createFile($fileName, 'deflate', gzdeflate($output, $compression));
        utils_comp_createFile($fileName, 'gzip', gzencode($output, $compression, FORCE_GZIP));
        utils_comp_createFile($fileName, 'txt', $output);
    }
}

?>