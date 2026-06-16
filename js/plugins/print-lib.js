//// new printing framework
function _dce(a) { return document.createElement(a); }
function _destroy(obj) {
    if(obj&&obj.parentNode) {
        obj.parentNode.removeChild(obj);
        obj=null;
    }
}

function do_print_iframe(nm) { window.frames[nm].focus(); window.frames[nm].print(); }

function close_preview() {
    _destroy(dvpreview.iframe);
    _destroy(dvpreview.frame);
    _destroy(dvpreview.bg1);
    _destroy(dvpreview.bg0);
    _destroy(dvpreview);
    dvpreview.frame = null;
    dvpreview.bg1 = null;
    dvpreview.bg0 = null;
    dvpreview = null;
}

var dvpreview = null;
function setup_preview() {
    if(dvpreview) {
        close_preview();
    }

    var btn_printserver = '';



    var nm = uniqid();
    dvpreview = _dce('div');
    dvpreview.setAttribute('class','xpreview');
    dvpreview = document.body.appendChild(dvpreview);
    dvpreview.bg0 = _dce('div');
    dvpreview.bg0.setAttribute('class','xpreview_bg0');
    dvpreview.bg0 = dvpreview.appendChild(dvpreview.bg0);
    dvpreview.bg1 = _dce('div');
    dvpreview.bg1.setAttribute('class','xpreview_bg1');
    dvpreview.bg1 = dvpreview.appendChild(dvpreview.bg1);
    dvpreview.btn = _dce('div');
    dvpreview.btn.setAttribute('class','xpreview_dvbtn');
    dvpreview.btn = dvpreview.bg1.appendChild(dvpreview.btn);
    dvpreview.btn.innerHTML = '<input type="button" value="Print" class="xpreview_btn" onclick="do_print_iframe(\''+nm+'\');"/>&nbsp;'
        + btn_printserver
        + '<input type="button" value="Close" class="xpreview_btn" onclick="close_preview();"/>&nbsp;';
    dvpreview.pn = _dce('div');
    dvpreview.pn.setAttribute('class','xpreview_pn');
    dvpreview.pn = dvpreview.bg1.appendChild(dvpreview.pn);
    dvpreview.pn.onclick=close_preview;
    dvpreview.frame = _dce('div');
    dvpreview.frame.setAttribute('class','xpreview_frame');
    dvpreview.frame = dvpreview.bg1.appendChild(dvpreview.frame);
    dvpreview.iframe = _dce('iframe');
    dvpreview.iframe.setAttribute('class','xpreview_iframe');
    dvpreview.iframe.setAttribute('id',nm);
    dvpreview.iframe.setAttribute('name',nm);
    dvpreview.iframe = dvpreview.frame.appendChild(dvpreview.iframe);
}
function setup_preview_link() {
    if(dvpreview) {
        close_preview();
    }

    var btn_printserver = '';



    var nm = uniqid();
    dvpreview = _dce('div');
    dvpreview.setAttribute('class','xpreview');
    dvpreview = document.body.appendChild(dvpreview);
    dvpreview.bg0 = _dce('div');
    dvpreview.bg0.setAttribute('class','xpreview_bg0');
    dvpreview.bg0 = dvpreview.appendChild(dvpreview.bg0);
    dvpreview.bg1 = _dce('div');
    dvpreview.bg1.setAttribute('class','xpreview_bg1');
    dvpreview.bg1 = dvpreview.appendChild(dvpreview.bg1);
    // dvpreview.btn = _dce('div');
    // dvpreview.btn.setAttribute('class','xpreview_dvbtn');
    // dvpreview.btn = dvpreview.bg1.appendChild(dvpreview.btn);
    // dvpreview.btn.innerHTML = '<input type="button" value="Print" class="xpreview_btn" onclick="do_print_iframe(\''+nm+'\');"/>&nbsp;'
    //     + btn_printserver
    //     + '<input type="button" value="Close" class="xpreview_btn" onclick="close_preview();"/>&nbsp;';
    dvpreview.pn = _dce('div');
    dvpreview.pn.setAttribute('class','xpreview_pn');
    dvpreview.pn = dvpreview.bg1.appendChild(dvpreview.pn);
    dvpreview.pn.onclick=close_preview;
    dvpreview.frame = _dce('div');
    dvpreview.frame.setAttribute('class','xpreview_frame');
    dvpreview.frame = dvpreview.bg1.appendChild(dvpreview.frame);
    dvpreview.iframe = _dce('iframe');
    dvpreview.iframe.setAttribute('class','xpreview_iframe');
    dvpreview.iframe.setAttribute('id',nm);
    dvpreview.iframe.setAttribute('name',nm);
    dvpreview.iframe = dvpreview.frame.appendChild(dvpreview.iframe);
}

