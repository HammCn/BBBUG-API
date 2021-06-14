var BBBUG = {
    sendTextMessage(message) {
        parent.window.postMessage({
            event: "sendTextMessage",
            message: message
        }, "*");
    },
    hideLrc() {
        parent.window.postMessage({
            event: "hideLrc"
        }, "*");
    },
    showLrc() {
        parent.window.postMessage({
            event: "showLrc"
        }, "*");
    },
    setVolume(volume) {
        parent.window.postMessage({
            event: "setVolume",
            volume: volume
        }, "*");
    },
    showUserInfo(userId) {
        parent.window.postMessage({
            event: "showUserInfo",
            userId: userId
        }, "*");
    },
    showPlayer() {
        parent.window.postMessage({
            event: "showPlayer"
        }, "*");
    },
    hidePlayer() {
        parent.window.postMessage({
            event: "hidePlayer"
        }, "*");
    },
    passTheSong() {
        parent.window.postMessage({
            event: "passTheSong"
        }, "*");
    },
    loveTheSong() {
        parent.window.postMessage({
            event: "loveTheSong"
        }, "*");
    },
    getNowSong() {
        parent.window.postMessage({
            event: "getNowSong"
        }, "*");
    },
    getLrcObj() {
        parent.window.postMessage({
            event: "getLrcObj"
        }, "*");
    },
    getThemeMode() {
        parent.window.postMessage({
            event: "getThemeMode"
        }, "*");
    }
};