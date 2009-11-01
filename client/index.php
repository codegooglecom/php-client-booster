<?php

/** PHP Client Booster 
 * easy file serving example
 * @author      Andrea Giammarchi
 * @example     ?file=js/jQuery.js
 */

if(
    // let's be sure a file has been required
    isset($_GET['file'])
){

    // where we have created public compressed files
    // in this case this directory (with subs included)
    $root       = './';

    // normalize slashes, not strictly necessary
    // assign directly $fileName = $root.$_GET['file'];
    // for best performances
    $fileName = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $root.$_GET['file']);

    // compare via realpath.
    // realpath fails if the file is not present
    // in this case we are sure that the file exists plus the file
    // is in the $root folder (or a nested one)
    // this is just one way to increase file serving security
    // everything outside the $root folder should never be acessed
    if(strpos(realpath($fileName.'.src.txt'), realpath($root)) === 0){

        // require serve.php in order to use
        // the utils_comp_serveFile function
        require '../utils/comp/serve.php';
        utils_comp_serveFile($fileName);

        // nothing else to do
        exit;

    }
}

// if something went wrong there is nothing to serve
// let's inform the client about the fail
header("HTTP/1.0 404 Not Found");

?>