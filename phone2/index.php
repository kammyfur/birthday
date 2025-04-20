<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Phone (conference) Birthday</title>
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
        <div id="data-list" class="list-group"></div>
    </div>

    <script>
        window.lastRead = null;
        window.shown = false;
        window.currentUser = null;
        window.lastUpdate = new Date();

        window.users = {};
        window.items = [];
        window.activatedUsers = [];

        refresh();

        async function refresh() {
            window.users = await (await window.fetch("/db.php")).json();
            window.items = await (await window.fetch("/items.json")).json();

            window.conferenceUsers = Object.keys(window.users).filter(i => (typeof window.users[i]['conference'] === "boolean" && window.users[i]['conference']) || (typeof window.users[i]['conference'] !== "boolean" && !window.users[i]['conference']));

            document.getElementById("data-list").innerHTML = Object.keys(window.users).filter(i => (typeof window.users[i]['conference'] === "boolean" && window.users[i]['conference']) || (typeof window.users[i]['conference'] !== "boolean" && !window.users[i]['conference']) || window.activatedUsers.includes(i)).map(i => `
            <a href="#" onclick="unregister('${i}');" class="list-group-item list-group-item-action text-white bg-${window.activatedUsers.includes(i) ? (window.conferenceUsers.includes(i) ? 'success' : 'primary') : 'danger'}">${window.users[i]['name']}</a>
            `).join("");
        }

        function unregister(user) {
            window.activatedUsers = window.activatedUsers.filter(i => i !== user);
            refresh();
            refreshUI();
        }

        function refreshUI() {
            if (users[lastRead]) {
                if (!window.activatedUsers.includes(window.lastRead)) window.activatedUsers.push(window.lastRead);
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