<!doctype html>
<html lang="en" style="background-color: white;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Display Birthday</title>
    <script src="/global.js"></script>
</head>
<body style="background-color: white; font-size: 28px;">
    <div id="error" style="display: none;position: fixed; inset: 0; z-index: 10; backdrop-filter: blur(30px);"></div>

    <div style="background-color: white; display: grid; grid-template-columns: 1fr 1fr; position: fixed; inset: 0; z-index: 5; grid-gap: 20px;" class="container" id="panes">
        <div style="margin-top: 50px;">
            <img onclick="document.body.requestFullscreen();" src="/icon.svg" style="width: 4em;"><span style="font-family: 'Josefin Sans', sans-serif; font-weight: bold; font-size: 1.75em; vertical-align: middle; margin-left: 10px;">Tableau des scores</span>
            <div class="list-group" id="score-table" style="margin-top: 1em;"></div>

            <hr>
            <div>
                <img src="/delta.svg" alt="Delta" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">Participe à la conférence Delta</span><br>
                <img src="/alcohol.svg" alt="Alcool" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">Consomme de l'alcool</span><br>
                <img src="/activities.svg" alt="Activités" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">Participe aux activités</span><br>
                <img src="/winner.svg" alt="Gagnant" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">1<sup>er·e</sup> en nombre de points</span><br>
                <img src="/medal.svg" alt="Podium" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">Sur le podium</span><br>
                <img src="/banned.svg" alt="Exclus" style="width: 24px;"><span style="vertical-align: middle; margin-left: 5px; display: inline-block;">Exclus de la fête</span><br>
            </div>
        </div>
        <div>
            <div id="pane-intro" style="display: none; grid-template-rows: 1fr 64px; height: calc(100% - 40px); margin: 20px;">
                <div style="display: flex; height: 100%; align-items: center; justify-content: center; text-align: center;">
                    <div>
                        <h1 style="font-family: 'Josefin Sans', sans-serif; font-weight: bold;">Bienvenue !</h1>
                        <p>Installez-vous confortablement, tout commence dans quelques instants.</p>
                    </div>
                </div>
                <div style="text-align: center; opacity: .5;">
                    <img src="https://equestria.horse/assets/brand/Wordmark/Coloured/WordmarkColoured.svg" style="height: 64px;"> | <img src="https://delta.equestria.dev/logo.svg" style="height: 64px;">
                </div>
            </div>

            <div id="pane-food" style="display: none; margin-top: 50px;">
                <img src="/menu.svg" style="width: 4em;"><span style="font-family: 'Josefin Sans', sans-serif; font-weight: bold; font-size: 1.75em; vertical-align: middle; margin-left: 10px;">La carte</span>

                <div class="list-group" style="margin-top: 1em;">
                    <?php $menu = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/menu.json"), true); foreach ($menu as $category => $items): ?>
                        <div class="list-group-item">
                            <b><?= $category ?></b>

                            <div style="border: 1px solid rgba(0, 0, 0, 0.125); border-radius: 0.375rem; margin: 0.5rem 0; display: grid; grid-template-columns: 1fr 1fr 1fr;">
                                <?php foreach ($items as $item): ?>
                                <div style="padding: 0.25rem 1rem;">
                                    <?= $item ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="pane-conference" style="display: none; position: fixed; z-index: 9999; inset: 0; background: black; align-items: center; justify-content: center;">
                <img style="width: 20%; pointer-events: none;" alt="Delta" src="/delta-full.svg">
            </div>

            <div id="pane-activities" style="display: none; margin-top: 50px;">
                <img src="/games.svg" style="width: 4em;"><span style="font-family: 'Josefin Sans', sans-serif; font-weight: bold; font-size: 1.75em; vertical-align: middle; margin-left: 10px;">Jeux proposés</span>

                <div class="list-group" style="margin-top: 1em;">
                    <?php $list = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/activities.json"), true)["list"]; foreach ($list as $game): if (trim($game) !== "-"): ?>
                        <div class="list-group-item">
                            <?= $game ?>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.users = {};
        window.config = {};

        async function refresh() {
            try {
                window.users = await (await window.fetch("/db.php?banned")).json();
                window.config = (await (await window.fetch("/display.json")).json());
                document.getElementById("error").style.display = "none";
            } catch (e) {
                document.getElementById("error").style.display = "";
                throw e;
            }
        }

        function refreshUI() {
            let k = 0;
            document.getElementById("score-table").innerHTML = Object.values(users).filter(i => i.arrival && i.activities !== null).sort((a, b) => calculateScore(b, true) - calculateScore(a, true)).map(i => {
                let text = `
                <div class="list-group-item ${k === 0 ? 'user-1' : (k === 1 ? 'user-2' : (k === 2 ? 'user-3' : ''))} ${i['banned'] ? 'user-banned' : ''}">
                    <span class="user-name">${i.name}</span>
                    <span style="float: right;">
                        ${i['alcohol'] === true || i['alcohol'] === null ? `<img src="/alcohol.svg" alt="Alcool" style="width: 24px;">` : ""}${i['activities'] === true || i['activities'] === null ? `<img src="/activities.svg" alt="Activités" style="width: 24px;">` : ""}${i['conference'] === true || i['conference'] === null ? `<img src="/delta.svg" alt="Delta" style="width: 24px;">` : ""}${k === 0 ? `<img src="/winner.svg" alt="Gagnant" style="width: 24px;">` : ""}${k === 1 || k === 2 ? `<img src="/medal.svg" alt="Podium" style="width: 24px;">` : ""}${i['banned'] ? `<img src="/banned.svg" alt="Exclus" style="width: 24px;">` : ""}<span style="vertical-align: middle; margin-left: 5px; display: inline-block;">${calculateScore(i)}</span>
                    </span>
                </div>
                `;

                if (!i['banned']) k++;

                return text;
            }).join("");
        }

        window.updateInterval = setInterval(async () => {
            await refresh();
            refreshUI();

            if (config.mode === "intro" || config.mode === "conference") {
                document.getElementById("panes").classList.add("single");
            } else {
                document.getElementById("panes").classList.remove("single");
            }

            switch (config.mode) {
                case "intro":
                    document.getElementById("pane-conference").style.display = "none";
                    document.getElementById("pane-food").style.display = "none";
                    document.getElementById("pane-intro").style.display = "grid";
                    document.getElementById("pane-activities").style.display = "none";
                    break;

                case "food":
                    document.getElementById("pane-conference").style.display = "none";
                    document.getElementById("pane-food").style.display = "block";
                    document.getElementById("pane-intro").style.display = "none";
                    document.getElementById("pane-activities").style.display = "none";
                    break;

                case "conference":
                    document.getElementById("pane-conference").style.display = "flex";
                    document.getElementById("pane-food").style.display = "none";
                    document.getElementById("pane-intro").style.display = "none";
                    document.getElementById("pane-activities").style.display = "none";
                    break;

                case "active":
                    document.getElementById("pane-activities").style.display = "block";
                    document.getElementById("pane-food").style.display = "none";
                    document.getElementById("pane-intro").style.display = "none";
                    document.getElementById("pane-conference").style.display = "none";
                    break;
            }
        }, 1000);
    </script>

    <style>
        #panes.single {
            grid-template-columns: 100% !important;
        }

        #panes.single > *:nth-child(1) {
            display: none;
        }

        .user-1, .user-2, .user-3 {
            font-weight: bold;
        }

        .user-1 {
            color: goldenrod;
        }

        .user-2 {
            color: #7c7c7c;
        }

        .user-3 {
            color: saddlebrown;
        }

        .user-banned * {
            opacity: .5;
        }

        .user-banned .user-name {
            color: rgba(0, 0, 0, .5);
        }

        .user-banned {
            color: black;
            font-weight: normal;
        }
    </style>
</body>
</html>