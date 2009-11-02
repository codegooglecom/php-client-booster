<?php

/** PHP Client Booster 
 * jQuery build example
 * @author      Andrea Giammarchi
 */

// include create.php utility
require '../utils/comp/create.php';

// build the file to serve
utils_comp_createCompressedVersion(

    // it could be an array, for this
    // example it is just one file as string
    $source = '../client-src/js/loader.js',

    // destination file - ti will create
    // jQuery.js.src.deflate
    // jQuery.js.src.gzip
    // jQuery.js.src.txt
    // jQuery.js.sha1.deflate
    // jQuery.js.sha1.gzip
    // jQuery.js.sha1.txt
    // all inside client/js folder
    '../client/js/loader.js'
);

// here it is possible to add code in order to show, if necessary, the used surce code
// let's say we use this builder as script src in the layout
// in this way we'll always have an updated build plus the updated code

//* remove a slash before this comment to avoid its execution
header('Content-Type: text/javascript');
utils_comp_noCache();
if(!is_array($source))
    $source = array($source);
$source = array_map('utils_comp_readFile', $source);
echo implode(PHP_EOL, $source);
//*/

?>