function do_video_fullscreen() { 
   var api = flowplayer();
   var elem = document.getElementById("videoplayer");
   api = flowplayer(elem);
   console.log(api);

}


function setup_videopreview() {
    if(dvpreview) {
        close_preview();
    }

    var btn_printserver = '';

    var nm = uniqid();
    dvpreview = _dce('div');
    dvpreview.setAttribute('class','xpreviewvideo');
    dvpreview = document.body.appendChild(dvpreview);
    dvpreview.bg0 = _dce('div');
    dvpreview.bg0.setAttribute('class','xpreviewvideo_bg0');
    dvpreview.bg0 = dvpreview.appendChild(dvpreview.bg0);
    dvpreview.bg1 = _dce('div');
    dvpreview.bg1.setAttribute('class','xpreviewvideo_bg1');
    dvpreview.bg1 = dvpreview.appendChild(dvpreview.bg1);
    dvpreview.btn = _dce('div');
    dvpreview.btn.setAttribute('class','xpreviewvideo_dvbtn');
    dvpreview.btn = dvpreview.bg1.appendChild(dvpreview.btn);
    dvpreview.btn.innerHTML = '<input type="button" value="Close" class="xpreview_btn" onclick="close_preview();"/>&nbsp;';
    dvpreview.pn = _dce('div');
    dvpreview.pn.setAttribute('class','xpreviewvideo_pn');
    dvpreview.pn = dvpreview.bg1.appendChild(dvpreview.pn);
    dvpreview.pn.onclick=close_preview;
    dvpreview.frame = _dce('div');
    dvpreview.frame.setAttribute('class','xpreviewvideo_frame');
    dvpreview.frame = dvpreview.bg1.appendChild(dvpreview.frame);
    dvpreview.iframe = _dce('iframe');
    dvpreview.iframe.setAttribute('class','xpreviewvideo_iframe');
    dvpreview.iframe.setAttribute('id',nm);
    dvpreview.iframe.setAttribute('name',nm);
    dvpreview.iframe = dvpreview.frame.appendChild(dvpreview.iframe);
}
function uniqid (prefix, more_entropy) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kankrelune (http://www.webfaktory.info/)
    // %        note 1: Uses an internal counter (in php_js global) to avoid collision
    // *     example 1: uniqid();    // *     returns 1: 'a30285b160c14'
    // *     example 2: uniqid('foo');
    // *     returns 2: 'fooa30285b1cd361'
    // *     example 3: uniqid('bar', true);
    // *     returns 3: 'bara20285b23dfd1.31879087'

    if (typeof prefix == 'undefined') prefix = "";

    var retId;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed,10).toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return Array(1 + (reqWidth - seed.length)).join('0')+seed;
        }
        return seed;
    };
    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
    // END REDUNDANT
    if (!this.php_js.uniqidSeed) { // init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;
    retId  = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date().getTime()/1000,10),8);
    retId += formatSeed(this.php_js.uniqidSeed,5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random()*10).toFixed(8).toString();
    }
    return retId;
}