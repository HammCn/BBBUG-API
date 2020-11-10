var BBBUG = new Vue({
    el: '#app',
    data() {
        return {
            systemTips: {
                msg: false,
                type: 'info',
                timer: null,
            },
            emojiList: [],
            guestUserInfo: {
                myRoom: false,
                user_admin: false,
                user_head: "//cdn.bbbug.com/images/nohead.jpg",
                user_id: -1,
                user_name: "Ghost",
                access_token: "45af3cfe44942c956e026d5fd58f0feffbd3a237",
            },
            globalMusicSwitch: true,
            timeDiff: 0, //与服务器时间偏移
            apiUrl: "https://api.bbbug.com/api/",
            audioUrl: "",
            copyString: "",
            ChatPlaceHolder: "",
            userInfo: "",
            lrcString: "",
            ctrlEnabled: false,
            sexList: [{
                value: 0,
                title: '女生',
            }, {
                value: 1,
                title: '男生',
            }],
            player: {
                nextButton: false,
                voiceBar: false,
                volumeChangeTimer: null
            },
            lockScreenData: {
                ifLockSystem: false,
                musicHead: "",
                musicString: "",
                nowMusicLrcText: ""
            },
            musicLrcObj: {},
            volume: 50,
            loading: false,
            baseData: {
                access_token: '',
                plat: 'pc',
                version: 10000,
            },
            config: {
                notification: true,
                lockScreen: false,
                playMusic: true,
            },
            websocket: {
                connection: null,
                heartBeatTimer: null,
                connectTimer: null,
                hardStop: false,
                isConnected: false,
            },
            title: {
                my_room: "我的房间",
                create_room: "创建房间",
                change_account: "退出登录",
                my_setting: "设置",
                invate_person: "邀请",
                my_profile: "我的",
                ios_app: "手机版",
                exit_room: "换房",
                rank_list: '排行榜',
                login: "登录"
            },
            chat_room: {
                timerForWebTitle: false,
                historyLoading: false,
                historyMax: 50,
                songSendUser: false,
                message: "",
                at: null,
                song: null,
                voice: false,
                songPercent: 0,
                list: [],
                isVideoFullScreen: false,
                dialog: {
                    showRankList: false,
                    editMyProfile: false,
                    editMyRoom: false,
                    searchImageBox: false,
                    searchSongBox: false,
                    pickedSongBox: false,
                    showOnlineBox: false,
                    showUserProfile: false,
                    mySongBox: false,
                    searchVoiceBox: false,
                    showRankList: false,

                },
                loading: {
                    showRankList: true,
                    searchImageBox: false,
                    searchSongBox: true,
                    pickedSongBox: true,
                    mySongBox: true,
                    searchVoiceBox: true,
                },
                data: {
                    isLoadingVoiceBox: false,
                    isLoadingMySongBox: false,
                    rankType: '',
                    showRankList: [],
                    searchImageList: [],
                    searchSongList: [],
                    pickedSongList: [],
                    mySongList: [],
                    mySongListPage: 1,
                    voiceBoxPage: 1,
                    searchVoiceList: [],
                    onlineList: [],
                    hisUserInfo: {},
                    room_addsong: [{
                        value: 0,
                        title: "所有人可点歌"
                    }, {
                        value: 1,
                        title: "仅房主可点歌"
                    }],
                    room_sendmsg: [{
                        value: 0,
                        title: "关闭全员禁言"
                    }, {
                        value: 1,
                        title: "开启全员禁言"
                    }],
                    room_public: [{
                        value: 0,
                        title: "公开房间"
                    }, {
                        value: 1,
                        title: "加密房间"
                    }],
                    room_robot: [{
                        value: 0,
                        title: "开启机器人点歌"
                    }, {
                        value: 1,
                        title: "关闭机器人点歌"
                    }],
                    room_playone: [{
                        value: 0,
                        title: "随机播放"
                    }, {
                        value: 1,
                        title: "单曲循环"
                    }],
                    room_votepass: [{
                        value: 0,
                        title: "关闭投票切歌"
                    }, {
                        value: 1,
                        title: "打开投票切歌"
                    }],
                    room_votepercent: [{
                        value: 20,
                        title: "20%"
                    }, {
                        value: 30,
                        title: "30%"
                    }, {
                        value: 40,
                        title: "40%"
                    }, {
                        value: 50,
                        title: "50%"
                    }, {
                        value: 60,
                        title: "60%"
                    },],
                },
                form: {
                    editMyProfile: {
                        user_name: "",
                        user_head: "",
                        user_remark: "",
                        user_touchtip: "",
                        user_sex: 0,
                        user_password: ""
                    },
                    editMyRoom: {
                        room_name: "",
                        room_notice: "",
                        room_type: 0,
                        room_password: "",
                        room_addsong: 0,
                        room_sendmsg: 0,
                        room_robot: 0,
                        room_public: 0,
                        room_playone: 0,
                        room_domain: "",
                        room_domain_edit: false,
                        room_huya: "",
                        room_votepass: 1,
                        room_votepercent: 30,
                    },
                    searchImageBox: {
                        keyword: ""
                    },
                    searchSongBox: {
                        keyword: ""
                    },
                    searchVoiceBox: {
                        keyword: "郭德纲相声",
                        page: 1
                    },
                    pickSong: null
                },
            },
            room: {
                search_id: "",
                room_id: globalData.room_id,
                roomInfo: false,
                list: [],
                showDialog: false,
            },
            room_create: {
                cancelSearchImage: false,
                typeList: [{
                    value: 0,
                    title: "文字聊天房"
                }, {
                    value: 1,
                    title: "点歌音乐房"
                }, {
                    value: 2,
                    title: "猜歌游戏房"
                }, {
                    value: 3,
                    title: "有声故事房"
                }, {
                    value: 4,
                    title: "房主电台房"
                }, {
                    value: 5,
                    title: "虎牙直播房"
                }],
                form: {
                    room_name: "",
                    room_password: "",
                    room_public: 0,
                    room_type: 1,
                    room_notice: '',
                }
            },
            login: {
                validEmail: false,
                form: {
                    user_account: "",
                    user_password: ""
                },
                showPage: false
            },
            isAudioCurrentTimeChanged: false,
            iosCanPlay: false,
            player_body: {
                top: 'auto',
                left: 'auto',
                startX: 0,
                startY: 0,
                startLeft: 10,
                startTop: 70,
                isMoving: false
            },
            showScrollToBottomBtn: false,
        }
    },
    created() {
        let that = this;
    },
    mounted() {
        let that = this;
        // 重写notify，设置默认偏移
        const notifyFunc = this.$notify;
        that.$notify = function (obj) {
            notifyFunc(
                Object.assign({ offset: 70 }, obj)
            )
        }
        document.addEventListener('paste', that.getClipboardFiles);
        that.emojiList = [];
        for (let i = 1; i <= 30; i++) {
            that.emojiList.push('https://cdn.bbbug.com/images/emoji/' + i + '.png');
        }
        that.chat_room.data.searchImageList = that.emojiList;

        that.globalMusicSwitch = localStorage.getItem('globalMusicSwitch') == "off" ? false : true;

        that.ctrlEnabled = localStorage.getItem('ctrlEnable') == 'ctrl_enter' ? true : false;
        that.login.form.user_account = localStorage.getItem('user_account') || '';
        that.baseData.access_token = localStorage.getItem('access_token') || '';

        if (that.room.room_id == 888) {
            room_id = localStorage.getItem('room_id');
            if (room_id) {
                that.room.room_id = parseInt(room_id);
            }
        }
        that.volume = localStorage.getItem('volume') == null ? 50 : parseInt(localStorage.getItem('volume'));
        if (that.volume == 0) {
            that.config.playMusic = false;
        } else {
            that.config.playMusic = true;
        }

        that.$alert(globalData.room_notice, 'Welcome', {
            confirmButtonText: '确定',
            callback: function () {
                that.initAudioControllers();
                if (that.baseData.access_token) {
                    that.getMyInfo(function (result) {
                        if (result) {
                            that.doJoinRoomById(that.room.room_id);
                        } else {
                            that.doLogout();
                            that.doJoinRoomById(that.room.room_id);
                        }
                    });
                } else {
                    that.doLogout();
                    that.doJoinRoomById(that.room.room_id);
                }
                that.callParentFunction('noticeClicked', 'success');
            }
        });
        that.request({
            url: "system/time",
            success(res) {
                let serverTime = res.data.time;
                that.timeDiff = parseInt(new Date().valueOf()) - serverTime;
                console.log("timeDiff is : " + that.timeDiff + "ms");
            },
        });
        window.onkeydown = function (e) {
            switch (e.keyCode) {
                case 27:
                    if ((that.room.roomInfo.room_type == 1 || that.room.roomInfo.room_type == 4) && that.chat_room.song) {
                        that.lockScreenData.ifLockSystem = !that.lockScreenData.ifLockSystem;
                        if (that.lockScreenData.ifLockSystem) {
                            document.title = '音乐播放器';
                        } else {
                            document.title = that.room.roomInfo.room_name;
                        }
                    }
                    e.preventDefault();
                    break;
                default:
            }
        };
    },
    updated() {
        let that = this;
        that.$previewRefresh();
    },
    methods: {
        doMessageKeyDown(e) {
            let that = this;
            if ((e.metaKey || e.altKey || e.ctrlKey)) {
                if (e.which >= 49 && e.which <= 57 && that.chat_room.data.searchSongList.length > 0 && that.chat_room.dialog.searchSongBox && e.which - 48 <= that.chat_room.data.searchSongList.length) {
                    that.doAddSong(that.chat_room.data.searchSongList[e.which - 49]);
                    e.preventDefault();
                }
            }
        },
        getHuyaId(str) {
            return str.replace('https://www.huya.com/', '');
        },
        showVipTips(){
            this.$alert(`
                <b>认证条件</b><br>BBBUG聊天室代码贡献与开发者、第三方合作网站站长、第三方推广与广告合作方、服务器赞助商等。<br><br>
                <b>认证方式</b><br>联系ID:10000 或邮件 admin@hamm.cn 发送即可；开发者可加入QQ群：1140258698
            `, '', {
                dangerouslyUseHTMLString: true,
                showClose:false,
                callback: function () {
                }
            });
        },
        doGetRankList() {
            var that = this;
            that.chat_room.loading.showRankList = true;
            let rankType = 'songrecv';
            switch (that.chat_room.data.rankType) {
                case '发言':
                    rankType = 'chat';
                    break;
                case '斗图':
                    rankType = 'img';
                    break;
                case '点歌':
                    rankType = 'song';
                    break;
                case '切歌':
                    rankType = 'pass';
                    break;
                case '顶歌':
                    rankType = 'push';
                    break;
                case '天狗':
                    rankType = 'songsend';
                    break;
                case '人气':
                    rankType = 'songrecv';
                    break;
                default:
            }

            that.request({
                url: "user/getRankList",
                data: {
                    type: rankType
                },
                success: function (res) {
                    that.$refs.showRankList.scrollTop = 0;
                    that.chat_room.data.showRankList = res.data;
                    that.chat_room.loading.showRankList = false;
                }
            });
        },
        doShowRankDialog() {
            this.chat_room.data.rankType = '人气';
            this.chat_room.dialog.showRankList = true;
            this.doGetRankList();
        },
        doContextMenu(e) {
            return false;
        },
        doShowQrcode() {
            this.$alert('<center><span class="item" style="color:red;font-size:14px;"><font color=black style="font-size:20px;">手机扫码立即穿梭</font><br><br><img width="200px" src="https://qr.hamm.cn?data=' + encodeURIComponent('https://bbbug.com/third?access_token=' + this.baseData.access_token) + '"/><br>请不要截图发给其他人,避免账号被盗</span></center>', {
                dangerouslyUseHTMLString: true
            });
        },
        isIos() {
            return !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);;
        },
        doCreateRoom() {
            let that = this;
            that.room.showDialog = false;
            that.room_create.showPage = true;
        },
        request(_data = {}) {
            let that = this;
            _data.loading && (that.loading = true);
            axios.post(that.apiUrl + (_data.url || ""), that.getPostData(_data.data || {}))
                .then(function (response) {
                    _data.loading && (that.loading = false);
                    switch (response.data.code) {
                        case 200:
                            if (_data.success) {
                                _data.success(response.data);
                            } else {
                                that.$message.success(response.data.msg);
                            }
                            break;
                        case 401:
                            that.callParentFunction('needLogin', 'please login first!');
                            if (_data.login) {
                                _data.login();
                            } else {
                                that.$confirm(response.data.msg, '无权访问', {
                                    confirmButtonText: '登录',
                                    cancelButtonText: '取消',
                                    closeOnClickModal: false,
                                    closeOnPressEscape: false,
                                    type: 'warning'
                                }).then(function () {
                                    that.doShowLoginBox();
                                }).catch(function () {
                                    that.doLogout();
                                    that.doJoinRoomById(that.room.room_id);
                                });
                            }
                            break;
                        default:
                            if (_data.error) {
                                _data.error(response.data);
                            } else {
                                that.$message.error(response.data.msg);
                            }
                    }
                })
                .catch(function (error) {
                    _data.loading && (that.loading = false);
                    console.log(error)
                });
        },
        openWebUrl(url) {
            window.open(url);
        },
        formatProgress(percentage) {
            return '';
        },
        doShowVoiceBar() {
            let that = this;
            that.player.voiceBar = true;
            clearTimeout(that.player.volumeChangeTimer);
            that.player.volumeChangeTimer = setTimeout(function () {
                that.player.voiceBar = false;
            }, 3000);
        },
        doVolumeChanged() {
            let that = this;
            that.volume = parseInt(that.volume);
            if (that.volume == 0) {
                that.config.playMusic = false;
            } else {
                that.config.playMusic = true;
            }
            that.$refs.audio.volume = parseFloat(that.volume / 100);
            localStorage.setItem('volume', that.volume);
            localStorage.setItem('volume_old', that.volume);
            clearTimeout(that.player.volumeChangeTimer);
            that.player.volumeChangeTimer = setTimeout(function () {
                that.player.voiceBar = false;
            }, 3000);
        },
        onCopySuccess() {
            this.$message.success('复制成功,快发给好友来一起嗨皮吧');
        },
        getRoomIdFromUrl() { //获取url里面的id参数
            var arr = window.location.href.split('#');
            if (arr.length == 2) {
                return arr[1];
            } else {
                return false;
            }
        },
        handleSendButtonCommand(cmd) {
            if (cmd == 'enter') {
                this.ctrlEnabled = false;
            } else {
                this.ctrlEnabled = true;
            }
            localStorage.setItem('ctrlEnable', cmd);
        },
        doReply(item) {
            let that = this;
            that.chat_room.at = {
                user_id: item.user.user_id,
                user_name: item.user.user_name,
                message: item
            };
            this.focusInput();
        },
        focusInput() {
            const textarea = document.querySelector(".chat_room_message");
            // 艾特后自动聚焦
            textarea.focus();
        },
        commandUserHead(cmd) {
            let that = this;
            switch (cmd.command) {
                case 'at':
                    that.chat_room.at = cmd.row;
                    that.chat_room.dialog.showOnlineBox = false;
                    this.focusInput();
                    break;
                case 'pullback':
                    that.request({
                        url: "message/back",
                        data: {
                            message_id: cmd.row.message_id,
                            room_id: that.room.room_id
                        }
                    });
                    break;
                case 'shutdown':
                    that.request({
                        url: "user/shutdown",
                        data: {
                            user_id: cmd.row.user_id,
                            room_id: that.room.room_id
                        }
                    });
                    break;
                case 'songdown':
                    that.request({
                        url: "user/songdown",
                        data: {
                            user_id: cmd.row.user_id,
                            room_id: that.room.room_id
                        }
                    });
                    break;
                case 'removeBan':
                    that.request({
                        url: "user/removeban",
                        data: {
                            user_id: cmd.row.user_id,
                            room_id: that.room.room_id
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.doShowOnlineList();
                        }
                    });
                    break;
                case 'profile':
                    that.doGetUserInfoById(cmd.row.user_id);
                    that.chat_room.dialog.showUserProfile = true;
                    that.chat_room.dialog.showOnlineBox = false;
                    break;
                case 'sendSong':
                    that.chat_room.dialog.showOnlineBox = false;
                    that.doSendSongToUser(cmd.row);
                    that.doSearchSong();
                    break;
                default:
                    that.$message.error('即将上线，敬请期待');
            }
        },
        doSendSongToUser(user) {
            let that = this;
            that.chat_room.songSendUser = user;
            that.hideAllDialog();
            that.chat_room.dialog.searchSongBox = true;
        },
        beforeHandleUserCommand(row, command) {
            return {
                "row": row,
                "command": command
            }
        },
        replaceProfileLink(appUrl, userExtra) {
            return appUrl.replace('#extra#', userExtra);
        },

        getMusicLrc() {
            let that = this;
            that.musicLrcObj = {};
            if (that.room.roomInfo.room_type == 2) {
                return;
            }
            that.request({
                url: 'song/getLrc',
                data: {
                    mid: that.chat_room.song.song.mid
                },
                success(res) {
                    that.musicLrcObj = (res.data);
                    // that.musicLrcObj = that.createLrcObj(res.data);
                }
            });
        },
        doGameMusicPass() {
            let that = this;
            that.musicLrcObj = {};
            that.request({
                url: '/song/gamePass',
                data: {
                    room_id: that.room.room_id,
                }
            });
        },
        doGetRoomData() {
            let that = this;
            that.loading = true;
            if (that.websocket.isConnected) {
                that.websocket.hardStop = true;
                that.websocket.connection.send('bye');
                that.chat_room.song = false;
                that.chat_room.voice = false;
                that.audioUrl = '';
            }
            if (that.websocket.isConnected) {
                setTimeout(function () {
                    that.doGetRoomData();
                }, 1000);
                return;
            }
            that.loading = false;
            that.initNowRoomInfo(function (result) {
                if (result) {
                    localStorage.setItem('room_id', that.room.room_id);
                    that.initWebsocket();
                    that.loadMessageHistory();
                    if (that.userInfo.user_needmotify && that.userInfo.user_app == 1) {
                        that.$confirm('完善资料并修改密码,下次就可以直接用密码登录啦!', '资料待完善', {
                            confirmButtonText: '去完善',
                            cancelButtonText: '取消',
                            closeOnClickModal: false,
                            closeOnPressEscape: false,
                            type: 'warning'
                        }).then(function () {
                            that.doEditMyProfile();
                            that.callParentFunction('noticeClicked', 'click_motify_info');
                        }).catch(function () {
                            that.callParentFunction('noticeClicked', 'click_cancel');
                        });
                    }
                }
            });
        },
        initAudioControllers() {
            let that = this;
            try {
                if (that.isIos()) {
                    that.$refs.audio.play();
                    that.$refs.audio.pause();
                } else {
                    that.$refs.audio.play();
                }
            } catch (error) {
                console.log(error);
            }
            that.loading = false;
        },
        callParentFunction(type, msg) {
            //触发父容器方法
            if (self != top) {
                window.parent.postMessage({
                    'type': type,
                    'msg': msg
                }, '*');
            }
        },
        getPostData(data) {
            return Object.assign({}, this.baseData, data);
        },
        audioLoaded() {
            let that = this;
            that.$refs.audio.play();
        },
        audioEnded() {
            let that = this;
            that.audioUrl = "";
            that.chat_room.song = null;
            that.lrcString = '歌词加载中...';
            that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\n" + location.href + that.room.room_id;
            if (that.room.roomInfo.room_domain && that.room.roomInfo.room_domainstatus) {
                if (location.href.indexOf('bbbug.com') < 0) {
                    //使用的独立域名
                    that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\n" + location.href;
                } else {
                    that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\nhttps://" + that.room.roomInfo.room_domain + ".bbbug.com";
                }
            }
            that.chat_room.songPercent = 0;
            that.$refs.audio.currentTime = 0;
        },
        audioPlaying() {
            let that = this;
            that.nowPlaying = true;
            that.lrcString = '歌词加载中...';
            that.$refs.audio.volume = parseFloat(that.volume / 100);

            // if (!that.isAudioCurrentTimeChanged) {
            //     let nowTimeStamps = parseInt((new Date().valueOf() - that.timeDiff) / 1000);
            //     let now = 0;
            //     switch (that.room.roomInfo.room_type) {
            //         case 1:
            //         case 2:
            //         case 4:
            //             now = parseFloat((nowTimeStamps - that.chat_room.song.since)).toFixed(2);
            //             if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
            //                 now = 0;
            //             }
            //             console.error('当前应播放' + now + 's');
            //             that.$refs.audio.currentTime = now < 0 ? 0 : now;
            //             break;
            //         case 3:
            //             now = parseFloat((nowTimeStamps - that.chat_room.voice.since)).toFixed(2);
            //             if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
            //                 now = 0;
            //             }
            //             console.error('当前应播放' + now + 's');
            //             that.$refs.audio.currentTime = now < 0 ? 0 : now;
            //             break;
            //     }
            // }
            if (that.chat_room.song) {
                if (that.$refs.audio.duration > 0) {
                    let percent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 100);
                    if (percent > 100) {
                        percent = 100;
                    }
                    if (percent < 0) {
                        percent = 0;
                    }
                    that.chat_room.songPercent = percent;
                }
            }
        },
        audioCanPlay() {
            let that = this;
            if (!that.isAudioCurrentTimeChanged) {
                let nowTimeStamps = parseInt((new Date().valueOf() - that.timeDiff) / 1000);
                let now = 0;
                switch (that.room.roomInfo.room_type) {
                    case 1:
                    case 2:
                    case 4:
                        now = parseFloat((nowTimeStamps - that.chat_room.song.since)).toFixed(2);
                        if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                            now = 0;
                        }
                        // console.error('当前应播放' + now + 's');
                        that.$refs.audio.currentTime = now < 0 ? 0 : now;
                        break;
                    case 3:
                        now = parseFloat((nowTimeStamps - that.chat_room.voice.since)).toFixed(2);
                        if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                            now = 0;
                        }
                        console.error('当前应播放' + now + 's');
                        that.$refs.audio.currentTime = now < 0 ? 0 : now;
                        break;
                }
            }
            that.isAudioCurrentTimeChanged = true;
            if (that.isIos() && !that.iosCanPlay) {
                that.$alert('播放器加载成功!', '加载成功', {
                    confirmButtonText: '确定',
                    callback: function () {
                        that.$refs.audio.play();
                        that.iosCanPlay = true;
                    }
                });
            } else {
                that.$refs.audio.play();
            }
        },
        audioError(e) {
            let that = this;
            if (that.audioUrl) {
                that.$refs.audio.src = "https://cdn.bbbug.com/music/"+that.chat_room.song.song.mid+".mp3";
                that.$refs.audio.load();
                that.$refs.audio.play();
            }
            // console.log(e);
            // that.$alert('很罕见的音频地址读取失败!', '读取失败', {
            //     confirmButtonText: '重试',
            //     callback: function () {
            //         that.doPlayMusic(that.chat_room.song);
            //     }
            // });
        },
        audioTimeUpdate() {
            let that = this;
            if (that.room.roomInfo.room_type == 2) {
                that.lrcString = '猜歌游戏进行中，请在上面输入歌曲名字即可参与游戏啦~';
                return;
            }
            if (that.$refs.audio.duration > 0 && that.$refs.audio.duration != NaN) {
                that.chat_room.songPercent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 100);
                let lrcText = '';
                if (that.room.roomInfo.room_type != 1 && that.room.roomInfo.room_type != 2 && that.room.roomInfo.room_type != 4) {
                    that.lrcString = '';
                    return false;
                }
                if (that.musicLrcObj) {
                    for (let i = 0; i < that.musicLrcObj.length; i++) {
                        if (i == that.musicLrcObj.length - 1) {
                            lrcText = (that.musicLrcObj[i].lineLyric);
                        } else {
                            if (that.$refs.audio.currentTime > that.musicLrcObj[i].time && that.$refs.audio.currentTime < that.musicLrcObj[i + 1].time) {
                                lrcText = (that.musicLrcObj[i].lineLyric);
                                break;
                            }
                        }
                    }
                    if (lrcText) {
                        that.lrcString = lrcText;
                        that.lockScreenData.nowMusicLrcText = lrcText;
                        return;
                    }
                }
                that.lrcString = '没有读取到歌词';
            }
        },
        loadMessageHistory() {
            let that = this;
            if (that.chat_room.historyLoading) {
                return;
            }
            that.chat_room.historyLoading = true;
            that.request({
                url: "message/getMessageList",
                data: {
                    room_id: that.room.room_id,
                    per_page: that.chat_room.historyMax,
                },
                success(res) {
                    that.chat_room.historyLoading = false;
                    that.chat_room.list = [];
                    for (let i = 0; i < res.data.length; i++) {
                        let _obj = false;
                        try {
                            _obj = JSON.parse(decodeURIComponent(res.data[i].message_content));
                        } catch (error) { }
                        if (_obj) {
                            if (_obj.at) {
                                _obj.content = '@' + _obj.at.user_name + " " + _obj.content;
                            }
                            _obj.time = res.data[i].message_createtime;
                            that.chat_room.list.unshift(_obj);
                        }
                    }
                    that.addSystemMessage(that.room.roomInfo.room_notice ? that.room.roomInfo.room_notice : ('欢迎来到' + that.room.roomInfo.room_name + '!'));
                    that.autoScroll();
                }
            });
        },
        doGetUserInfoById(user_id) {
            let that = this;
            that.request({
                url: "user/getUserInfo",
                data: {
                    user_id: user_id
                },
                success(res) {
                    that.chat_room.data.hisUserInfo = res.data;
                    that.chat_room.dialog.showUserProfile = true;
                }
            });
        },
        checkRoomPassword(room_id, room_password, callback = false) {
            let that = this;
            that.request({
                url: "room/getRoomInfo",
                data: {
                    room_id: room_id,
                    room_password: room_password
                },
                success(res) {
                    if (callback) {
                        callback(true);
                    }
                },
                error(res) {
                    if (callback) {
                        callback(false, res.msg);
                    }
                }
            });
        },
        getMyInfo(callback = null) {
            let that = this;
            that.request({
                url: "user/getMyInfo",
                loading: true,
                success(res) {
                    that.userInfo = res.data;
                    that.chat_room.data.hisUserInfo = res.data;
                    if (callback) {
                        callback(res);
                    }
                },
                login() {
                    if (callback) {
                        callback(false);
                    }
                },
                error() {
                    if (callback) {
                        callback(false);
                    }
                }
            });
        },
        addSystemMessage(msg, color = '#999', bgColor = '#eee') {
            let that = this;
            if (that.chat_room.list.length > that.chat_room.historyMax) {
                that.chat_room.list.shift();
            }
            that.chat_room.list.push({
                type: "system",
                content: msg,
                bgColor: bgColor,
                color: color
            });
            that.autoScroll();
        },
        addSystemTips(msg, type = 'info') {
            let that = this;
            if (that.systemTips.msg) {
                clearTimeout(that.systemTips.timer);
            }
            that.systemTips = {
                msg: msg,
                type: type
            };
            that.systemTips.timer = setTimeout(function () {
                that.systemTips = {
                    msg: false,
                    type: type
                };
            }, 3000);
        },
        autoScroll() {
            let that = this;
            that.$nextTick(function () {
                if (!that.config.lockScreen) {
                    let ele = document.getElementById('chat_room_history');
                    ele.scrollTop = ele.scrollHeight;
                }
            });
        },
        hideAllDialog() {
            let that = this;
            that.chat_room.dialog.editMyProfile = false;
            that.chat_room.dialog.searchImageBox = false;
            that.chat_room.dialog.searchSongBox = false;
            that.chat_room.dialog.pickedSongBox = false;
            that.chat_room.dialog.searchVoiceBox = false;
            that.chat_room.dialog.mySongBox = false;
        },
        initNowRoomInfo(callback = false) {
            let that = this;
            that.request({
                url: "room/getRoomInfo",
                data: {
                    room_id: that.room.room_id
                },
                success(res) {
                    if (res.data.room_type != that.room.roomInfo.room_type) {
                        that.audioUrl = '';
                        that.chat_room.song = null;
                    }
                    that.room.roomInfo = res.data;
                    that.addSystemMessage(res.data.room_notice ? res.data.room_notice : ('欢迎来到' + res.data.room_name + '!'));
                    that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\n" + location.href + that.room.room_id;
                    if (that.room.roomInfo.room_domain && that.room.roomInfo.room_domainstatus) {
                        if (location.href.indexOf('bbbug.com') < 0) {
                            //使用的独立域名
                            that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\n" + location.href;
                        } else {
                            that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\nhttps://" + that.room.roomInfo.room_domain + ".bbbug.com";
                        }
                    }
                    document.title = res.data.room_name;
                    that.doShowOnlineList();
                    if (that.websocket.connection) {
                        that.websocket.connection.send('getNowSong');
                    }
                    if (that.room.roomInfo.room_sendmsg == 1 && that.room.roomInfo.room_user != that.userInfo.user_id && !that.userInfo.user_admin) {
                        that.ChatPlaceHolder = '全员禁言中,你暂时无法发言';
                    } else {
                        that.ChatPlaceHolder = placeholder;
                    }
                    if (callback) {
                        callback(true);
                    }
                },
                error(res) {
                    that.$message.error(res.msg);
                    switch (res.code) {
                        case 301:
                            that.websocket.hardStop = true;
                            that.websocket.connection.send('bye');
                            that.$alert(res.msg, '房间封禁', {
                                confirmButtonText: '确定',
                                callback: function () {
                                    that.doJoinRoomById(888);
                                }
                            });
                            break;
                        case 302:
                            that.websocket.hardStop = true;
                            that.websocket.connection.send('bye');
                            that.$alert(res.msg, '房间密码变更', {
                                confirmButtonText: '确定',
                                callback: function () {
                                    that.doJoinRoomById(888);
                                }
                            });
                            break;
                        default:
                            that.$alert(res.msg, '进入失败', {
                                confirmButtonText: '确定',
                                callback: function () {
                                    that.doJoinRoomById(888);
                                }
                            });
                            callback(false);
                    }
                    if (callback) {
                        callback(false);
                    }
                }
            });
        },
        doChangeTo(room) {
            let that = this;
            that.$confirm('你点击了一张快捷机票，是否确认进入 ' + room.room_name + ' ?', 'ID: ' + room.room_id, {
                confirmButtonText: '进入',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                that.doJoinRoomById(room.room_id);
            }).catch(function () { });

        },
        initWebsocket() {
            let that = this;
            that.request({
                url: "room/getWebsocketUrl",
                data: {
                    channel: that.room.room_id
                },
                success(res) {
                    that.websocket.params = res.data;
                    that.websocket.connection = new WebSocket("wss://websocket.bbbug.com/?account=" + res.data.account + "&channel=" + res.data.channel + "&ticket=" + res.data.ticket);
                    that.websocket.connection.onopen = function (evt) {
                        that.websocket.isConnected = true;
                        that.websocket.hardStop = false;
                        that.doWebsocketHeartBeat();
                        // that.chat_room.list.push({
                        //     desc: "嘤嘤嘤,快给我们的项目点个Star好不好呀,有兴趣的话也欢迎来一起贡献代码呀~",
                        //     img: "",
                        //     key: "edc937ec77f8a6e786c1e1c5c9288f21c0316db5883754",
                        //     link: "https://gitee.com/bbbug_com",
                        //     sha: "cbe2db8e9a6bad851cb4b94540a583c3bbf5ab78",
                        //     title: "BBBUG项目开发团队开源地址",
                        //     type: "link",
                        //     user: {
                        //         app_id: 1,
                        //         app_name: "BBBUG",
                        //         app_url: "https://bbbug.com",
                        //         user_admin: true,
                        //         user_head: "https://api.bbbug.com/uploads/thumb/image/20200828/7e9ac63489f863a2e690fdb74931565b.jpg",
                        //         user_id: 1,
                        //         user_sex: 0,
                        //         user_name: "BBBUG机器人",
                        //     }
                        // });
                        // that.chat_room.list.push({
                        //     key: "edc937ec77f8a6e786c1e1c5c9288f21c0316db5883754",
                        //     sha: "cbe2db8e9a6bad851cb4b94540a583c3bbf5ab78",
                        //     content: "Hello World!",
                        //     type: "text",
                        //     user: {
                        //         app_id: 1,
                        //         app_name: "BBBUG",
                        //         app_url: "https://bbbug.com",
                        //         user_admin: true,
                        //         user_head: "https://api.bbbug.com/uploads/thumb/image/20200828/7e9ac63489f863a2e690fdb74931565b.jpg",
                        //         user_id: 1,
                        //         user_name: "机器人",
                        //         user_sex: 0,
                        //         user_remark: "别@我,我只是个测试帐号",
                        //     }
                        // });
                    };
                    that.websocket.connection.onmessage = function (event) {
                        that.messageController(event.data);
                    };
                    that.websocket.connection.onclose = function (event) {
                        console.log(event);
                        that.websocket.isConnected = false;
                        if (!that.websocket.hardStop) {
                            that.doWebsocketError();
                        }
                    };
                }
            });
        },
        scrollEvent(e) {
            let that = this;
            if (e.currentTarget.scrollTop + e.currentTarget.clientHeight + 200 >= e.currentTarget.scrollHeight) {
                that.config.lockScreen = false;
            } else {
                that.config.lockScreen = true;
            }
            // 聊天记录区域滚动相关处理
            const func = scrollFuncs[e.target.id];
            func && func(e, this);
        },
        scrollToBottom() {
            scrollFuncs.scrollToBottom();
        },
        friendlyTime: function (time) {
            var now = parseInt(Date.parse(new Date()) / 1000);
            if (now - time <= 60) {
                return '刚刚';
            } else if (now - time > 60 && now - time <= 3600) {
                return parseInt((now - time) / 60) + '分钟前'
            } else if (now - time > 3600 && now - time <= 86400) {
                return parseInt((now - time) / 3600) + '小时前'
            } else if (now - time > 86400 && now - time <= 86400 * 7) {
                return parseInt((now - time) / 86400) + '天前'
            } else if (now - time > 86400 * 7 && now - time <= 86400 * 30) {
                return parseInt((now - time) / 86400 / 7) + '周前'
            } else if (now - time > 86400 * 30 && now - time <= 86400 * 30 * 12) {
                return parseInt((now - time) / 86400 / 30) + '月前'
            } else {
                return parseInt((now - time) / 86400 / 365) + '年前'
            }
        },
        scrollToChat: function (msgid) {
            scrollFuncs.scrollToChat(msgid);
        },
        messageController(data) {
            let that = this;
            try {
                let obj = {};
                try {
                    obj = JSON.parse(decodeURIComponent(data));
                } catch (e) {
                    obj = JSON.parse(data);
                }
                if (that.chat_room.list.length > that.chat_room.historyMax) {
                    that.chat_room.list.shift();
                }
                obj.time = parseInt(new Date().valueOf() / 1000);
                switch (obj.type) {
                    case 'touch':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 摸了摸 " + that.urldecode(obj.at.user_name) + obj.at.user_touchtip, '#999', '#eee');
                        if (obj.at) {
                            if (obj.at.user_id == that.userInfo.user_id) {
                                if (that.config.notification) {
                                    let isNotificated = false;
                                    if (window.Notification && Notification.permission !== "denied") {
                                        Notification.requestPermission(function (status) { // 请求权限
                                            if (status === 'granted') {
                                                // 弹出一个通知
                                                var n = new Notification("摸一摸", {
                                                    body: that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip),
                                                    icon: ""
                                                });
                                                isNotificated = true;
                                                // 两秒后关闭通知
                                                setTimeout(function () {
                                                    n.close();
                                                }, 5000);
                                            }
                                        });
                                    }
                                    if (!isNotificated) {
                                        that.$notify({
                                            title: "摸一摸",
                                            message: that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip),
                                            duration: 10000,
                                            dangerouslyUseHTMLString: true
                                            // offset: 70,
                                        });

                                    }
                                }
                            }
                        } else {
                            if (that.chat_room.isVideoFullScreen) {
                                that.$notify({
                                    title: that.urldecode(obj.user.user_name) + "说：",
                                    message: that.urldecode(obj.content),
                                    duration: 5000,
                                    // offset: 70,
                                });
                            }
                        }
                        break;
                    case 'clear':
                        that.chat_room.list = [];
                        that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                        break;
                    case 'text':
                        if (obj.user.user_id == that.userInfo.user_id) {
                            for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                                if (that.chat_room.list[i].sha == 'loading') {
                                    that.chat_room.list.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        if (obj.user.user_id == 10000) {
                            if (obj.content == 'clear') {
                                that.chat_room.list = [];
                                that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                                return;
                            }
                            if (obj.content == 'reload') {
                                that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "刷新了你的页面", '#f00', '#eee');

                                location.replace(location.href);
                                return;
                            }
                        }
                        if (obj.at) {
                            if (obj.at.user_id == that.userInfo.user_id) {
                                if (that.config.notification) {
                                    let isNotificated = false;
                                    if (window.Notification && Notification.permission !== "denied") {
                                        Notification.requestPermission(function (status) { // 请求权限
                                            if (status === 'granted') {
                                                // 弹出一个通知
                                                var n = new Notification(that.urldecode(obj.user.user_name) + "@了你：", {
                                                    body: that.urldecode(obj.content),
                                                    icon: ""
                                                });
                                                isNotificated = true;
                                                // 两秒后关闭通知
                                                setTimeout(function () {
                                                    n.close();
                                                }, 5000);
                                            }
                                        });
                                    }
                                    if (!isNotificated) {
                                        that.$notify({
                                            title: that.urldecode(obj.user.user_name) + "@了你：",
                                            message: that.urldecode(obj.content) + `<span class="notify-at-goto" onclick="scrollFuncs.scrollToChat(${obj.message_id})">[查看]</span>`,
                                            duration: 0,
                                            dangerouslyUseHTMLString: true
                                            // offset: 70,
                                        });

                                    }
                                }
                            }
                            obj.content = '@' + obj.at.user_name + " " + obj.content;
                        } else {
                            if (that.chat_room.isVideoFullScreen) {
                                that.$notify({
                                    title: that.urldecode(obj.user.user_name) + "说：",
                                    message: that.urldecode(obj.content),
                                    duration: 5000,
                                    // offset: 70,
                                });
                            }
                        }
                        that.chat_room.list.push(obj);
                        document.title = that.urldecode(obj.user.user_name) + "说：" + that.urldecode(obj.content);
                        clearTimeout(that.chat_room.timerForWebTitle);
                        that.callParentFunction('onTextMessage', obj);
                        that.chat_room.timerForWebTitle = setTimeout(function () {
                            if (that.lockScreenData.ifLockSystem) {
                                document.title = '音乐播放器';
                            } else {
                                document.title = that.room.roomInfo.room_name;
                            }
                        }, 3000);
                        break;
                    case 'link':
                        if (obj.user.user_id == that.userInfo.user_id) {
                            for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                                if (that.chat_room.list[i].sha == 'loading') {
                                    that.chat_room.list.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        if (that.chat_room.list.length > that.chat_room.historyMax) {
                            that.chat_room.list.shift();
                        }
                        that.chat_room.list.push(obj);
                        that.autoScroll();
                        break;
                    case 'img':
                    case 'system':
                    case 'jump':
                        if (obj.user && obj.user.user_id == that.userInfo.user_id) {
                            for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                                if (that.chat_room.list[i].sha == 'loading') {
                                    that.chat_room.list.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        if (that.chat_room.list.length > that.chat_room.historyMax) {
                            that.chat_room.list.shift();
                        }
                        that.chat_room.list.push(obj);
                        that.autoScroll();
                        break;
                    case 'join':
                        that.addSystemTips(obj.content);
                        break;
                    case 'addSong':
                        if (obj.at) {
                            that.addSystemMessage(that.urldecode(obj.user.user_name) + " 送了一首 《" + obj.song.name + "》(" + obj.song.singer + ") 给 " + that.urldecode(obj.at.user_name), '#409EFF', '#eee');
                            if (obj.at.user_id == that.userInfo.user_id) {
                                if (that.config.notification) {
                                    let isNotificated = false;
                                    if (window.Notification && Notification.permission !== "denied") {
                                        Notification.requestPermission(function (status) { // 请求权限
                                            if (status === 'granted') {
                                                // 弹出一个通知
                                                var n = new Notification(that.urldecode(obj.user.user_name) + "送了歌给你：", {
                                                    body: "《" + obj.song.name + "》(" + obj.song.singer + ")",
                                                    icon: ""
                                                });
                                                isNotificated = true;
                                                // 两秒后关闭通知
                                                setTimeout(function () {
                                                    n.close();
                                                }, 5000);
                                            }
                                        });
                                    }
                                    if (!isNotificated) {
                                        that.$notify({
                                            title: that.urldecode(obj.user.user_name) + "送了歌给你：",
                                            message: "《" + obj.song.name + "》(" + obj.song.singer + ")",
                                            duration: 5000
                                        });
                                    }
                                }
                            }
                        } else {
                            that.addSystemTips(that.urldecode(obj.user.user_name) + " 点了一首 《" + obj.song.name + "》(" + obj.song.singer + ")");
                        }

                        break;
                    case 'chat_bg':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 运气大爆发,触发了点歌背景墙特效(1小时内播放歌曲时有效)!", 'green', '#eee');

                        break;
                    case 'push':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 设为置顶候播放");

                        break;
                    case 'removeSong':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 从队列移除");

                        break;
                    case 'removeban':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将 " + that.urldecode(obj.ban.user_name) + " 解禁");

                        break;
                    case 'shutdown':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 发言");

                        break;
                    case 'songdown':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 点歌");

                        break;
                    case 'pass':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 切掉了当前播放的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');

                        break;
                    case 'passGame':
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " PASS了当前的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');

                        break;
                    case 'all':
                        that.addSystemMessage(obj.content, '#fff', '#666');

                        break;
                    case 'back':
                        for (let i = 0; i < that.chat_room.list.length; i++) {
                            if (parseInt(that.chat_room.list[i].message_id) == parseInt(obj.message_id)) {
                                that.chat_room.list.splice(i, 1);
                                break;
                            }
                        }
                        that.addSystemMessage(that.urldecode(obj.user.user_name) + " 撤回了一条消息");
                        break;
                    case 'playSong':
                        if (obj.song && (that.room.roomInfo.room_type == 1 || that.room.roomInfo.room_type == 2 || that.room.roomInfo.room_type == 4)) {
                            if (that.globalMusicSwitch) {
                                that.doPlayMusic(obj);
                            }
                        }
                        break;
                    case 'online':
                        that.chat_room.data.onlineList = obj.data;
                        break;
                    case 'roomUpdate':
                        that.initNowRoomInfo();
                        break;
                    case 'game_music_success':
                        that.addSystemMessage("恭喜 " + that.urldecode(obj.user.user_name) + " 猜中了《" + obj.song.name + "》(" + obj.song.singer + "),30s后开始新一轮游戏", '#ff4500', '#eee');
                        that.chat_room.song.song.pic = that.http2https(obj.song.pic);
                        that.chat_room.song.song.name = obj.song.name;
                        that.chat_room.song.song.singer = obj.song.singer;
                        break;
                    case 'story':
                        that.addSystemMessage('正在播放声音《' + obj.story.name + '》(' + obj.story.part + ')', '#409EFF', '#eee');
                        that.audioUrl = obj.story.play;
                        that.chat_room.voice = obj.story;
                        that.isAudioCurrentTimeChanged = false;

                        break;
                    default:
                }
            } catch (error) {
                console.log(error)
            }
            that.autoScroll();
        },
        http2https(str) {
            return str.toString().replace('http://', 'https://');
        },
        doPlayMusic(obj) {
            let that = this;
            // if (that.chat_room.song) {
            //     //is playing
            //     if (obj.song.mid == that.chat_room.song.song.mid && that.room.roomInfo.room_type != 4) {
            //         return;
            //     }
            // }
            that.chat_room.song = false;
            that.audioUrl = "";
            setTimeout(function () {
                obj.song.pic = that.http2https(obj.song.pic);
                console.log(that.chat_room.song);
                that.chat_room.songPercent = 0;
                that.chat_room.song = obj;
                that.isAudioCurrentTimeChanged = false;
                that.audioUrl = "https://api.bbbug.com/api/song/playurl?mid=" + obj.song.mid;

                that.volume = localStorage.getItem('volume') == null ? 50 : parseInt(localStorage.getItem('volume'));
                localStorage.setItem('volume', that.volume);
                localStorage.setItem('volume_old', that.volume);
                that.$refs.audio.volume = parseFloat(that.volume / 100);

                that.lockScreenData.musicHead = obj.song.pic || '//cdn.bbbug.com/images/nohead.jpg';
                that.lockScreenData.musicString = "《" + obj.song.name + "》(" + obj.song.singer + ") ";
                if (obj.at) {
                    that.addSystemMessage("正在播放 " + that.urldecode(obj.user.user_name) + " 送给 " + that.urldecode(obj.at.user_name) + " 的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", 'white', 'lightsalmon');
                }
                switch (that.room.roomInfo.room_type) {
                    case 1:
                        that.getMusicLrc();
                        if (obj.user.user_id == that.userInfo.user_id) {
                            if (that.config.notification) {
                                let isNotificated = false;
                                if (window.Notification && Notification.permission !== "denied") {
                                    Notification.requestPermission(function (status) { // 请求权限
                                        if (status === 'granted') {
                                            // 弹出一个通知
                                            var n = new Notification("正在播放你点的歌", {
                                                body: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                                icon: ""
                                            });
                                            isNotificated = true;
                                            // 两秒后关闭通知
                                            setTimeout(function () {
                                                n.close();
                                            }, 5000);
                                        }
                                    });
                                }
                                if (!isNotificated) {
                                    that.$notify({
                                        title: "正在播放你点的歌曲",
                                        message: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                        duration: 5000
                                    });
                                }
                            }
                        }
                        if (obj.at.user_id == that.userInfo.user_id) {
                            if (that.config.notification) {
                                let isNotificated = false;
                                if (window.Notification && Notification.permission !== "denied") {
                                    Notification.requestPermission(function (status) { // 请求权限
                                        if (status === 'granted') {
                                            // 弹出一个通知
                                            var n = new Notification("正在播放 " + that.urldecode(obj.user.user_name) + " 送你的歌", {
                                                body: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                                icon: ""
                                            });
                                            isNotificated = true;
                                            // 两秒后关闭通知
                                            setTimeout(function () {
                                                n.close();
                                            }, 5000);
                                        }
                                    });
                                }
                                if (!isNotificated) {
                                    that.$notify({
                                        title: "正在播放 " + that.urldecode(obj.user.user_name) + " 送你的歌",
                                        message: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                        duration: 5000
                                    });
                                }
                            }
                        }
                        that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\n" + location.href + that.room.room_id;
                        if (that.room.roomInfo.room_domain && that.room.roomInfo.room_domainstatus) {
                            if (location.href.indexOf('bbbug.com') < 0) {
                                //使用的独立域名
                                that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\n" + location.href;
                            } else {
                                that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\nhttps://" + that.room.roomInfo.room_domain + ".bbbug.com";
                            }
                        }
                        break;
                    case 2:
                        that.addSystemTips("仔细听,猜猜是什么歌曲(直接在聊天框输入答案发送即可)");
                    case 4:
                        that.getMusicLrc();
                        that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\n" + location.href + that.room.room_id;
                        if (that.room.roomInfo.room_domain && that.room.roomInfo.room_domainstatus) {
                            if (location.href.indexOf('bbbug.com') < 0) {
                                //使用的独立域名
                                that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\n" + location.href;
                            } else {
                                that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\nhttps://" + that.room.roomInfo.room_domain + ".bbbug.com";
                            }
                        }
                }
            }, 100);

        },
        getImageUrl(url) {
            if (!url) {
                return '';
            }
            if (url.indexOf('https://') > -1 || url.indexOf('http://') > -1) {
                return url.replace('http:', 'https:');
            } else {
                return 'https://cdn.bbbug.com/uploads/' + url;
            }
        },
        urldecode(str) {
            try {
                return decodeURIComponent(str);
            } catch (error) {
                return null;
            }
        },
        handleProfileHeadUploadSuccess(res, file) {
            var that = this;
            if (res.code == 200) {
                that.chat_room.form.editMyProfile.user_head = that.getImageUrl(res.data.attach_thumb);
            } else {
                that.$message.error(res.msg);
            }
        },
        doGlobalMusicSwitch() {
            let that = this;
            if (that.globalMusicSwitch) {
                that.$confirm('是否确认直到手动开启前禁止房间自动播放音乐?', '房间静音', {
                    confirmButtonText: '关闭音乐',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    that.globalMusicSwitch = false;
                    that.audioUrl = '';
                    that.chat_room.song = null;
                    localStorage.setItem('globalMusicSwitch', that.globalMusicSwitch ? "on" : "off");
                }).catch(function () { });
            } else {
                that.globalMusicSwitch = true;
                that.websocket.connection.send('getNowSong');
                localStorage.setItem('globalMusicSwitch', that.globalMusicSwitch ? "on" : "off");
            }
        },
        handleSettingCommand(cmd) {
            let that = this;
            switch (cmd) {
                case 'doGlobalMusicSwitch':
                    that.doGlobalMusicSwitch();
                    break;
                case 'doEditMyProfile':
                    that.doEditMyProfile();
                    break;
                case 'doShowQrcode':
                    that.doShowQrcode();
                    break;
                case 'clearHistory':
                    that.clearHistory();
                    break;
                case 'contactUs':
                    window.open('https://doc.bbbug.com');
                    break;
                case 'downloadWindows':
                    window.open('https://cdn.bbbug.com/install/windows_v1.0.4.zip');
                    break;
                case 'downloadApp':
                    window.open('https://gitee.com/bbbug_com/ChatAPP');
                    break;
                case 'downloadVSC':
                    window.open('https://my.oschina.net/majhamm/blog/4687654');
                    break;
                case 'switchNotification':
                    that.config.notification = !that.config.notification;
                    if (that.config.notification) {
                        if (window.Notification && Notification.permission !== "denied") {
                            Notification.requestPermission(function (status) { // 请求权限
                                if (status === 'granted') {
                                    var n = new Notification("通知已开启,你将收到@提醒和歌曲通知");
                                    setTimeout(function () {
                                        n.close();
                                    }, 5000);
                                }
                            });
                        }
                        that.addSystemTips('通知已开启,你将收到@提醒和歌曲通知');
                    } else {
                        that.addSystemTips('通知已关闭,你将无法@提醒和歌曲通知');
                    }
                    break;
                case 'doLogout':
                    that.doLogout();
                    break;
                default:
            }
        },
        doSwitchMusic() {
            let that = this;
            that.config.playMusic = !that.config.playMusic;
            if (that.config.playMusic) {
                that.addSystemTips('音乐已打开');
                that.volume = parseInt(localStorage.getItem('volume_old')) || 50;
                localStorage.setItem('volume', that.volume);
                localStorage.setItem('volume_old', that.volume);
                that.$refs.audio.volume = parseFloat(that.volume / 100);
            } else {
                that.addSystemTips('音乐已静音');
                that.$refs.audio.volume = 0;
                that.volume = 0;
                localStorage.setItem('volume', that.volume);
            }
        },
        clearHistory() {
            var that = this;
            that.$confirm('是否确认清空当前房间聊天记录?', '删除聊天记录', {
                confirmButtonText: '删除',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                that.request({
                    url: "message/clear",
                    data: {
                        room_id: that.room.room_id,
                    },
                    success(res) {
                        that.$message.success('删除房间聊天记录成功');
                    }
                });
            }).catch(function () { });
        },
        handleImageUploadSuccess(res, file) {
            var that = this;
            if (res.code == 200) {
                that.request({
                    url: "message/send",
                    data: {
                        where: 'channel',
                        to: that.websocket.params.channel,
                        type: 'img',
                        msg: res.data.attach_thumb,
                        resource: res.data.attach_path,
                    },
                    success(res) {
                        that.chat_room.message = '';
                    }
                });
            } else {
                that.$message.error(res.msg);
            }
        },
        doPushSongTop(row) {
            let that = this;
            that.request({
                url: "song/push",
                data: {
                    room_id: that.room.room_id,
                    mid: row.song.mid
                },
                success(res) {
                    that.$message.success(res.msg);
                    that.doSongListUpdate();
                }
            });
        },
        addMySong() {
            let that = this;
            that.request({
                url: "song/addMySong",
                data: {
                    room_id: that.room.room_id,
                    mid: that.chat_room.song.song.mid
                },
                success(res) {
                    that.$message.success(res.msg);
                }
            });
        },
        doDeleteMySong(row) {
            let that = this;
            that.$confirm('是否确认将这首歌从歌单中删除?', '删除提醒', {
                confirmButtonText: '删除',
                cancelButtonText: '取消',
                closeOnClickModal: false,
                closeOnPressEscape: false,
                type: 'warning'
            }).then(function () {
                that.request({
                    url: "song/deleteMySong",
                    data: {
                        room_id: that.room.room_id,
                        mid: row.mid
                    },
                    success(res) {
                        that.$message.success(res.msg);
                        that.doGetMySongList(that.room.roomInfo.room_type == 4 ? 'recent' : 'count');
                    }
                });
            }).catch(function () { });
        },
        doDeleteSong(row) {
            let that = this;
            that.$confirm('是否确认将这首歌从队列中移除?', '移除提醒', {
                confirmButtonText: '移除',
                cancelButtonText: '取消',
                closeOnClickModal: false,
                closeOnPressEscape: false,
                type: 'warning'
            }).then(function () {
                that.request({
                    url: "song/remove",
                    data: {
                        room_id: that.room.room_id,
                        mid: row.song.mid
                    },
                    success(res) {
                        that.$message.success(res.msg);
                        that.doSongListUpdate();
                    }
                });
            }).catch(function () { });
        },
        doShowSongList() {
            let that = this;
            if (that.chat_room.dialog.pickedSongBox) {
                that.chat_room.dialog.pickedSongBox = false;
            } else {
                that.hideAllDialog();
                that.chat_room.dialog.pickedSongBox = true;
                that.doSongListUpdate();
            }
        },
        doShowMySongList(order) {
            let that = this;
            if (that.chat_room.dialog.mySongBox) {
                that.chat_room.dialog.mySongBox = false;
            } else {
                that.hideAllDialog();
                that.chat_room.dialog.mySongBox = true;
                that.chat_room.data.mySongListPage = 1;
                that.doGetMySongList(order);
            }
        },
        doMySongBoxScroll(e) {
            let that = this;
            if (that.chat_room.data.isLoadingMySongBox) {
                return;
            }
            if (e.target.scrollHeight - e.target.scrollTop < 300 + 50) {
                that.chat_room.data.mySongListPage++;
                that.doGetMySongList(that.room.roomInfo.room_type == 4 ? 'recent' : 'count')
            }
        },
        doGetMySongList(order) {
            let that = this;
            that.chat_room.loading.mySongBox = true;
            that.chat_room.data.isLoadingMySongBox = true;
            that.request({
                url: "song/mySongList",
                data: {
                    order: order,
                    page: that.chat_room.data.mySongListPage
                },
                success(res) {
                    that.chat_room.data.isLoadingMySongBox = false;
                    that.chat_room.loading.mySongBox = false;
                    if (that.chat_room.data.mySongListPage == 1) {
                        that.chat_room.data.mySongList = res.data;
                        that.$refs.mySongBox.scrollTop = 0;
                    } else {
                        for (let i = 0; i < res.data.length; i++) {
                            that.chat_room.data.mySongList.push(res.data[i]);
                        }
                    }
                }, error() {
                    that.chat_room.data.isLoadingMySongBox = false;
                }
            });
        },
        doSongListUpdate() {
            let that = this;
            that.chat_room.loading.pickedSongBox = true;
            that.request({
                url: "song/songList",
                data: {
                    room_id: that.room.room_id,
                },
                success(res) {
                    that.chat_room.loading.pickedSongBox = false;
                    that.chat_room.data.pickedSongList = res.data;
                }
            });
        },
        doSaveRoomInfo() {
            let that = this;
            that.request({
                url: "room/saveMyRoom",
                data: Object.assign({}, that.chat_room.form.editMyRoom, {
                    room_id: that.room.room_id
                }),
                success(res) {
                    that.$message.success(res.msg);
                    that.chat_room.dialog.editMyRoom = false;
                }
            });
        },
        doSendImage(url) {
            let that = this;
            that.request({
                url: "message/send",
                data: {
                    where: 'channel',
                    to: that.websocket.params.channel,
                    type: 'img',
                    msg: url,
                    resource: url,
                },
                success(res) {
                    // this.$message.success('表情发送成功');
                    that.chat_room.dialog.searchImageBox = false;
                }
            });
        },
        doAddSong(row) {
            let that = this;
            that.chat_room.form.pickSong = row;
            that.chat_room.loading.searchSongBox = true;
            that.request({
                url: "song/addSong",
                data: {
                    mid: row.mid,
                    at: that.chat_room.songSendUser.user_id,
                    room_id: that.room.room_id
                },
                success(res) {
                    that.chat_room.loading.searchSongBox = false;
                    that.chat_room.songSendUser = false;
                    that.$message.success(res.msg);
                },
                login() {
                    that.chat_room.loading.searchSongBox = false;
                    that.$confirm(response.data.msg, '无权访问', {
                        confirmButtonText: '登录',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        that.doShowLoginBox();
                    }).catch(function () {
                        that.doLogout();
                        that.doJoinRoomById(that.room.room_id);
                    });
                }, error(res) {
                    that.chat_room.loading.searchSongBox = false;
                    that.$message.error(res.msg);
                }
            });
        },
        doTouch(user) {
            let that = this;
            that.request({
                url: "message/touch",
                data: {
                    at: user.user_id,
                    room_id: that.room.room_id
                },
                success(res) {
                    that.$message.success(res.msg);
                }
            });
        },
        doPlaySong(row) {
            let that = this;
            that.chat_room.form.pickSong = row;
            that.request({
                url: "song/playSong",
                data: {
                    mid: row.mid,
                    room_id: that.room.room_id
                },
                success(res) {
                    that.$message.success(res.msg);
                    that.doSongListUpdate();
                }
            });
        },
        doSearchImage() {
            let that = this;
            if (!that.chat_room.form.searchImageBox.keyword) {
                return;
            }
            that.chat_room.loading.searchImageBox = true;
            axios.post(that.apiUrl + 'attach/search', {
                keyword: that.chat_room.form.searchImageBox.keyword
            })
                .then(function (response) {
                    that.chat_room.data.searchImageList = response.data.data;
                    that.chat_room.loading.searchImageBox = false;
                })
                .catch(function (error) {
                    that.chat_room.loading.searchImageBox = false;
                });
        },
        doPassTheSong() {
            let that = this;
            that.$confirm('是否确认切掉当前正在播放的歌曲?', '切歌提醒', {
                confirmButtonText: '切歌',
                cancelButtonText: '取消',
                closeOnClickModal: false,
                closeOnPressEscape: false,
                type: 'warning'
            }).then(function () {
                that.request({
                    url: "song/pass",
                    // loading: true,
                    data: {
                        room_id: that.room.room_id,
                        mid: that.chat_room.song.song.mid
                    },
                });
            }).catch(function () { });
        },
        doDontLikeTheSong() {
            let that = this;
            that.request({
                url: "song/pass",
                // loading: true,
                data: {
                    room_id: that.room.room_id,
                    mid: that.chat_room.song.song.mid
                },
                success(res) {
                    that.$message.success(res.msg);
                    that.addSystemTips('你选择了不喜欢这首歌,已自动静音,下首歌自动开启音乐.');
                    that.volume = 0;
                    that.config.playMusic = false;
                    that.$refs.audio.volume = 0;
                }
            });
        },
        doSearchSong() {
            let that = this;
            that.chat_room.loading.searchSongBox = true;
            that.request({
                url: "song/search",
                data: {
                    keyword: that.chat_room.form.searchSongBox.keyword
                },
                success(res) {
                    that.$refs.searchSongBox.scrollTop = 0;
                    that.chat_room.data.searchSongList = res.data;
                    that.$message.info('温馨提示,你可以使用Ctrl/Alt/Command+对应数字快速点歌');
                    that.chat_room.loading.searchSongBox = false;
                },
                error(res) {
                    that.chat_room.loading.searchSongBox = false;
                    that.$message.error(res.msg);
                }
            });
        },
        doSearchVoiceBoxScroll(e) {
            let that = this;
            if (that.chat_room.data.isLoadingVoiceBox) {
                return;
            }
            if (e.target.scrollHeight - e.target.scrollTop < 300 + 50) {
                that.chat_room.data.voiceBoxPage++;
                that.doSearchVoice();
            }
        },
        doShowVoiceSearchBox() {
            let that = this;
            that.chat_room.dialog.searchVoiceBox = !that.chat_room.dialog.searchVoiceBox;
            if (that.chat_room.dialog.searchVoiceBox) {
                that.chat_room.data.voiceBoxPage = 1;
                that.doSearchVoice();
            }
        },
        doSearchVoice() {
            let that = this;
            that.chat_room.loading.searchVoiceBox = true;
            that.chat_room.data.isLoadingVoiceBox = true;
            that.request({
                url: "story/search",
                data: {
                    keyword: that.chat_room.form.searchVoiceBox.keyword,
                    page: that.chat_room.data.voiceBoxPage,
                },
                success(res) {
                    that.chat_room.data.isLoadingVoiceBox = false;
                    that.chat_room.loading.searchVoiceBox = false;
                    if (that.chat_room.data.voiceBoxPage == 1) {
                        that.$refs.searchVoiceBox.scrollTop = 0;
                        that.chat_room.data.searchVoiceList = res.data;
                    } else {
                        for (let i = 0; i < res.data.length; i++) {
                            that.chat_room.data.searchVoiceList.push(res.data[i]);
                        }
                    }
                },
                error(res) {
                    that.chat_room.loading.searchVoiceBox = false;
                    that.chat_room.data.isLoadingVoiceBox = false;
                    that.$message.error(res.msg);
                }
            });
        },
        doPlayVoice(row) {
            let that = this;
            that.$confirm('是否停掉当前正在播放的故事?', '播放提醒', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                closeOnClickModal: false,
                closeOnPressEscape: false,
                type: 'warning'
            }).then(function () {
                that.request({
                    url: "story/playStory",
                    loading: true,
                    data: {
                        mid: row.mid,
                        cid: row.cid,
                        room_id: that.room.room_id
                    },
                    success(res) {
                        that.$message.success(res.msg);
                    }
                });
            }).catch(function () { });
        },
        doSaveMyProfile() {
            let that = this;
            if (!that.chat_room.form.editMyProfile.user_name) {
                this.$message.error('你确定不输入一个好听的名字吗???');
                return;
            }
            that.request({
                url: "user/updateMyInfo",
                loading: true,
                data: that.chat_room.form.editMyProfile,
                success(res) {
                    that.getMyInfo();
                    that.$message.success(res.msg);
                    that.chat_room.dialog.editMyProfile = false;
                }
            });
        },
        doUploadBefore(file) {
            const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif';
            const isLt2M = file.size / 1024 / 1024 < 2;

            if (!isJPG) {
                this.$message.error('发送图片只能是 JPG/PNG/GIF 格式!');
            }
            if (!isLt2M) {
                this.$message.error('发送图片大小不能超过 2MB!');
            }
            return isJPG && isLt2M;
        },
        getClipboardFiles(event) {
            var that = this;
            let items = event.clipboardData && event.clipboardData.items;
            let file = null
            if (items && items.length) {
                // 检索剪切板items
                for (var i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        file = items[i].getAsFile()
                    }
                }
            }
            if (file) {
                if (that.doUploadBefore(file)) {
                    let param = new FormData();
                    param.append('file', file);
                    param.append('access_token', that.baseData.access_token);
                    param.append('plat', that.baseData.plat);
                    param.append('version', that.baseData.version);
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                    // 添加请求头
                    axios.post(that.apiUrl + 'attach/uploadimage', param, config)
                        .then(function (res) {
                            if (res.data.code == 200) {
                                that.request({
                                    url: "message/send",
                                    data: {
                                        where: 'channel',
                                        to: that.websocket.params.channel,
                                        type: 'img',
                                        msg: res.data.data.attach_thumb,
                                        resource: res.data.data.attach_path,
                                    },
                                    success(res) { }
                                });
                            } else {
                                that.$message.error(res.data.msg);
                            }
                        })
                        .catch(function (error) {
                            that.$message.error("上传图片发生错误");
                        });
                }
            }
            return;
        },
        doDelete() {
            this.hideAllDialog();
        },
        doEnterDown(e) {
            let that = this;
            if (that.ctrlEnabled) {
                //开启了ctrl+enter
                if (window.event.ctrlKey) {
                    e.preventDefault();
                    that.doSendMessage();
                }
            } else {
                e.preventDefault();
                that.doSendMessage();
            }
        },
        doShowOnlineList() {
            let that = this;
            that.request({
                url: "user/online",
                data: {
                    room_id: that.room.room_id,
                },
                success(res) {
                    that.chat_room.data.onlineList = res.data;
                }
            });
        },
        doShowSettingBox() {
            let that = this;
            that.chat_room.form.editMyRoom.room_name = that.room.roomInfo.room_name;
            that.chat_room.form.editMyRoom.room_notice = that.room.roomInfo.room_notice;
            that.chat_room.form.editMyRoom.room_type = that.room.roomInfo.room_type;
            that.chat_room.form.editMyRoom.room_sendmsg = that.room.roomInfo.room_sendmsg;
            that.chat_room.form.editMyRoom.room_addsong = that.room.roomInfo.room_addsong;
            that.chat_room.form.editMyRoom.room_robot = that.room.roomInfo.room_robot;
            that.chat_room.form.editMyRoom.room_public = that.room.roomInfo.room_public;
            that.chat_room.form.editMyRoom.room_playone = that.room.roomInfo.room_playone;
            that.chat_room.form.editMyRoom.room_domain = that.room.roomInfo.room_domain;
            that.chat_room.form.editMyRoom.room_domain_edit = that.room.roomInfo.room_domain ? false : true;
            that.chat_room.form.editMyRoom.room_password = '';
            that.chat_room.form.editMyRoom.room_huya = that.room.roomInfo.room_huya;
            that.chat_room.form.editMyRoom.room_votepercent = that.room.roomInfo.room_votepercent;
            that.chat_room.form.editMyRoom.room_votepass = that.room.roomInfo.room_votepass;
            that.chat_room.dialog.editMyRoom = true;
        },
        doEditMyProfile() {
            let that = this;
            that.chat_room.form.editMyProfile.user_name = that.urldecode(that.userInfo.user_name);
            that.chat_room.form.editMyProfile.user_touchtip = that.urldecode(that.userInfo.user_touchtip);
            that.chat_room.form.editMyProfile.user_remark = that.userInfo.user_remark;
            that.chat_room.form.editMyProfile.user_sex = that.userInfo.user_sex;
            that.chat_room.form.editMyProfile.user_head = that.userInfo.user_head;
            that.chat_room.form.editMyProfile.user_password = "";
            that.chat_room.dialog.editMyProfile = true;
        },
        doSendMessage() {
            let that = this;
            if (!that.chat_room.message) {
                return;
            }
            let msg = that.chat_room.message;
            if (msg.indexOf("点歌") == 0) {
                let songName = msg.replace("点歌", "");
                that.hideAllDialog();
                that.chat_room.dialog.searchSongBox = true;
                that.chat_room.form.searchSongBox.keyword = songName;
                that.doSearchSong()
                return;
            }
            if (msg.indexOf("音量") == 0) {
                let volume = parseInt(msg.replace(/音量/g, '').replace(/\/\//g, ''));
                if (msg == '音量' + volume) {
                    if (volume < 0 || volume > 100) {
                        return;
                    } else {
                        that.volume = volume;
                        if (that.volume == 0) {
                            that.config.playMusic = false;
                        } else {
                            that.config.playMusic = true;
                        }
                        that.$refs.audio.volume = parseFloat(volume / 100);
                        this.addSystemTips("音量已经设置为" + volume + "%");
                        localStorage.setItem('volume', volume);
                        localStorage.setItem('volume_old', volume);
                        that.chat_room.message = '';
                        return;
                    }
                }
            }
            that.chat_room.message = '';
            if (that.userInfo.user_id > 0) {
                that.chat_room.list.push({
                    key: "loading",
                    sha: "loading",
                    content: encodeURIComponent(msg),
                    type: "text",
                    user: that.userInfo
                });
            }
            that.autoScroll();
            that.request({
                url: "message/send",
                data: {
                    where: 'channel',
                    to: that.websocket.params.channel,
                    type: 'text',
                    at: that.chat_room.at,
                    msg: encodeURIComponent(msg)
                },
                success(res) {
                    that.chat_room.message = '';
                    that.chat_room.at = false;

                    for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                        if (that.chat_room.list[i].sha == 'loading') {
                            that.chat_room.list.splice(i, 1);
                            break;
                        }
                    }
                },
                error(res) {
                    that.$message.error(res.msg);
                    that.chat_room.message = msg;
                    for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                        if (that.chat_room.list[i].sha == 'loading') {
                            that.chat_room.list.splice(i, 1);
                            break;
                        }
                    }
                }
            });
        },
        doWebsocketHeartBeat() {
            let that = this;
            if (that.websocket.hardStop) {
                return;
            }
            clearTimeout(that.websocket.heartBeatTimer);
            that.websocket.heartBeatTimer = setTimeout(function () {
                that.websocket.connection.send('heartBeat');
                that.doWebsocketHeartBeat();
            }, 10000);
        },
        doWebsocketError() {
            let that = this;
            if (that.websocket.hardStop) {
                return;
            }
            console.log("连接已断开，10s后将自动重连");
            clearTimeout(that.websocket.connectTimer);
            that.websocket.connectTimer = setTimeout(function () {
                that.initWebsocket();
            }, 1000);
        },
        doGetRoomList() {
            let that = this;
            that.request({
                url: "room/hotRooms",
                success(res) {
                    that.room.list = res.data;
                    that.room.showDialog = true;
                }
            });
        },
        doSearchRoomById() {
            let that = this;
            let room_id = that.room.search_id;
            if (!room_id) {
                return;
            }
            that.doJoinRoomById(room_id);
        },
        doJoinRoomById(room_id) {
            let that = this;
            that.room.showDialog = false;
            that.request({
                url: "room/getRoomInfo",
                data: {
                    room_id: room_id
                },
                success(res) {
                    let room = res.data;
                    that.room.room_id = room.room_id;
                    that.room.roomInfo = room;
                    // if(room.room_type==5){
                    //     if(location.href.indexOf('https://')>=0){
                    //         location.replace(location.href.replace('https://','http://'));
                    //         return;
                    //     }
                    // }else{
                    //     if(location.href.indexOf('https://')<0 && location.href.indexOf('bbbug.com')>=0){
                    //         location.replace(location.href.replace('http://','https://'));
                    //         return;
                    //     }
                    // }
                    that.doGetRoomData();
                },
                error(res) {
                    switch (res.code) {
                        case 301:
                            that.$alert(res.msg, '房间封禁', {
                                confirmButtonText: '确定',
                                callback: function () {
                                    if (!that.room.roomInfo) {
                                        that.doJoinRoomById(888);
                                    }
                                }
                            });
                            break;
                        case 302:
                            that.$prompt('请输入该房间的密码后进入', '加密房间', {
                                confirmButtonText: '验证',
                                showClose: false,
                                closeOnClickModal: false,
                                closeOnPressEscape: false,
                                closeOnHashChange: false,
                                center: true,
                                showCancelButton: that.room.roomInfo ? true : false,
                            }).then(function (password) {
                                that.checkRoomPassword(room_id, password.value, function (result, msg) {
                                    if (result) {
                                        that.room.room_id = room_id;
                                        localStorage.setItem('room_id', room_id);
                                        that.lrcString = '';
                                        that.lockScreenData.nowMusicLrcText = '';
                                        that.doGetRoomData();
                                    } else {
                                        if (that.room.roomInfo) {
                                            that.$alert(msg, '密码错误', {
                                                confirmButtonText: '确定',
                                                callback: function () { }
                                            });
                                        } else {
                                            that.$confirm(msg, '密码错误', {
                                                confirmButtonText: '重试',
                                                cancelButtonText: '去大厅',
                                                type: 'warning'
                                            }).then(function () {
                                                that.doJoinRoomById(room_id);
                                            }).catch(function () {
                                                that.doJoinRoomById(888);
                                            });
                                        }

                                    }
                                });
                            }).catch(function (e) {
                            });
                            break;
                        default:
                            that.$confirm(res.msg, '进入失败', {
                                confirmButtonText: '重试',
                                cancelButtonText: '去大厅',
                                type: 'warning'
                            }).then(function () {
                                that.doJoinRoomById(room_id);
                            }).catch(function () {
                                that.doJoinRoomById(888);
                            });
                    }
                },
            });
        },
        doSubmitCreateRoom(formName) {
            let that = this;
            that.$refs[formName].validate(function (valid) {
                if (valid) {
                    that.request({
                        url: "room/create",
                        loading: true,
                        data: that.room_create.form,
                        success(res) {
                            that.room_create.showPage = false;
                            that.getMyInfo();
                            that.$confirm('你的私人房间创建成功,是否立即进入?', '创建成功', {
                                confirmButtonText: '进入',
                                cancelButtonText: '返回列表',
                                type: 'warning'
                            }).then(function () {
                                that.doEnterMyRoom();
                            }).catch(function () { });
                        }
                    });
                }
            });
        },
        doEnterMyRoom() {
            let that = this;
            if (that.userInfo.myRoom) {
                that.doJoinRoomById(that.userInfo.myRoom.room_id);
            } else {
                that.$message.error('你还没有创建自己的房间呀~');
            }
        },
        doSendRandCode() {
            let that = this;
            that.request({
                url: "sms/email",
                loading: true,
                data: {
                    email: that.login.form.user_account
                },
                success(res) {
                    that.$message.success(res.msg);
                }
            });
        },
        do_login_email_changed() {
            let that = this;
            if (that.login.form.user_account) {
                that.login.validEmail = true;
            } else {
                that.login.validEmail = false;
            }
        },
        do_login_form_submit(formName) {
            let that = this;
            that.$refs[formName].validate(function (valid) {
                if (valid) {
                    that.request({
                        url: "user/login",
                        loading: true,
                        data: that.login.form,
                        success(res) {
                            that.baseData.access_token = res.data.access_token;
                            localStorage.setItem('access_token', that.baseData.access_token);
                            localStorage.setItem('user_account', that.login.form.user_account);
                            that.$message.success('登录成功!');
                            that.getMyInfo(function () {
                                that.login.showPage = false;
                                that.doJoinRoomById(that.room.room_id);
                            });
                        }
                    });
                }
            });
        },
        doLogout() {
            let that = this;
            that.userInfo = that.guestUserInfo;
            that.baseData.access_token = that.guestUserInfo.access_token;
            localStorage.setItem('access_token', that.baseData.access_token);
            that.login.showPage = false;
        },
        doShowLoginBox() {
            let that = this;
            that.login.showPage = true;
        },
        createLrcObj(lrc) {
            var oLRC = {
                ti: "", //歌曲名
                ar: "", //演唱者
                al: "", //专辑名
                by: "", //歌词制作人
                offset: 0, //时间补偿值，单位毫秒，用于调整歌词整体位置
                ms: [] //歌词数组{t:时间,c:歌词}
            };

            if (lrc.length == 0) {
                return;
            }
            var lrcs = lrc.split('\n');
            //用回车拆分成数组
            for (var i in lrcs) {
                //遍历歌词数组
                lrcs[i] = lrcs[i].replace(/(^\s*)|(\s*$)/g, "");
                //去除前后空格
                var t = lrcs[i].substring(lrcs[i].indexOf("[") + 1, lrcs[i].indexOf("]"));
                //取[]间的内容
                var s = t.split(":");
                //分离:前后文字
                if (isNaN(parseInt(s[0]))) {
                    //不是数值
                    for (var i in oLRC) {
                        if (i != "ms" && i == s[0].toLowerCase()) {
                            oLRC[i] = s[1];
                        }
                    }
                } else {
                    //是数值
                    var arr = lrcs[i].match(/\[(\d+:.+?)\]/g);
                    //提取时间字段，可能有多个
                    var start = 0;
                    for (var k in arr) {
                        start += arr[k].length; //计算歌词位置
                    }
                    var content = lrcs[i].substring(start); //获取歌词内容
                    if (!content) {
                        continue;
                    }
                    for (var k in arr) {
                        var t = arr[k].substring(1, arr[k].length - 1); //取[]间的内容
                        var s = t.split(":");
                        //分离:前后文字
                        oLRC.ms.push({
                            //对象{t:时间,c:歌词}加入ms数组
                            t: parseFloat((parseFloat(s[0]) * 60 + parseFloat(s[1])).toFixed(3)),
                            c: content
                        });
                    }
                }
            }
            oLRC.ms.sort(function (a, b) {
                //按时间顺序排序
                return a.t - b.t;
            });
            return oLRC;
        }
    }
});
