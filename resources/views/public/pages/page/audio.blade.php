<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>


<html>

<head>
    <link href="data:image/gif;" rel="icon" type="image/x-icon" />

    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/public/assets/public/audio/css/style.css" />
    <link rel="stylesheet" href="/public/assets/public/audio/css/ribbon.css" />
    <link rel="screenshot" itemprop="screenshot"
        href="https://katspaugh.github.io/wavesurfer.js/example/screenshot.png" />

    <!-- wavesurfer.js -->
    <!-- <script src="https://unpkg.com/wavesurfer.js/dist/wavesurfer.min.js"></script> -->
    <script src="/public/assets/public/audio/js/wavesurfer.js"></script>

    <!-- App -->
    <script src="/public/assets/public/audio/js/app.js"></script>

    <style>
        body {
            background: #000000 !important;
            margin-left: 0 !important;
            padding: 0 !important;
            top: 0 !important;
            left: 0 !important;
            width: 70% !important;
        }
    </style>

</head>


<body>
    <div class="container"
        style="position:absolute; top:0; left:0;   margin-left:0px; padding-top:0px; padding-right:0px; padding-bottom:0px; padding:0px; background:#222222; border:1px solid #333333;">
        <div style="margin-top:0px; padding:1px 0px 10px;">
            <div class="row" style="margin:0; background:#111111; color:#ffffff; font-size:11px;">
                <div style="  padding-top:5px; padding-bottom:5px; padding-left:5px; padding-right:5px; ">
                    <div id="waveform" style="100px !important">
                        <!-- Here be waveform -->
                    </div>
                </div>
            </div>

            <style>
                .media_name {
                    color: #ffffff;
                }

                .media_name a {
                    color: powderblue;
                    text-decoration: none;
                }

                .media_name a:hover {
                    color: #007bff;
                    text-decoration: none;
                }

                .media_name a:active {
                    color: blue;
                    text-decoration: none;
                }

                .volbox {
                    margin-top: 20px;
                }
            </style>

            <div style="display:flex; padding-top:9px; padding-bottom:9px; background:#111111;">
                <div style="padding-left:5px;">
                    <button class="btn btn-success btn-block" id="playPause"
                        style="min-width:100px !important; max-width:100px !important;">
                        <span id="play"
                            style="height:20px !important; padding-top:5px !important; padding-right:5px !important; padding-bottom:5px !important; padding-left:5px !important; text-align:center !important;">
                            <i class="glyphicon glyphicon-play"></i>
                            Play
                        </span>

                        <span id="pause"
                            style="display: none; height:20px !important; padding:5px !important; text-align:center !important;">
                            <i class="glyphicon glyphicon-pause"></i>
                            Pause
                        </span>
                    </button>
                </div>
                <div id="stop_btn" style="display:flex; padding-left:5px;" onclick="wavesurfer.stop()">
                    <div class="btn btn-success btn-block"
                        style="display:flex; justify-content:center; align-items:center; background:#888888; min-width:100px !important; max-width:100px !important; border:1px solid #333333;">
                        <div
                            style="position:relative; top:-1px; margin-right:5px; display:flex; justify-content:center; align-items:center; font-size:20px; color:#1a1a1a;">
                            <i class="glyphicon glyphicon-stop"></i>
                        </div>
                        <div>Stop</div>
                    </div>
                </div>

                <div style="display:flex; padding-left:5px;" onclick="mute_control(); ">
                    <div class="btn btn-success btn-block"
                        style="display:flex; justify-content:center; align-items:center; background:#888888; min-width:100px !important; max-width:100px !important; border:1px solid #333333;">
                        <div id="mute_icon"
                            style="position:relative; top:-1px; margin-right:5px; display:flex; justify-content:center; align-items:center; font-size:20px; color:#1a1a1a;">
                            <i class="glyphicon glyphicon-volume-off"></i>
                        </div>
                        <div>Mute</div>
                    </div>
                </div>

                <div style="display:flex; padding-left:5px;">
                    <div class="btn btn-success btn-block"
                        style="display:flex; justify-content:center; align-items:center; background:#888888; min-width:130px !important; max-width:130px !important; border:1px solid #333333;">
                        <div
                            style="position:relative; top:-1px; display:flex; justify-content:center; align-items:center; font-size:20px; color:#1a1a1a;">
                            <i class="glyphicon glyphicon-volume-down"></i>
                        </div>
                        <div
                            style="position:relative; top:1px; display:flex; justify-content:center; align-items:center; margin-left:0px; margin-right:5px;">
                            <input id="volume" type="range" min="0" max="1" value="0.525"
                                step="0.01">
                        </div>
                        <div
                            style="position:relative; top:-1px; display:flex; justify-content:center; align-items:center; font-size:20px; color:#1a1a1a;">
                            <i class="glyphicon glyphicon-volume-up"></i>
                        </div>
                    </div>
                </div>
                <div id="equalizer" style="margin-top: 10px"></div>

                <div class="" id="playlist"
                    style="display:flex; justify-content:flex-start; align-items:center; width:100%; height:34px; background:#111111;">
                    <a id="media_name_href" href='/public/uploads/tracks/{{ $file->name }}'></a>
                    <div class="media_name" id="media_name_0"
                        style="width:auto; display:flex; justify-content:flex-start; align-items:center; background:transparent; color:#ffffff; border-bottom:2px solid #007bff; height:100%; margin-left:10px; padding-top:0; padding-bottom:0; padding-left:0px; padding-right:2px; font-size:12px; border-radius:0px; cursor:pointer; overflow:hidden; white-space: nowrap;"
                        onclick="media_load('1597372717','1','0'); return false;">
                        <i class="glyphicon glyphicon-play"
                            style="position:relative; top:0px; margin-left:0px; margin-right:5px; margin-top:0px;"></i><span
                            style="position:relative; top:0px; border-radius:0px; margin-top:0; margin-bottom:0;">
                            {{ str_replace('-', ' ', $file->name) }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                style="display:flex; color:#a1a1a1; background:#000000; margin-top:1px; text-align:right; padding-top:1px; padding-bottom:1px;">
                <div
                    style="display:flex; width:100%; margin-top:3px; padding-left:6px; color:#ffffff; text-align:left;  font-size:12px; overflow:hidden; white-space: nowrap;">
                    <div id="media_now_play_name">
                        {{ str_replace('-', ' ', $file->name) }}
                    </div>
                    <div id="playtime" style="margin-left:10px; color:orangered;">
                        Loading...
                    </div>
                </div>
            </div>

        </div>

    </div>


    <script type="text/javascript">
        function mute_control() {
            wavesurfer.toggleMute();
            if (wavesurfer.getMute()) {
                $("#mute_icon").css("color", "red");
            } else {
                $("#mute_icon").css("color", "#1a1a1a");
            }
        }
    </script>

    <script type="text/javascript">
        var player_init = 0;

        $(document).ready(function() {
            $('.container').fadeIn('fast');
        });

        $(document).ready(function() {
            var playtime_timer = setInterval(function() {
                if ((wavesurfer.getCurrentTime() >= (wavesurfer.getDuration() - 0.01)) && (wavesurfer
                        .getCurrentTime() > 0)) {
                    $('#playtime').html(wavesurfer.getDuration().toFixed(1) + " / " + wavesurfer
                        .getDuration().toFixed(1));
                    wavesurfer.setPlayEnd(wavesurfer.getDuration() - 0.01);
                    wavesurfer.stop();
                    //clearInterval(playtime_timer);
                } else {
                    wavesurfer.setPlayEnd(wavesurfer.getDuration() - 0.01);
                    if (wavesurfer.getDuration() == 0) {
                        $('#playtime').html("Loading...");
                    } else {
                        if (wavesurfer.getVolume() > 0) {
                            document.querySelector('#volume').value = wavesurfer.getVolume();
                            $("#mute_icon").css("color", "#1a1a1a");
                            wavesurfer.setMute(false);
                        }
                        var playtime_rate = wavesurfer.getCurrentTime() / wavesurfer.getDuration() * 100;
                        playtime_rate = playtime_rate.toFixed(2);
                        $('#playtime').html(wavesurfer.getCurrentTime().toFixed(1) + " / " + wavesurfer
                            .getDuration().toFixed(1) +
                            "<span style='color:#888888; font-size:7px;'>SEC</span> (" + playtime_rate +
                            "<span style='color:#888888; font-size:7px;'>%</span>)");

                        if (player_init == 0) {
                            wavesurfer.stop();
                            player_init = 1;
                        }
                    }
                }
            }, 100);
        });
    </script>





    <script type="text/javascript">
        function media_load(pf_id, pf, no) {
            if (pf_id == "" || no == "") {
                return false;
            } else {
                $('#playtime').html('Loading...');
                $.ajax({
                    url: 'ajax_media_load.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        pf_id: pf_id,
                        pf: pf,
                        no: no,
                        action: 'ajax'
                    },
                    success: function(data) {
                        if (data) {
                            var data_arr = data.split('Æ');
                            if (data_arr[0] == 'SUCCESS') {
                                wavesurfer.load(data_arr[1]);
                                $(".media_name").css("background", "transparent");
                                $(".media_name").css("border-bottom", "2px solid #888888");
                                $(".media_name").css("color", "#888888");
                                $("#media_name_" + no).css("background", "transparent");
                                $("#media_name_" + no).css("border-bottom", "2px solid #007bff");
                                $("#media_name_" + no).css("color", "#ffffff");
                                $("#media_now_play_name").html(data_arr[2]);
                            } else if (data_arr[0] == 'FAIL') {
                                alert(data_arr[1]);
                            }
                        }
                    }
                });
            }
        }
    </script>

    {{-- <script type="text/javascript">
        //IE에서 금지합니다
        function click() {
            if ((event.button == 2) || (event.button == 3)) {
                return false
            }
        }
        document.onmousedown = click

        //IE 이외에서도 금지합니다-Netscape
        if (navigator.appName == "Netscape") {
            document.captureEvents(Event.MOUSEDOWN)
            document.onmousedown = checkClick

            function checkClick(ev) {
                if (ev.which != 1) {
                    return false
                }
            }
        }
    </script> --}}
    {{-- 
    <script type="text/javascript">
        // F12 버튼 방지
        $(document).ready(function() {
            $(document).bind('keydown', function(e) {
                if (e.keyCode == 123 /* F12 */ ) {
                    e.preventDefault();
                    e.returnValue = false;
                }
            });
        });

        // 우측 클릭 방지
        document.onmousedown = disableclick;
        status = "Right click is not available.";

        function disableclick(event) {
            if (event.button == 2 || event.button == 3) {
                alert(status);
                return false;
            }
        }
    </script> --}}

</body>

</html>
