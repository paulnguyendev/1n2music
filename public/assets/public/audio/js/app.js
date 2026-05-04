// Create a WaveSurfer instance

// Init on DOM ready
document.addEventListener("DOMContentLoaded", function () {
    wavesurfer = WaveSurfer.create({
        container: "#waveform",
        waveColor: "#A8DBA8",
        progressColor: "#3B8686",
        cursorColor: "#3B8686",
        height: 100,
        barHeight: 1,
        barWidth: 1,
        mediaType: "audio",
        loopSelection: true,
        barRadius: 0,
        cursorWidth: 2,
        barGap: 2,
        backend: "WebAudio",
        mediaControls: true,
    });
});

// Bind controls
document.addEventListener("DOMContentLoaded", function () {
    var playPause = document.querySelector("#playPause");
    playPause.addEventListener("click", function () {
        wavesurfer.playPause();
    });

    // Toggle play/pause text
    wavesurfer.on("play", function () {
        document.querySelector("#play").style.display = "none";
        document.querySelector("#pause").style.display = "";
    });
    wavesurfer.on("pause", function () {
        document.querySelector("#play").style.display = "";
        document.querySelector("#pause").style.display = "none";
    });

    // The playlist links
    var links = document.querySelectorAll("#playlist a");
    var currentTrack = 0;

    // Load a track by index and highlight the corresponding link
    var setCurrentSong = function (index) {
        links[currentTrack].classList.remove("active");
        currentTrack = index;
        links[currentTrack].classList.add("active");
        wavesurfer.load(links[currentTrack].href);
    };

    // Load the track on click
    Array.prototype.forEach.call(links, function (link, index) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            setCurrentSong(index);
        });
    });

    // Play on audio load
    wavesurfer.on("ready", function () {
        wavesurfer.play();
    });

    wavesurfer.on("error", function (e) {
        console.warn(e);
    });

    // Go to the next track on finish
    wavesurfer.on("finish", function () {
        setCurrentSong((currentTrack + 1) % links.length);
    });

    // Load the first track
    setCurrentSong(currentTrack);

    // Equalizer

    var EQ = [
        {
            f: 32,
            type: "lowshelf",
        },
        {
            f: 64,
            type: "peaking",
        },
        {
            f: 125,
            type: "peaking",
        },
        {
            f: 250,
            type: "peaking",
        },
        {
            f: 500,
            type: "peaking",
        },
        {
            f: 1000,
            type: "peaking",
        },
        {
            f: 2000,
            type: "peaking",
        },
        {
            f: 4000,
            type: "peaking",
        },
        {
            f: 8000,
            type: "peaking",
        },
        {
            f: 16000,
            type: "highshelf",
        },
    ];

    // Create filters
    var filters = EQ.map(function (band) {
        var filter = wavesurfer.backend.ac.createBiquadFilter();
        filter.type = band.type;
        filter.gain.value = 0;
        filter.Q.value = 1;
        filter.frequency.value = band.f;
        return filter;
    });

    // Connect filters to wavesurfer
    wavesurfer.backend.setFilters(filters);

    wavesurfer.setVolume(0.525);
    document.querySelector("#volume").value = wavesurfer.backend.getVolume();

    // Bind filters to vertical range sliders
    var container = document.querySelector("#equalizer");
    filters.forEach(function (filter) {
        var input = document.createElement("input");
        wavesurfer.util.extend(input, {
            type: "range",
            min: -40,
            max: 40,
            value: 0,
            title: filter.frequency.value,
        });
        input.style.display = "inline-block";
        input.setAttribute("orient", "vertical");
        wavesurfer.drawer.style(input, {
            webkitAppearance: "slider-vertical",
            width: "50px",
            height: "150px",
            display: "none",
        });
        container.appendChild(input);

        var onChange = function (e) {
            filter.gain.value = ~~e.target.value;
        };

        input.addEventListener("input", onChange);
        input.addEventListener("change", onChange);
    });

    var volumeInput = document.querySelector("#volume");
    var onChangeVolume = function (e) {
        wavesurfer.setVolume(e.target.value);
        console.log(e.target.value);
    };
    volumeInput.addEventListener("input", onChangeVolume);
    volumeInput.addEventListener("change", onChangeVolume);

    // For debugging
    wavesurfer.filters = filters;
});

/*  ́›ë˜ ́½”ë“œ


// Create a WaveSurfer instance
var wavesurfer;

// Init on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: '#428bca',
        progressColor: '#31708f',
        height: 120,
        barWidth: 3
    });
});

// Bind controls
document.addEventListener('DOMContentLoaded', function() {
    var playPause = document.querySelector('#playPause');
    playPause.addEventListener('click', function() {
        wavesurfer.playPause();
    });

    // Toggle play/pause text
    wavesurfer.on('play', function() {
        document.querySelector('#play').style.display = 'none';
        document.querySelector('#pause').style.display = '';
    });
    wavesurfer.on('pause', function() {
        document.querySelector('#play').style.display = '';
        document.querySelector('#pause').style.display = 'none';
    });

    // The playlist links
    var links = document.querySelectorAll('#playlist a');
    var currentTrack = 0;

    // Load a track by index and highlight the corresponding link
    var setCurrentSong = function(index) {
        links[currentTrack].classList.remove('active');
        currentTrack = index;
        links[currentTrack].classList.add('active');
        wavesurfer.load(links[currentTrack].href);
    };

    // Load the track on click
    Array.prototype.forEach.call(links, function(link, index) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            setCurrentSong(index);
        });
    });

    // Play on audio load
    wavesurfer.on('ready', function() {
        wavesurfer.play();
    });

    wavesurfer.on('error', function(e) {
        console.warn(e);
    });

    // Go to the next track on finish
    wavesurfer.on('finish', function() {
        setCurrentSong((currentTrack + 1) % links.length);
    });

    // Load the first track
    setCurrentSong(currentTrack);
});


*/
