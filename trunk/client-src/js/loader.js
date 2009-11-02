(function(){
function script(src){
    // create the script tag and load it
    var script = d.createElement("script");
    script.type = "text/javascript";
    script.onload = script.onerror = onload;
    script.src = src;
    head.insertBefore(
        script,
        head.firstChild
    );
};
function onload(){
    // code already parsed, remove this script
    this.parentNode.removeChild(this);
    // call the callback, if present
    // when every script has been loaded
    if(--length == 0)
        $.onload()
    ;
};
var // document shortcut
    d = document,
    // the head element or the documentElement (quirks)
    head = (
        d.getElementsByTagName("head")[0] ||
        d.documentElement
    ),
    // scripts length
    length = 0,
    // exposed object
    $ = {
        // method to call to load scripts
        load:function(){
            for(var
                i = 0,
                l = length = arguments.length;
                i < l; ++i
            )
                script(arguments[i])
            ;
            // chain to add an optional onload
            return $
        },
        // silly dual behavior for every taste
        // this.onload = function(){}
        // or
        // this.onload(function(){})
        onload:function(onload){
            $.onload = onload;
        }
    }
;
return $;
})()