<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Remote Birthday</title>
    <script src="/global.js"></script>
    <style>
        #volume-visualizer, #volume-visualizer-2, #volume-visualizer-3, #volume-visualizer-mini, #volume-visualizer-mini-2 {
            --volume: 0%;
            position: relative;
            width: 400px;
            height: 48px;
            background-color: var(--bs-light);
        }

        #volume-visualizer::before, #volume-visualizer-2::before, #volume-visualizer-3::before, #volume-visualizer-mini::before, #volume-visualizer-mini-2::before, #volume-visualizer-mini-3::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--volume);
            max-width: 100%;
            background-image: linear-gradient(90deg, var(--bs-success) 0%, var(--bs-success) 54.999999%, var(--bs-warning) 55%, var(--bs-warning) 79.999999%, var(--bs-danger) 80%, var(--bs-danger) 100%);
            background-size: 400px;
        }

        #volume-indicator, #volume-indicator-2, #volume-average, #volume-average-2, #volume-indicator-3, #volume-time-3 {
            font-family: monospace;
        }

        #volume-visualizer-mini, #volume-visualizer-mini-2 {
            height: 16px;
        }

        #volume-graph, #volume-graph-2, #volume-graph-3 {
            width: 512px;
            height: 256px;
            display: inline-flex;
            align-items: end;
            justify-content: end;
        }

        #volume-graph-2 {
            background-color: var(--bs-light);
        }

        .volume-graph-item {
            max-height: 100%;
            width: 1px;
            display: inline-block;
        }

        #volume-graph-2 .volume-graph-item {
            background-color: var(--bs-primary);
        }

        #volume-graph .volume-graph-item {
            background-color: var(--bs-info);
            opacity: .5;
        }

        #volume-graph-3 .volume-graph-item {
            background-color: var(--bs-secondary);
            opacity: .5;
        }

        #volume-graph, #volume-graph-2, #volume-graph-3 {
            position: absolute;
        }

        #volume-graph-2 {
            z-index: 10;
        }

        #volume-graph {
            z-index: 20;
        }

        #volume-graph-3 {
            z-index: 30;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-top: 20px;">Remote</h1>
        <div id="loader">
            Downloading songs... <span id="load-progress">0</span>/<span id="load-total">0</span>
        </div>
        <div id="app" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr;">
                <div>
                    <h2>Music player</h2>
                    <p style="font-family: monospace;">
                        <button id="btn-start" onclick="start();">Start</button> <button id="btn-stop" onclick="stop();" disabled>Stop</button> <button id="btn-skip" onclick="skip();" disabled>Skip</button>
                    </p>
                    <table>
                        <tr>
                            <td style="font-family: monospace; padding-right: 20px;">
                                <b>In:</b>
                            </td>
                            <td style="padding-right: 20px;">
                                <div id="volume-visualizer"></div>
                                <div id="volume-visualizer-mini"></div>
                            </td>
                            <td>
                                <div id="volume-indicator">000</div>
                                <div id="volume-average">000</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; padding-right: 20px;">
                                <b>Out:</b>
                            </td>
                            <td style="padding-right: 20px;">
                                <div id="volume-visualizer-2"></div>
                                <div id="volume-visualizer-mini-2"></div>
                            </td>
                            <td>
                                <div id="volume-indicator-2">000</div>
                                <div id="volume-average-2">000</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; padding-right: 20px;">
                                <b>Prg:</b>
                            </td>
                            <td style="padding-right: 20px;">
                                <div id="volume-visualizer-3"></div>
                            </td>
                            <td>
                                <div id="volume-indicator-3">000</div>
                                <div id="volume-time-3">00:00</div>
                            </td>
                        </tr>
                    </table>
                    <div style="font-family: monospace; margin-top: 20px; margin-bottom: 20px;">
                        System Volume:&nbsp; <span id="volume-difference">0.000</span>%
                    </div>
                    <div id="volume-graph-2"></div>
                    <div id="volume-graph"></div>
                    <div id="volume-graph-3"></div>
                </div>
                <div>
                    <h2>Display preferences</h2>
                    <select id="display-mode" onchange="updateMode();">
                        <option value="intro">Getting ready</option>
                        <option value="food">Menu, food points and other</option>
                        <option value="conference">Delta conference</option>
                        <option value="active">Activities</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateMode() {
            window.config['mode'] = document.getElementById("display-mode").value;
            saveDisplay(window.config);
        }

        let avg1l = [];
        let avg2l = [];

        window.volumeMultiplier = 1;
        window.diffInput = 0;
        window.realVolume = 100;
        window.volumeHistory = [];
        window.volumeHistory2 = [];
        window.volumeHistory3 = [];

        window.selected = 0;

        function start() {
            play(selected);
        }

        function stop() {
            songs[window.currentSongID].stop();

            document.getElementById("btn-stop").disabled = true;
            document.getElementById("btn-start").disabled = false;
            document.getElementById("btn-skip").disabled = true;
        }

        function skip() {
            songs[window.currentSongID].stop();
            let selected = window.currentSongID;

            if (selected + 1 <= 9) {
                selected++;
            } else {
                selected = 0;
            }

            console.log(selected);
            play(selected);
        }

        function fixTime(seconds) {
            let mins = Math.floor(seconds / 60);
            let secs = Math.floor(seconds - (mins * 60));

            return fix2(mins) + ":" + fix2(secs);
        }

        setInterval(() => {
            let avg1 = 0;
            let avg2 = 0;

            try {
                avg1 = avg1l.reduce((a, b) => a + b) / avg1l.length;
            } catch (e) {}

            try {
                avg2 = avg2l.reduce((a, b) => a + b) / avg2l.length;
            } catch (e) {}

            document.getElementById("volume-visualizer-mini").style.setProperty("--volume", avg1 + "%");
            document.getElementById("volume-visualizer-mini-2").style.setProperty("--volume", avg2 + "%");
            document.getElementById("volume-average").innerText = fix3(avg1);
            document.getElementById("volume-average-2").innerText = fix3(avg2);

            if (avg1 >= 80) {
                document.getElementById("volume-average").className = "text-danger";
            } else if (avg1 >= 55) {
                document.getElementById("volume-average").className = "text-warning";
            } else {
                document.getElementById("volume-average").className = "";
            }

            if (avg2 >= 80) {
                document.getElementById("volume-average-2").className = "text-danger";
            } else if (avg2 >= 55) {
                document.getElementById("volume-average-2").className = "text-warning";
            } else {
                document.getElementById("volume-average-2").className = "";
            }

            let progress = (currentSampleSource.context.currentTime / songs[currentSongID].audio.duration) * 100;

            if (progress >= 100) {
                skip();
            }

            document.getElementById("volume-visualizer-3").style.setProperty('--volume', progress + '%');
            document.getElementById("volume-indicator-3").innerText = fix3(progress);
            document.getElementById("volume-time-3").innerText = fixTime(songs[currentSongID].audio.duration - currentSampleSource.context.currentTime);

            if (progress >= 80) {
                document.getElementById("volume-indicator-3").className = "text-danger";
            } else if (progress >= 55) {
                document.getElementById("volume-indicator-3").className = "text-warning";
            } else {
                document.getElementById("volume-indicator-3").className = "";
            }

            /*window.volumeMultiplier = 0.5 / (avg2 / 100);
            if (!isFinite(window.volumeMultiplier)) window.volumeMultiplier = 1;
            document.getElementById("volume-multiplier").innerText = (volumeMultiplier).toFixed(3).substring(0, 5);*/

            window.diffInput = avg1 + 30;
            if (window.diffInput >= 45) window.diffInput = 70 - avg1;
            window.realVolume = window.diffInput;
            document.getElementById("volume-difference").innerText = diffInput.toFixed(3).substring(0, 5);

            try {
                songs[currentSongID].gain.gain.value = realVolume / 100;
            } catch (e) {}

            window.volumeHistory.push(realVolume);
            window.volumeHistory = window.volumeHistory.reverse().splice(0, 512).reverse();
            document.getElementById("volume-graph").innerHTML = window.volumeHistory.map(i => `<div class="volume-graph-item" style="height: ${i}%;"></div>`).join("");
        }, 30);

        let toDownload = JSON.parse(`<?= json_encode(array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/music"), function ($i) { return !str_starts_with($i, "."); }))) ?>`);
        let songs = [];
        let downloaded = 0;
        window.currentSongID = 0;

        setInterval(async () => {
            window.config = (await (await window.fetch("/display.json")).json());
            document.getElementById("display-mode").value = config['mode'];
        }, 1000);

        (async () => {
            window.config = (await (await window.fetch("/display.json")).json());
            document.getElementById("display-mode").value = config['mode'];
        })();

        function fix3(number) {
            number = Math.round(number);
            return "000".substring(0, 3 - number.toString().length) + number.toString();
        }

        function fix2(number) {
            number = Math.round(number);
            return "00".substring(0, 2 - number.toString().length) + number.toString();
        }

        setInterval(() => {
            try {
                const volumeVisualizer = document.getElementById('volume-visualizer-2');
                let volume = songs[currentSongID].calculateVolume() + (songs[currentSongID].calculateVolume() * (1 - (realVolume / 100)));
                if (isNaN(volume)) volume = 0;

                volumeVisualizer.style.setProperty('--volume', volume + '%');
                document.getElementById("volume-indicator-2").innerText = fix3(volume);

                if (volume > 0) {
                    avg2l.unshift(volume);
                    avg2l = avg2l.splice(0, 1000);
                }

                window.volumeHistory2.push(volume);
                window.volumeHistory2 = window.volumeHistory2.reverse().splice(0, 512).reverse();
                document.getElementById("volume-graph-2").innerHTML = window.volumeHistory2.map(i => `<div class="volume-graph-item" style="height: ${i}%;"></div>`).join("");

                if (volume >= 80) {
                    document.getElementById("volume-indicator-2").className = "text-danger";
                } else if (volume >= 55) {
                    document.getElementById("volume-indicator-2").className = "text-warning";
                } else {
                    document.getElementById("volume-indicator-2").className = "";
                }
            } catch (e) {}
        }, 30);

        function play(id) {
            window.currentSongID = id;
            console.log(window.currentSongID);
            songs[currentSongID].start();

            document.getElementById("btn-stop").disabled = false;
            document.getElementById("btn-start").disabled = true;
            document.getElementById("btn-skip").disabled = false;
        }

        async function afterDownload() {
            downloaded++;
            document.getElementById("load-progress").innerText = downloaded.toString();

            let volumeCallback;

            if (downloaded >= 10) {
                document.getElementById("loader").style.display = "none";
                document.getElementById("app").style.display = "";

                const volumeVisualizer = document.getElementById('volume-visualizer');
                try {
                    const audioStream = await navigator.mediaDevices.getUserMedia({
                        audio: {
                            echoCancellation: true
                        }
                    });
                    const audioContext = new AudioContext();
                    const audioSource = audioContext.createMediaStreamSource(audioStream);
                    const analyser = audioContext.createAnalyser();
                    analyser.fftSize = 512;
                    analyser.minDecibels = -127;
                    analyser.maxDecibels = 0;
                    analyser.smoothingTimeConstant = 0.4;
                    audioSource.connect(analyser);
                    const volumes = new Uint8Array(analyser.frequencyBinCount);
                    volumeCallback = () => {
                        analyser.getByteFrequencyData(volumes);
                        let volumeSum = 0;
                        for (const volume of volumes)
                            volumeSum += volume;

                        const averageVolume = volumeSum / volumes.length;
                        const volume = (averageVolume * 100 / 127);

                        volumeVisualizer.style.setProperty('--volume', volume + '%');
                        document.getElementById("volume-indicator").innerText = fix3(volume);

                        avg1l.unshift(averageVolume * 100 / 127);
                        avg1l = avg1l.splice(0, 1000);

                        window.volumeHistory3.push(volume);
                        window.volumeHistory3 = window.volumeHistory3.reverse().splice(0, 512).reverse();
                        document.getElementById("volume-graph-3").innerHTML = window.volumeHistory3.map(i => `<div class="volume-graph-item" style="height: ${i}%;"></div>`).join("");

                        if (volume >= 80) {
                            document.getElementById("volume-indicator").className = "text-danger";
                        } else if (volume >= 55) {
                            document.getElementById("volume-indicator").className = "text-warning";
                        } else {
                            document.getElementById("volume-indicator").className = "";
                        }
                    };
                } catch (e) {
                    console.error('Failed to initialize volume visualizer, simulating instead...', e);
                    let lastVolume = 50;
                    volumeCallback = () => {
                        const volume = Math.min(Math.max(Math.random() * 100, 0.8 * lastVolume), 1.2 * lastVolume);
                        lastVolume = volume;
                        volumeVisualizer.style.setProperty('--volume', volume + '%');
                    };
                }

                let volumeInterval = setInterval(volumeCallback, 30);
            }
        }

        document.getElementById("load-total").innerText = toDownload.length;
        window.currentSampleSource = null;

        for (let item of toDownload) {
            window.fetch("/music/" + item).then((res) => {
                res.blob().then(async (blob) => {
                    let url = URL.createObjectURL(blob);

                    console.log(url);

                    let audio = new Audio();
                    audio.src = url;

                    let context = new AudioContext();
                    let analyser = context.createAnalyser();
                    let sampleBuffer = await context.decodeAudioData(await blob.arrayBuffer());
                    let bufferLength = analyser.frequencyBinCount;
                    let dataArray = new Uint8Array(bufferLength);
                    let sampleSource = null;
                    let gain = context.createGain();

                    analyser.connect(context.destination);
                    //gain.gain.value = 0
                    gain.connect(analyser);

                    songs.push({
                        audio,
                        context,
                        analyser,
                        sampleBuffer,
                        sampleSource,
                        bufferLength,
                        gain,
                        dataArray,
                        start: () => {
                            sampleSource = context.createBufferSource()
                            sampleSource.buffer = sampleBuffer;
                            sampleSource.connect(gain);
                            sampleSource.start();
                            window.currentSampleSource = sampleSource;
                        },
                        stop: () => {
                            currentSampleSource.stop();
                            window.currentSampleSource = null;
                        },
                        calculateVolume: () => {
                            analyser.getByteFrequencyData(dataArray)

                            let sum = 0;
                            for (const amplitude of dataArray) {
                                sum += amplitude * amplitude
                            }

                            const volume = Math.sqrt(sum / dataArray.length);
                            return volume;
                        }
                    });
                    afterDownload();
                })
            });
        }
    </script>
</body>
</html>