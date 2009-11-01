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
    '../client-src/js/jQuery.js',

    // destination file - ti will create
    // jQuery.js.src.deflate
    // jQuery.js.src.gzip
    // jQuery.js.src.txt
    // jQuery.js.sha1.deflate
    // jQuery.js.sha1.gzip
    // jQuery.js.sha1.txt
    // all inside client/js folder
    '../client/js/jQuery.js'
);
?>