<?php

/** PHP Client Booster
 * @description utility to easily serve files
 * @author      Andrea Giammarchi
 * @license     Mit Style License
 * @version     1.0
 */

/** utils_comp_contentType
 * @description     given a file name with an extension, returns the correct Content-Type header.
 * @param       string      a generic file name
 * @return      string      Content-Type to use. This function is mainly suitable for common client files.
 *                          Please note not every extension has been included but we can easily add more if needed.
 */
function utils_comp_contentType($fileName){
    static  $mime = array(
        // add your custom extension here
        // 'myext' => 'mycontent/type',
        'css'   => 'text/css',
        'js'    => 'text/javascript',
        'json'  => 'application/json',
        'htm'   => 'text/html',
        'html'  => 'text/html',
        'txt'   => 'text/plain',
        'xml'   => 'application/xml',
        'svg'   => 'application/xml'
    );
    $ext = strrpos($fileName, '.');
    return  $ext !== false && isset($mime[$fileName = substr($fileName, ++$ext)]) ?
        $mime[$fileName] :
        'application/octet-stream'
    ;
}

/** utils_comp_readFile (partial file_get_contents alias)
 * @description     return a generic file content, binary safe.
 *                  Please note if PHP is not that old the if statement could be voided
 *                  and the function in the else removed.
 * @param       string      a generic file name
 * @return      string      the generic file content.
 */
if(function_exists('file_get_contents')){
    function utils_comp_readFile($fileName){
        return file_get_contents($fileName);
    }
} else {
    function utils_comp_readFile($fileName){
        $result = fread($fp = fopen($fileName, 'rb'), filesize($fileName));
        fclose($fp);
        return $result;
    }
}

/** utils_comp_serveFile
 * @description     easy way to avoid cached content (not used but may be useful)
 */
function utils_comp_noCache(){
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

/** utils_comp_serveFile
 * @description     the fastest way to handle a 304 response, creating conditions
 *                  to force 304 for every other request.
 * @param       string      a generic file name that has been parsed via create.php functions.
 */
function utils_comp_serveFile($dest){
    // browser supports encoding?
    $HAE    = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
    // browser has sent an if_none_match header? (ETag related)
    $HINM   = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : '';
    switch(true){
        // deflate is slightly bigger than gzip
        // but less problematic with older browsers
        // and slightly faster to decompress: default!
        case strpos($HAE, 'deflate') !== false:
            $ext = 'deflate';
            break;
        // gzip or x-gzip for those browsers not
        // compatible with deflate (I guess not a single one but who knows ...)
        case strpos($HAE, 'gzip') !== false:
            $ext = 'gzip';
            break;
        // readers or alien browsers may not be compatible with compressed output
        default:
            $ext = 'txt';
            break;
    }
    // the unique ETag without performing any costly operation (e.g. sha1)
    // the hash has been created before and the file length will be 40bytes
    // a small file which 99.9% will be simply stored in the hard disk buffer
    $hash = utils_comp_readFile($dest.'.sha1.'.$ext);
    switch(true){
        // the browser has already downloaded this file?
        case $HINM === $hash:
            // in this case there is absolutely nothing else to do
            // the browser knows file associated headers/info
            // so all we need is to provide the Content-Type
            // specifying 304 as response status (no overwrite required)
            header('Content-Type: '.utils_comp_contentType($dest), false, 304);
            break;
        // first time, does the browser support compression?
        case $ext !== 'txt':
            // in this case we need to specify used compression
            // as Content-Encoding. Most of the case this will be deflate
            header('Content-Encoding: '.$ext);
        default:
            // in any case if it was not a 304 we need to serve the file
            // providing the length, via fileSize
            header('Content-Length: '.filesize($fileName = $dest.'.src.'.$ext));
            // specifying the Content-Type to use
            header('Content-Type: '.utils_comp_contentType($dest));
            // setting the cache as public
            header('Cache-Control: public');
            // serving the ETag to use next time (or overwrite the existent one if file has been re-compressed)
            header('ETag: '.$hash);
            // write the content in the output
            echo utils_comp_readFile($fileName);
            break;
    }
}

?>