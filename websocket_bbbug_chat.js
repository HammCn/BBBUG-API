console.log("Ready now.\n**********************************");
var Config = {
    portSocket: 10011,//websocket端口 如果前端配置了https，你需要反代一下这个端口到443实现wss
    portHttp: 10012,//api端通过这个端口的http服务来获取在线用户信息
    secret: "wss_bbbug_com",
    apiUrl: "https://api.bbbug.com",//这里修改为你部署的API端地址
};
var websocket = require("nodejs-websocket"),
    crypto = require('crypto'),
    http = require('http'),
    https = require('https');
var webSocketServer = websocket.createServer(function (conn) {
    var query = login(conn.path);
    if (!query) {
        console.error("客户端登录失败");
        conn.close();
    } else {
        console.log("客户端连接成功 " + query.account);
        var url = Config.apiUrl + '/api/song/now?room_id=' + query.channel;
        https.get(url, function (res) {
            var dataString = "";
            res.on("data", function (data) {
                dataString += data;
            });
            res.on("end", function () {
                try {
                    conn.sendText(JSON.stringify({
                        type: JSON.parse(dataString).type,
                        time: 'now',
                        song: JSON.parse(dataString).song || null,
                        story: JSON.parse(dataString).story || null,
                        since: JSON.parse(dataString).since || 0,
                        count: JSON.parse(dataString).count || 0,
                        user: JSON.parse(dataString).user || null,
                        at: JSON.parse(dataString).at || false,
                    }));
                } catch (e) {
                }
            });
        });
        sendOnlineList(query.channel);
    }
    conn.on("close", function (code, reason) {
        console.error("客户端断开");
        sendOnlineList(query.channel);
    });
    conn.on("error", function (code, reason) {
        console.error("客户端断开");
        sendOnlineList(query.channel);
    });
    conn.on("text", function (msg) {
        if (msg == 'getNowSong') {
            var url = Config.apiUrl + '/api/song/now?room_id=' + query.channel;
            https.get(url, function (res) {
                var dataString = "";
                res.on("data", function (data) {
                    dataString += data;
                });
                res.on("end", function () {
                    try {
                        conn.sendText(JSON.stringify({
                            type: JSON.parse(dataString).type,
                            time: 'now',
                            song: JSON.parse(dataString).song || null,
                            story: JSON.parse(dataString).story || null,
                            since: JSON.parse(dataString).since || 0,
                            user: JSON.parse(dataString).user || null,
                            at: JSON.parse(dataString).at || false,
                        }));
                    } catch (e) {
                    }
                });
            });
        } else if (msg == 'bye') {
            console.error('用户主动断开链接');
            conn.close();
        }
    });
});
webSocketServer.listen(Config.portSocket);
console.log("服务启动成功(" + Config.portSocket.toString() + ")Websocket");
checkConnection();

function checkConnection() {
    console.log("当前在线连接数：(" + webSocketServer.connections.length + ")");
    setTimeout(function () {
        checkConnection();
    },
        5000);
}

function sendOnlineList(channel) {
    var url = Config.apiUrl + '/api/user/online?sync=yes&room_id=' + channel;
    https.get(url, function (res) {
        var dataString = "";
        res.on("data", function (data) {
            dataString += data;
        });
        res.on("end", function () {
            webSocketServer.connections.forEach(function (conn) {
                try {
                    var query = login(conn.path);
                    if (query.channel == channel) {
                        conn.sendText(JSON.stringify({
                            type: "online",
                            channel: channel,
                            data: JSON.parse(dataString).data
                        }));
                    }
                } catch (e) {
                }
            });
        });
    });
}

var http = require('http');
var url = require('url');
var querystring = require('querystring');
var httpServer = http.createServer(function (req, res) {
    if (req.method.toUpperCase() == 'POST') {
        res.writeHead(200, {
            'Content-Type': 'application/json;charset=utf-8'
        });
        var postData = '';
        req.on('data', function (chunk) {
            postData += chunk;
        });
        req.on('end', function () {
            postData = querystring.parse(postData);
            if (postData.token == sha1(Config.secret)) {
                switch (postData.type) {
                    case 'chat':
                        webSocketServer.connections.forEach(function (conn) {
                            var query = new QueryString(conn.path);
                            if (query.account == postData.to) {
                                conn.sendText(postData.msg);
                            }
                        });
                        break;
                    case 'channel':
                        webSocketServer.connections.forEach(function (conn) {
                            var query = new QueryString(conn.path);
                            if (query.channel == postData.to) {
                                conn.sendText(postData.msg);
                            }
                        });
                        break;
                    case 'system':
                        webSocketServer.connections.forEach(function (conn) {
                            conn.sendText(postData.msg);
                        });
                        break;
                    default:
                }
                res.end();
            } else {
                res.end("token error");
            }
        });
    } else if (req.method.toUpperCase() == 'GET') {
        res.writeHead(200, {
            'Content-Type': 'application/json;charset=utf-8'
        });
        var onlineList = [];
        var gets = new QueryString(req.url);
        webSocketServer.connections.forEach(function (conn) {
            var query = new QueryString(conn.path);
            if (gets.channel) {
                if (gets.channel == query.channel) {
                    onlineList.push(query.account);
                }
            } else {
                onlineList.push(query.account);
            }
        });
        res.end(JSON.stringify(onlineList));
    } else {
        res.writeHead(403, {
            'Content-Type': 'application/json;charset=utf-8'
        });
        res.end();
    }
});
httpServer.listen(Config.portHttp);
console.log("Web服务{" + Config.portHttp + "}启动成功!");


function getTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    return hours + ":" + minutes + ":" + seconds;
}

function debug(message) {
    console.log(getTime() + " : " + message);
}

function login(url) {
    var query = new QueryString(url);
    if (sha1("account" + query.account + "channel" + query.channel + 'salt' + query.channel) == query.ticket) {
        return query;
    } else {
        return false;
    }
}

function QueryString(url) {
    var name, value;
    url = url.replace("/?", "");
    var arr = url.split("&"); //各个参数放到数组里
    for (var i = 0; i < arr.length; i++) {
        num = arr[i].indexOf("=");
        if (num > 0) {
            name = arr[i].substring(0, num);
            value = arr[i].substr(num + 1);
            this[name] = value;
        }
    }
}

function getTimeStamp() {
    return Date.parse(new Date()) / 1000;
}

function sha1(str) {
    var sha1 = crypto.createHash("sha1"); //定义加密方式:md5不可逆,此处的md5可以换成任意hash加密的方法名称；
    sha1.update(str);
    var res = sha1.digest("hex"); //加密后的值d
    return res;
}