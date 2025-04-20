function calculateScore(user, countBanned) {
    if (countBanned && user['banned']) return -1;

    return (
        (user['foods'].length * 2) +
        (user['drinks'].length) +
        (user['score'].reduce((a, b) => a + b))
    );
}

async function saveUser(user, id) {
    await window.fetch("/save.php?id=" + id, {
        method: "post",
        body: JSON.stringify(user)
    });

    await refresh();
    await refreshUI();
}

async function saveDisplay(config) {
    await window.fetch("/save.php?display", {
        method: "post",
        body: JSON.stringify(config)
    });

    await refresh();
    await refreshUI();
}