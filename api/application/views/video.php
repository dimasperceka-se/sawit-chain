<!doctype html>
<head>

   <!-- player skin -->
   <link rel="stylesheet" type="text/css" href="http://demo.cocoatrace.com/js/plugins/flowplayer-5.4.6/skin/minimalist.css">

   <!-- site specific styling -->
   <style type="text/css">
   body { font: 12px "Myriad Pro", "Lucida Grande", sans-serif; text-align: center; padding-top: 5%; background-color: #000;}
   .flowplayer { width: 80%; }
   input.xpreviewvideo_btn {background-color:transparent;color:white;border:1px solid #fff;font-size:1.1em;font-weight:bold;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-border-radius:5px;padding-left:20px;padding-right:20px;cursor:pointer;}
   input.xpreviewvideo_btn:hover {border:1px solid #fff;background-color:#888;color:#000;}
   input.xpreviewvideo_btn:active {background-color:#aac;}
   </style>

   <!-- flowplayer depends on jQuery 1.7.1+ (for now) -->
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

   <!-- include flowplayer -->
   <script type="text/javascript" src="http://demo.cocoatrace.com/js/plugins/flowplayer-5.4.6/flowplayer.min.js"></script>
     <script>

   // bind listeners to all players on the page
   flowplayer(function(api, root) {

      // when a new video is about to be loaded
      api.bind("load", function() {
         //console.info("load", api.engine);
         jQuery('div.flowplayer').addClass("is-fullscreen");

      // when a video is loaded and ready to play
      }).bind("ready", function() {
         //console.info("ready", api.video.duration)

      });

   });
   function do_video_fullscreen() { 
      var api = flowplayer();
      var elem = document.getElementById("videoplayer");
      api = flowplayer(elem);
      //console.log(api);
      api.fullscreen();
   }
    function do_video_play() { 
      var api = flowplayer();
      var elem = document.getElementById("videoplayer");
      api = flowplayer(elem);
      //console.log(api);
      api.play();
   }
    function do_video_pause() { 
      var api = flowplayer();
      var elem = document.getElementById("videoplayer");
      api = flowplayer(elem);
      //console.log(api);
      api.pause();
   }
    function do_video_stop() { 
      var api = flowplayer();
      var elem = document.getElementById("videoplayer");
      api = flowplayer(elem);
      //console.log(api);
      api.stop();
   }
</script>
</head>

<body>
  <!--  <?=$data?> -->
   <!-- the player -->
   <div id="videoplayer" class="flowplayer" data-swf="flowplayer.swf" data-ratio="0.4167" fullscreen="true">
      <video>
         <source type="video/mp4" src="<?=base_url()?>files/video/<?=$data?>">
      </video>
   </div>
   <br />
   <br />
   <div><input type="button" value="Play" class="xpreviewvideo_btn" onclick="do_video_play();"/>&nbsp;<input type="button" value="Pause" class="xpreviewvideo_btn" onclick="do_video_pause();"/>&nbsp;<input type="button" value="Stop" class="xpreviewvideo_btn" onclick="do_video_stop();"/></div>

</body>
