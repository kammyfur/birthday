<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Phone (activities) Birthday</title>
    <script src="/global.js"></script>
    <style>
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    </style>
</head>
<body>
    <video id="preview" style="aspect-ratio: 13.47/9; border-bottom: 1px solid #ccc; width: 100%; background: black;"></video>

    <div class="container" style="margin-top: 5px;">
        <div class="btn-group" style="width: 100%;">
            <a id="btn-registration" onclick="switchRegistration();" class="btn-primary btn" style="border-bottom-left-radius: 0;">Registration</a>
            <a id="btn-score" onclick="switchScore();" class="btn-outline-primary btn" style="border-bottom-right-radius: 0;">Score</a>
        </div>

        <div id="activity-selector" class="btn-group" style="width: 100%; border-top: none;">
            <a id="btn-activity-1" onclick="selectGame(0);" class="btn-primary btn disabled" style="border-top: none; border-top-left-radius: 0;">-</a>
            <a id="btn-activity-2" onclick="selectGame(1);" class="btn-outline-primary btn disabled" style="border-top: none;">-</a>
            <a id="btn-activity-3" onclick="selectGame(2);" class="btn-outline-primary btn disabled" style="border-top: none;">-</a>
            <a id="btn-activity-4" onclick="selectGame(3);" class="btn-outline-primary btn disabled" style="border-top: none;">-</a>
            <a id="btn-activity-5" onclick="selectGame(4);" class="btn-outline-primary btn disabled" style="border-top: none; border-top-right-radius: 0;">-</a>
        </div>

        <hr>

        <div id="data-list" class="list-group"></div>
        <div id="score" class="list-group" style="display: none;"></div>
    </div>

    <script>
        window.lastRead = null;
        window.shown = false;
        window.currentUser = null;
        window.lastUpdate = new Date();

        window.selectedGame = 0;

        window.users = {};
        window.items = [];
        window.activatedUsers = [];

        refresh();

        function selectGame(i) {
            window.selectedGame = i;

            Array.from(document.getElementById("activity-selector").children).map(i => {
                i.classList.remove("btn-primary");
                i.classList.add("btn-outline-primary");
            });

            document.getElementById("btn-activity-" + (i + 1)).classList.remove("btn-outline-primary");
            document.getElementById("btn-activity-" + (i + 1)).classList.add("btn-primary");

            for (let id of Object.keys(window.users)) {
                if (typeof window.users[id]["score"][selectedGame] === "number") {
                    if (document.getElementById("score-" + id)) {
                        document.getElementById("score-" + id).value = window.users[id]["score"][selectedGame];

                        if (window.users[id]["score"][window.selectedGame] < 999) {
                            document.getElementById("score-" + id + "-add").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-add").classList.add("disabled");
                        }

                        if (window.users[id]["score"][window.selectedGame] > 0) {
                            document.getElementById("score-" + id + "-remove").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-remove").classList.add("disabled");
                        }
                    }
                }
            }
        }

        function switchScore() {
            if (document.getElementById("btn-registration").classList.contains("btn-primary")) {
                document.getElementById("btn-registration").classList.remove("btn-primary");
                document.getElementById("btn-score").classList.add("btn-primary");
                document.getElementById("btn-score").classList.remove("btn-outline-primary");
                document.getElementById("btn-registration").classList.add("btn-outline-primary");

                Array.from(document.getElementById("activity-selector").children).filter(i => i.innerText.trim() !== "-").map(i => i.classList.remove("disabled"));
                Array.from(document.getElementById("activity-selector").children).filter(i => i.innerText.trim() === "-").map(i => i.classList.add("disabled"));

                document.getElementById("data-list").style.display = "none";
                document.getElementById("score").style.display = "";

                document.getElementById("score").innerHTML = Object.keys(window.users).filter(i => (typeof window.users[i]['registered'] === "boolean" && window.users[i]['registered'])).map(i => `
                <div class="list-group-item" style="display: grid; grid-template-columns: 1fr max-content;">
                    <span style="display: flex; align-items: center;">${window.users[i]['name']}</span>
                    <div class="input-group" style="font-family: monospace; display: grid; grid-template-columns: max-content max-content calc(3ch + 0.75rem * 2 + 2px) max-content max-content;">
                        <button class="btn btn-primary disabled" onclick="removePoint('${i}', 10);" id="score-${i}-remove2">&lt;</button>
                        <button class="btn btn-primary disabled" onclick="removePoint('${i}', 1);" id="score-${i}-remove">-</button>
                        <input type="text" value="0" style="text-align: center; font-family: monospace; width: 100%; border-right: none;" disabled class="form-control" id="score-${i}">
                        <button class="btn btn-primary" onclick="addPoint('${i}', 1);" id="score-${i}-add">+</button>
                        <button class="btn btn-primary" onclick="addPoint('${i}', 10);" id="score-${i}-add2">&gt;</button>
                    </div>
                </div>
                `).join("");
            }
        }

        function addPoint(id, score) {
            if (!score) score = 1;

            if (window.users[id]["score"][window.selectedGame] < 1000 - score) window.users[id]["score"][window.selectedGame] += score;
            document.getElementById("score-" + id).value = (parseInt(document.getElementById("score-" + id).value) + score).toString();

            if (window.users[id]["score"][window.selectedGame] < 999) {
                document.getElementById("score-" + id + "-add").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-add").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] < 990) {
                document.getElementById("score-" + id + "-add2").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-add2").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] > 0) {
                document.getElementById("score-" + id + "-remove").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-remove").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] > 9) {
                document.getElementById("score-" + id + "-remove2").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-remove2").classList.add("disabled");
            }

            saveUser(window.users[id], id);
        }

        function removePoint(id, score) {
            if (!score) score = 1;

            if (window.users[id]["score"][window.selectedGame] > 0) window.users[id]["score"][window.selectedGame] -= score;
            document.getElementById("score-" + id).value = (parseInt(document.getElementById("score-" + id).value) - score).toString();

            if (window.users[id]["score"][window.selectedGame] < 999) {
                document.getElementById("score-" + id + "-add").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-add").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] < 990) {
                document.getElementById("score-" + id + "-add2").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-add2").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] > 0) {
                document.getElementById("score-" + id + "-remove").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-remove").classList.add("disabled");
            }

            if (window.users[id]["score"][window.selectedGame] > 9) {
                document.getElementById("score-" + id + "-remove2").classList.remove("disabled");
            } else {
                document.getElementById("score-" + id + "-remove2").classList.add("disabled");
            }

            saveUser(window.users[id], id);
        }

        function switchRegistration() {
            if (document.getElementById("btn-score").classList.contains("btn-primary")) {
                document.getElementById("btn-score").classList.remove("btn-primary");
                document.getElementById("btn-registration").classList.add("btn-primary");
                document.getElementById("btn-registration").classList.remove("btn-outline-primary");
                document.getElementById("btn-score").classList.add("btn-outline-primary");

                Array.from(document.getElementById("activity-selector").children).map(i => i.classList.add("disabled"));

                document.getElementById("data-list").style.display = "";
                document.getElementById("score").style.display = "none";
            }
        }

        async function refresh() {
            window.users = await (await window.fetch("/db.php")).json();
            window.activities = (await (await window.fetch("/activities.json")).json())['list'];

            document.getElementById("btn-activity-1").innerText = activities[0] ?? "-";
            document.getElementById("btn-activity-2").innerText = activities[1] ?? "-";
            document.getElementById("btn-activity-3").innerText = activities[2] ?? "-";
            document.getElementById("btn-activity-4").innerText = activities[3] ?? "-";
            document.getElementById("btn-activity-5").innerText = activities[4] ?? "-";

            for (let id of Object.keys(window.users)) {
                if (typeof window.users[id]["score"][selectedGame] === "number") {
                    if (document.getElementById("score-" + id)) {
                        document.getElementById("score-" + id).value = window.users[id]["score"][selectedGame];

                        if (window.users[id]["score"][window.selectedGame] < 999) {
                            document.getElementById("score-" + id + "-add").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-add").classList.add("disabled");
                        }

                        if (window.users[id]["score"][window.selectedGame] < 990) {
                            document.getElementById("score-" + id + "-add2").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-add2").classList.add("disabled");
                        }

                        if (window.users[id]["score"][window.selectedGame] > 0) {
                            document.getElementById("score-" + id + "-remove").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-remove").classList.add("disabled");
                        }

                        if (window.users[id]["score"][window.selectedGame] > 9) {
                            document.getElementById("score-" + id + "-remove2").classList.remove("disabled");
                        } else {
                            document.getElementById("score-" + id + "-remove2").classList.add("disabled");
                        }
                    }
                }
            }

            window.conferenceUsers = Object.keys(window.users).filter(i => (typeof window.users[i]['activities'] === "boolean" && window.users[i]['activities']) || (typeof window.users[i]['activities'] !== "boolean" && !window.users[i]['activities']));

            document.getElementById("data-list").innerHTML = Object.keys(window.users).filter(i => (typeof window.users[i]['activities'] === "boolean" && window.users[i]['activities']) || (typeof window.users[i]['activities'] !== "boolean" && !window.users[i]['activities']) || window.activatedUsers.includes(i) || window.users[i]['registered']).map(i => `
            <a href="#" onclick="unregister('${i}');" class="list-group-item list-group-item-action text-white bg-${window.activatedUsers.includes(i) || window.users[i]['registered'] ? (window.conferenceUsers.includes(i) ? 'success' : 'primary') : 'danger'}">${window.users[i]['name']}</a>
            `).join("");
        }

        function unregister(user) {
            window.activatedUsers = window.activatedUsers.filter(i => i !== user);

            let current = users[user];
            current.registered = false;
            saveUser(current, user);
            refresh();
            refreshUI();
        }

        function refreshUI() {
            if (users[lastRead]) {
                if (!window.activatedUsers.includes(window.lastRead)) window.activatedUsers.push(window.lastRead);

                let current = users[window.lastRead];
                current.registered = true;
                saveUser(current, window.lastRead);

                window.lastRead = null;
            } else {
                window.lastRead = null;
                window.currentUser = null;
            }
        }

        setInterval(() => {
            if (new Date().getTime() - lastUpdate.getTime() > 200 && shown) {
                shown = false;
                refreshUI();
            }
        });

        setInterval(async () => {
            await refresh();
            refreshUI();
        }, 1000);
    </script>

    <script type="module">
        import QrScanner from '/qr-scanner.min.js';

        const qrScanner = new QrScanner(
            document.getElementById("preview"),
            result => {
                lastUpdate = new Date();
                shown = true;

                if (result.data !== lastRead) {
                    console.log(result.data);
                    lastRead = result.data;
                }

                refreshUI();
            },
            {
                preferredCamera: 'environment'
            },
        );

        qrScanner.start();
    </script>
</body>
</html>