<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Controller Birthday</title>
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
    <div style="display: grid; grid-template-columns: 1fr 2.5fr; position: fixed; inset: 0;">
        <div style="border-right: 1px solid #ccc;">
            <video id="preview" style="aspect-ratio: 13.47/9; border-bottom: 1px solid #ccc; width: 100%; background: black;"></video>

            <div class="container">
                <div id="data-wait">
                    <b><i id="status">Welcome, please present card</i></b>
                </div>
                <div id="data-info" style="display: none;">
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2"><b id="info-name">-</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">ID:</td>
                                <td id="info-id">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Age:</td>
                                <td id="info-age">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Conference:</td>
                                <td id="info-conference">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Activities:</td>
                                <td id="info-activities">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Alcohol:</td>
                                <td id="info-alcohol">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Arrival:</td>
                                <td id="info-arrival">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 5px;">Score:</td>
                                <td id="info-score">-</td>
                            </tr>
                        </tbody>
                    </table>

                    <b style="margin-top: 10px;">Last orders:</b>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; grid-gap: 10px;">
                        <div>
                            <a style="display: block;" id="info-orders-0-0">-</a>
                            <a style="display: block;" id="info-orders-0-1">-</a>
                            <a style="display: block;" id="info-orders-0-2">-</a>
                            <a style="display: block;" id="info-orders-0-3">-</a>
                            <a style="display: block;" id="info-orders-0-4">-</a>
                        </div>
                        <div>
                            <a style="display: block;" id="info-orders-1-0">-</a>
                            <a style="display: block;" id="info-orders-1-1">-</a>
                            <a style="display: block;" id="info-orders-1-2">-</a>
                            <a style="display: block;" id="info-orders-1-3">-</a>
                            <a style="display: block;" id="info-orders-1-4">-</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background-color: white;">
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); grid-template-rows: repeat(5, 1fr); grid-gap: 10px; height: 100%; padding: 10px;">
                <div class="btn btn-info" id="btn-item-0" onclick="addDrink(0);"></div>
                <div class="btn btn-info" id="btn-item-1" onclick="addDrink(1);"></div>
                <div class="btn btn-info" id="btn-item-2" onclick="addDrink(2);"></div>
                <div class="btn btn-info" id="btn-item-3" onclick="addDrink(3);"></div>
                <div class="btn btn-info" id="btn-item-4" onclick="addDrink(4);"></div>

                <div class="btn btn-primary" id="btn-item-5" onclick="addFood(5);"></div>
                <div class="btn btn-primary" id="btn-item-6" onclick="addFood(6);"></div>
                <div class="btn btn-primary" id="btn-item-7" onclick="addFood(7);"></div>
                <div class="btn btn-primary" id="btn-item-8" onclick="addFood(8);"></div>
                <div class="btn btn-primary" id="btn-item-9" onclick="addFood(9);"></div>

                <div class="btn btn-primary" id="btn-item-10" onclick="addFood(10);"></div>
                <div class="btn btn-primary" id="btn-item-11" onclick="addFood(11);"></div>
                <div class="btn btn-primary" id="btn-item-12" onclick="addFood(12);"></div>
                <div class="btn btn-primary" id="btn-item-13" onclick="addFood(13);"></div>
                <div class="btn btn-primary" id="btn-item-14" onclick="addFood(14);"></div>

                <div class="btn btn-primary" id="btn-item-15" onclick="addFood(15);"></div>
                <div class="btn btn-primary" id="btn-item-16" onclick="addFood(16);"></div>
                <div class="btn btn-primary" id="btn-item-17" onclick="addFood(17);"></div>
                <div class="btn btn-primary" id="btn-item-18" onclick="addFood(18);"></div>
                <div class="btn btn-primary" id="btn-item-19" onclick="addFood(19);"></div>

                <div class="btn btn-secondary" onclick="complete();">
                    Complete operation
                </div>
                <div class="btn btn-warning" onclick="toggleAlcohol();">
                    Toggle alcohol
                </div>
                <div class="btn btn-warning" onclick="toggleConference();">
                    Toggle conference
                </div>
                <div class="btn btn-warning" onclick="toggleActivities();">
                    Toggle activities
                </div>
                <div class="btn btn-danger" onclick="excludeGuest();">
                    Exclude guest from party
                </div>
            </div>
        </div>
    </div>

    <script>
        window.lastRead = null;
        window.shown = false;
        window.currentUser = null;
        window.lastUpdate = new Date();

        function complete() {
            window.lastRead = null;
            window.shown = false;
            window.currentUser = null;

            document.getElementById("data-wait").style.display = "block";
            document.getElementById("data-info").style.display = "none";
            document.getElementById("status").innerText = "Welcome, please present card";
        }

        async function addFood(id) {
            if (!window.currentUser || !window.items[id]) return;

            window.currentUser['foods'].unshift(window.items[id]);
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        async function addDrink(id) {
            if (!window.currentUser || !window.items[id]) return;

            window.currentUser['drinks'].unshift(window.items[id]);
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        async function removeFood(index) {
            if (!window.currentUser || !window.currentUser['foods'][index]) return;

            window.currentUser['foods'].splice(index, 1);
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        async function removeDrink(index) {
            if (!window.currentUser || !window.currentUser['drinks'][index]) return;

            window.currentUser['drinks'].splice(index, 1);
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        async function toggleAlcohol() {
            if (!window.currentUser) return;

            if (typeof window.currentUser['alcohol'] === "boolean") window.currentUser['alcohol'] = !window.currentUser['alcohol'];
            await saveUser(window.currentUser, window.currentUser['id']);

        }

        async function toggleConference() {
            if (!window.currentUser) return;

            if (typeof window.currentUser['conference'] === "boolean") window.currentUser['conference'] = !window.currentUser['conference'];
            await saveUser(window.currentUser, window.currentUser['id']);

        }

        async function toggleActivities() {
            if (!window.currentUser) return;

            if (typeof window.currentUser['activities'] === "boolean") window.currentUser['activities'] = !window.currentUser['activities'];
            await saveUser(window.currentUser, window.currentUser['id']);

        }

        async function excludeGuest() {
            if (!window.currentUser) return;

            window.currentUser['banned'] = true;
            await saveUser(window.currentUser, window.currentUser['id']);
            complete();
        }

        window.users = {};
        window.items = [];

        refresh();

        async function refresh() {
            window.users = await (await window.fetch("/db.php")).json();
            window.items = await (await window.fetch("/items.json")).json();

            let index = 0;
            for (let item of window.items) {
                document.getElementById("btn-item-" + index).innerText = item;
                index++;
            }

            if (index < 20) {
                for (let i = index; i < 20; i++) {
                    document.getElementById("btn-item-" + i).innerText = "";
                }
            }
        }

        function refreshUI() {
            if (users[lastRead]) {
                window.currentUser = users[lastRead];

                document.getElementById("data-wait").style.display = "none";
                document.getElementById("data-info").style.display = "block";

                document.getElementById("info-id").innerText = "#" + currentUser.id.toUpperCase();
                document.getElementById("info-name").innerText = currentUser.name;
                document.getElementById("info-age").innerText = currentUser['age'] + " (" + (currentUser['age'] >= 18 ? "adult" : "minor") + ")";
                document.getElementById("info-alcohol").innerText = currentUser['age'] >= 18 ? (currentUser['alcohol'] ? "Yes" : "No (choice)") : "No (minor)";
                document.getElementById("info-conference").innerText = typeof currentUser['conference'] === "boolean" ? (currentUser['conference'] ? "Yes" : "No") : "-";
                document.getElementById("info-activities").innerText = typeof currentUser['activities'] === "boolean" ? (currentUser['activities'] ? "Yes" : "No") : "-";
                document.getElementById("info-arrival").innerText = currentUser['arrival'] ? new Date(currentUser['arrival']).toString().split("GMT")[0].trim() : "-";
                document.getElementById("info-score").innerText = calculateScore(currentUser).toString();

                document.getElementById("info-orders-0-0").innerText = "-";
                document.getElementById("info-orders-0-1").innerText = "-";
                document.getElementById("info-orders-0-2").innerText = "-";
                document.getElementById("info-orders-0-3").innerText = "-";
                document.getElementById("info-orders-0-4").innerText = "-";
                document.getElementById("info-orders-1-0").innerText = "-";
                document.getElementById("info-orders-1-1").innerText = "-";
                document.getElementById("info-orders-1-2").innerText = "-";
                document.getElementById("info-orders-1-3").innerText = "-";
                document.getElementById("info-orders-1-4").innerText = "-";
                document.getElementById("info-orders-0-0").onclick = null;
                document.getElementById("info-orders-0-1").onclick = null;
                document.getElementById("info-orders-0-2").onclick = null;
                document.getElementById("info-orders-0-3").onclick = null;
                document.getElementById("info-orders-0-4").onclick = null;
                document.getElementById("info-orders-1-0").onclick = null;
                document.getElementById("info-orders-1-1").onclick = null;
                document.getElementById("info-orders-1-2").onclick = null;
                document.getElementById("info-orders-1-3").onclick = null;
                document.getElementById("info-orders-1-4").onclick = null;

                if (currentUser['foods'][0]) {
                    document.getElementById("info-orders-0-0").innerText = currentUser['foods'][0];
                    document.getElementById("info-orders-0-0").onclick = () => {
                        removeFood(0);
                    }
                }
                if (currentUser['foods'][1]) {
                    document.getElementById("info-orders-0-1").innerText = currentUser['foods'][1];
                    document.getElementById("info-orders-0-1").onclick = () => {
                        removeFood(1);
                    }
                }
                if (currentUser['foods'][2]) {
                    document.getElementById("info-orders-0-2").innerText = currentUser['foods'][2];
                    document.getElementById("info-orders-0-2").onclick = () => {
                        removeFood(2);
                    }
                }
                if (currentUser['foods'][3]) {
                    document.getElementById("info-orders-0-3").innerText = currentUser['foods'][3];
                    document.getElementById("info-orders-0-3").onclick = () => {
                        removeFood(3);
                    }
                }
                if (currentUser['foods'][4]) {
                    document.getElementById("info-orders-0-4").innerText = currentUser['foods'][4];
                    document.getElementById("info-orders-0-4").onclick = () => {
                        removeFood(4);
                    }
                }
                if (currentUser['drinks'][0]) {
                    document.getElementById("info-orders-1-0").innerText = currentUser['drinks'][0];
                    document.getElementById("info-orders-1-0").onclick = () => {
                        removeDrink(0);
                    }
                }
                if (currentUser['drinks'][1]) {
                    document.getElementById("info-orders-1-1").innerText = currentUser['drinks'][1];
                    document.getElementById("info-orders-1-1").onclick = () => {
                        removeDrink(1);
                    }
                }
                if (currentUser['drinks'][2]) {
                    document.getElementById("info-orders-1-2").innerText = currentUser['drinks'][2];
                    document.getElementById("info-orders-1-2").onclick = () => {
                        removeDrink(2);
                    }
                }
                if (currentUser['drinks'][3]) {
                    document.getElementById("info-orders-1-3").innerText = currentUser['drinks'][3];
                    document.getElementById("info-orders-1-3").onclick = () => {
                        removeDrink(3);
                    }
                }
                if (currentUser['drinks'][4]) {
                    document.getElementById("info-orders-1-4").innerText = currentUser['drinks'][4];
                    document.getElementById("info-orders-1-4").onclick = () => {
                        removeDrink(4);
                    }
                }
            } else {
                window.lastRead = null;
                window.currentUser = null;

                document.getElementById("data-wait").style.display = "block";
                document.getElementById("data-info").style.display = "none";

                if (shown) {
                    document.getElementById("status").innerText = "Failed to read card, is it valid?";
                } else {
                    document.getElementById("status").innerText = "Welcome, please present card";
                }
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
                preferredCamera: 'user'
            },
        );

        qrScanner.start();
    </script>
</body>
</html>