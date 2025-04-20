<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Phone (arrival) Birthday</title>
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

    <div class="container">
        <div id="data-wait">
            <b><i id="status">Present card to continue with arrival</i></b>
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

            <div class="btn btn-primary" onclick="registerArrival();" style="margin-top: 15px;" id="register-0">
                Register arrival
            </div>
            <div class="btn-group" style="margin-top: 15px; width: 100%; display: grid; grid-template-columns: repeat(2, 1fr);" id="register-1">
                <div class="btn btn-primary" onclick="registerArrival();">
                    Register again
                </div>
                <div class="btn btn-danger" onclick="unregisterArrival();">
                    Unregister
                </div>
            </div>
            <div class="btn btn-warning" onclick="toggleAlcohol();" style="margin-top: 5px;">
                Toggle alcohol
            </div>
            <div class="btn btn-warning" onclick="toggleConference();" style="margin-top: 5px;">
                Toggle conference
            </div>
            <div class="btn btn-warning" onclick="toggleActivities();" style="margin-top: 5px;">
                Toggle activities
            </div>
            <div class="btn btn-secondary" onclick="complete();" style="margin-top: 5px;">
                Complete operation
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
            document.getElementById("status").innerText = "Present card to continue with arrival";
        }

        async function toggleActivities() {
            if (!window.currentUser) return;

            if (typeof window.currentUser['activities'] === "boolean") window.currentUser['activities'] = !window.currentUser['activities'];
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

        async function registerArrival() {
            if (!window.currentUser) return;

            window.currentUser['arrival'] = new Date().toISOString();
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        async function unregisterArrival() {
            if (!window.currentUser) return;

            window.currentUser['arrival'] = null;
            await saveUser(window.currentUser, window.currentUser['id']);
        }

        window.users = {};
        window.items = [];

        refresh();

        async function refresh() {
            window.users = await (await window.fetch("/db.php")).json();
            window.items = await (await window.fetch("/items.json")).json();
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

                if (currentUser['arrival']) {
                    document.getElementById("register-0").style.display = "none";
                    document.getElementById("register-1").style.display = "grid";
                } else {
                    document.getElementById("register-0").style.display = "block";
                    document.getElementById("register-1").style.display = "none";
                }
            } else {
                window.lastRead = null;
                window.currentUser = null;

                document.getElementById("data-wait").style.display = "block";
                document.getElementById("data-info").style.display = "none";

                if (shown) {
                    document.getElementById("status").innerText = "Failed to read card, is it valid?";
                } else {
                    document.getElementById("status").innerText = "Present card to continue with arrival";
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
                preferredCamera: 'environment'
            },
        );

        qrScanner.start();
    </script>
</body>
</html>