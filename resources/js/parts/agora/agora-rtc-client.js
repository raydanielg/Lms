import AgoraRTC from 'agora-rtc-sdk'
import EventEmitter from 'events'

(function () {
    "use strict"

    var liveEndedHtml = `<div class="no-result default-no-result d-flex align-items-center justify-content-center flex-column w-100 h-100">
        <div class="no-result-logo">
            <img src="/assets/default/img/no-results/support.png" alt="">
        </div>
        <div class="d-flex align-items-center flex-column mt-30 text-center">
            <h2 class="text-dark-blue">${liveEndedLang}</h2>
            <p class="mt-5 text-center text-gray font-weight-500">${redirectToPanelInAFewMomentLang}</p>
        </div>
    </div>`;

    var featherIconsConf = {width: 20, height: 20};
    var localTracks = {
        localStream: null,
        remoteStreams: null,
        localShareScreenStream: null,
        cameraIsActive: true,
        micIsActive: true,
        shareScreen: false,
        shareScreenActived: false,
    };

    var option = {
        appid: appId,
        token: rtcToken,
        uid: null,
        channel: channelName,
        role: streamRole, // host or audience
        audienceLatency: 2
    }

    class RTCClient {
        constructor(cameraStatus, microphone) {
            // Options for joining a channel
            this.option = {
                appId: '',
                channel: '',
                uid: '',
                token: '',
            }
            this.cameraVideoProfile = '1080p_2'; // https://docs.agora.io/en/Video/video_profile_web?platform=Web
            this.screenVideoProfile = '1080p_1';
            this.client = null
            this.localStream = null
            this._eventBus = new EventEmitter()
            this.mic = microphone
            this.camera = cameraStatus
            this.localStreams = {
                camera: {
                    id: "",
                    stream: null
                },
                screen: {
                    id: "",
                    stream: null
                }
            }
        }

        //init client and Join a channel
        joinChannel(option) {
            return new Promise((resolve, reject) => {
                this.client = AgoraRTC.createClient({mode: 'rtc', codec: "h264"})

                this.client.init(option.appid, () => {
                    console.log("init success")

                    this.clientListener()

                    this.client.join(option.token, option.channel, null, (uid) => {
                        console.log("join channel: " + this.option.channel + " success, uid: ", uid)

                        this.option = {
                            appid: option.appid,
                            token: option.token,
                            channel: option.channel,
                            uid: uid,
                        }

                        //this.createCameraStream(uid);

                        this.localStreams.camera.id = uid; // keep track of the stream uid

                        resolve()
                    }, (err) => {
                        console.error("client join failed", err)
                    })
                }, (err) => {
                    reject(err)
                    console.error(err)
                })
                console.log("appId", option.appid)
            })
        }

        publishCameraStream() {
            return new Promise((resolve, reject) => {
                // Create a local stream
                const localStream = AgoraRTC.createStream({
                    streamID: this.option.uid,
                    audio: this.mic,
                    video: this.camera,
                    screen: false,
                })

                localStream.setVideoProfile(this.cameraVideoProfile)

                // Initialize the local stream
                localStream.init(() => {
                    console.log("init local stream success")

                    // Publish the local stream
                    this.client.publish(localStream, (err) => {
                        console.log("publish failed")
                        console.error(err)
                    })

                    this.localStreams.camera.stream = localStream; // keep track of the camera stream for later

                    resolve(localStream)
                }, (err) => {
                    reject(err)
                    console.error("init local stream failed ", err)
                })
            })
        }

        joinChannelAsScreenShare(option) {
            const $this = this;

            return new Promise((resolve, reject) => {
                const screenStream = AgoraRTC.createStream({
                    streamID: $this.option.uid,
                    audio: true, // Set the audio attribute as false to avoid any echo during the call.
                    video: false,
                    screen: true, // screen stream
                    //mediaSource: 'screen', // Firefox: 'screen', 'application', 'window' (select one)
                });

                screenStream.setScreenProfile($this.screenVideoProfile); // set the profile of the screen

                screenStream.init(function () {
                    console.log("getScreen successful");
                    $this.localStreams.screen.stream = screenStream; // keep track of the screen stream

                    $this.toggleCamera(false, true);

                    $this.client.publish(screenStream, function (err) {
                        console.log("[ERROR] : publish screen stream error: " + err);
                    });

                    screenStream.on('stopScreenSharing', (evt) => {
                        $this._eventBus.emit('stopScreenSharing', evt)
                    })

                    resolve(screenStream)
                }, function (err) {
                    console.log("[ERROR] : getScreen failed", err);
                    $this.localStreams.screen.id = ""; // reset screen stream id
                    $this.localStreams.screen.stream = null; // reset the screen stream
                });
            })
        }

        stopScreenShare() {
            return new Promise((resolve, reject) => {
                if (this.localStreams.screen.stream) {
                    this.localStreams.screen.stream.disableVideo(); // disable the local video stream (will send a mute signal)
                    this.localStreams.screen.stream.stop(); // stop playing the local stream

                    this.client.unpublish(this.localStreams.screen.stream); // unpublish the screen client
                }

                this.localStreams.screen.id = ""; // reset the screen id
                this.localStreams.screen.stream = null; // reset the stream obj

                this.toggleCamera(true, true);

                resolve(this.localStreams.camera.stream)
            })
        }

        clientListener() {
            const client = this.client

            client.on('stream-added', (evt) => {
                // The stream is added to the channel but not locally subscribed
                this._eventBus.emit('stream-added', evt)
            })
            client.on('stream-subscribed', (evt) => {
                this._eventBus.emit('stream-subscribed', evt)
            })
            client.on('stream-removed', (evt) => {
                this._eventBus.emit('stream-removed', evt)
            })
            client.on('peer-online', (evt) => {
                this._eventBus.emit('peer-online', evt)
            })
            client.on('peer-leave', (evt) => {
                this._eventBus.emit('peer-leave', evt)
            })
        }

        on(eventName, callback) {
            this._eventBus.on(eventName, callback)
        }

        leaveChannel() {
            return new Promise((resolve, reject) => {
                // Leave the channel
                this.client.unpublish(this.localStream, (err) => {
                    console.log(err)
                })
                this.client.leave(() => {
                    // Stop playing the local stream

                    if (this.localStreams.camera.stream) {
                        if (this.localStreams.camera.stream.isPlaying()) {
                            this.localStreams.camera.stream.stop()
                        }
                        // Close the local stream
                        this.localStreams.camera.stream.close()
                    }

                    this.localStreams.camera.id = ""; // reset the camera id
                    this.localStreams.camera.stream = null; // reset the stream obj
                    this.client = null;

                    resolve()
                    console.log("client leaves channel success");
                }, (err) => {
                    reject(err)
                    console.log("channel leave failed");
                    console.error(err);
                })
            })
        }

        toggleCamera(enable, editPublish = false) {
            this.camera = enable;

            const $button = $('#cameraEffect');
            let icon = feather.icons['video'].toSvg(featherIconsConf);

            if (this.localStreams.camera.stream) {
                if (enable) {
                    this.localStreams.camera.stream.enableVideo();

                    if (editPublish) {
                        this.client.publish(this.localStreams.camera.stream);
                    }

                    $button.addClass('active');
                    $button.removeClass('disabled');
                } else {
                    this.localStreams.camera.stream.disableVideo()

                    if (editPublish) {
                        this.client.unpublish(this.localStreams.camera.stream);
                    }

                    $button.removeClass('active');
                    $button.addClass('disabled');
                    icon = feather.icons['video-off'].toSvg(featherIconsConf);
                }
            }

            $button.find('.icon').html(icon);

            return true;
        }

        toggleMicrophone(enable) {
            this.mic = enable;

            if (this.localStreams.camera.stream) {
                if (enable) {
                    this.localStreams.camera.stream.enableAudio()
                } else {
                    this.localStreams.camera.stream.disableAudio()
                }
            }

            return true;
        }
    }

    //

    function createRtc() {
        let rtc = new RTCClient(true, true);

        rtc.on('stream-added', (evt) => {
            let {stream} = evt
            console.log("[agora] [stream-added] stream-added", stream.getId())
            rtc.client.subscribe(stream)

            /*if (localTracks.remoteStreams) {
                handleLocalVideoBox(true);
            }*/
        })

        rtc.on('stream-subscribed', (evt) => {
            let {stream} = evt
            console.log("[agora] [stream-subscribed] stream-added", stream.getId())

            localTracks.remoteStreams = stream;
            console.log('$$$$$$$$$$$$$$$$$$$$$$$$$$')
            console.log(stream)
            console.log('$$$$$$$$$$$$$$$$$$$$$$$$$$')
            stream.play('remote-stream-player', {fit: 'cover'}, (err) => {
                if (err && err.status !== 'aborted') {
                    console.warn('trigger autoplay policy')
                }
            })

            handleLocalVideoBox(true);
            $(".agora-stream-loading").addClass('d-none');
        })

        rtc.on('stream-removed', (evt) => {
            let {stream} = evt
            console.log('[agora] [stream-removed] stream-removed', stream.getId())

            localTracks.remoteStreams = null;

            handleLocalVideoBox(false);
        })

        rtc.on('peer-online', (evt) => {

        })

        rtc.on('stopScreenSharing', (evt) => {
            handleCloseShareScreen()
        })

        rtc.on('peer-leave', (evt) => {
            localTracks.remoteStreams = null;

            handleLocalVideoBox(false);
        })

        window.rtc = rtc;

        joinEvent(rtc);
    }

    createRtc();

    function joinEvent(rtc) {

        rtc.joinChannel(option).then(() => {

            const startAt = (streamStartAt && streamStartAt > 0) ? (new Date().getTime() / 1000) - streamStartAt : 0;
            handleTimer(startAt);

            rtc.publishCameraStream().then((stream) => {
                localTracks.localStream = stream;

                stream.play('local-stream-player', {fit: 'cover'}, (err) => {
                    if (err && err.status !== 'aborted') {
                        console.warn('trigger autoplay policy')
                    }
                })

                $(".agora-stream-loading").addClass('d-none');

            }).catch((err) => {
                console.log('publish local error', err)
            })
        }).catch((err) => {
            console.log('join channel error', err)
        })
    }

    function leaveEvent() {
        rtc.leaveChannel().then(() => {
            if (redirectAfterLeave) {
                window.location = redirectAfterLeave;
            }
        }).catch((err) => {

        })
    }

    function toggleCamera() {
        if (!localTracks.shareScreenActived) {
            localTracks.cameraIsActive = !localTracks.cameraIsActive;

            rtc.toggleCamera(localTracks.cameraIsActive);
        }
    }

    function toggleMicrophone(effect) {
        localTracks.micIsActive = effect;
        rtc.toggleMicrophone(effect);
    }

    function handleLocalVideoBox(add) {
        const localVideoStream = $('.local-stream');
        const remoteVideoStream = $('.remote-stream');

        if (add) {
            localVideoStream.addClass('has-remote-stream');
            remoteVideoStream.removeClass('d-none')
        } else {
            localVideoStream.removeClass('has-remote-stream');
            remoteVideoStream.addClass('d-none')
        }
    }

    function handleShareScreen() {
        if (!localTracks.shareScreen) {
            localTracks.shareScreen = true;
            localTracks.cameraIsActive = false;

            rtc.joinChannelAsScreenShare(option).then(stream => {

                localTracks.localShareScreenStream = stream

                if (localTracks.localStream) {
                    localTracks.localStream.stop()
                }

                localTracks.localStream = null;
            })
        } else {
            handleCloseShareScreen()
        }
    }

    function handleCloseShareScreen() {
        localTracks.cameraIsActive = true;
        localTracks.shareScreen = false

        rtc.stopScreenShare().then(stream => {
            if (localTracks.localShareScreenStream) {
                localTracks.localShareScreenStream.stop();
            }

            localTracks.localShareScreenStream = null;

            if (stream) {
                localTracks.localStream = stream;
            }
        });
    }

    function handleEndStream() {

        if (option.role === 'host') {
            leaveEvent()
        } else {
            $(`#player-wrapper-${id}`).html(liveEndedHtml);

            setTimeout(() => {
                if (redirectAfterLeave) {
                    window.location = redirectAfterLeave;
                }
            }, 3000);
        }
    }

    function handleTimer(startAt = 0) {
        const streamTimer = $('#streamTimer');

        const hoursLabel = streamTimer.find('.hours');
        const minutesLabel = streamTimer.find('.minutes');
        const secondsLabel = streamTimer.find('.seconds');

        let totalSeconds = startAt;

        setInterval(setTime, 1000);

        function setTime() {
            ++totalSeconds;
            const seconds = pad(Math.floor((totalSeconds) % 60));
            const minutes = pad(Math.floor((totalSeconds / 60) % 60));
            const hours = pad(Math.floor((totalSeconds / (60 * 60)) % 24));

            hoursLabel.html(hours);
            minutesLabel.html(minutes);
            secondsLabel.html(seconds);
        }

        function pad(val) {
            var valString = val + "";
            if (valString.length < 2) {
                return "0" + valString;
            } else {
                return valString;
            }
        }
    }


    $('body').on('click', '#leave', function (e) {
        const $this = $(this);
        const sessionId = $this.attr('data-id');

        $this.addClass('loadingbar primary').prop('disabled', true);

        const path = '/panel/sessions/' + sessionId + '/endAgora';

        $.get(path, function (result) {
            if (result && result.code === 200) {
                handleEndStream();
            }
        });
    });

    $('body').on('click', '#shareScreen', function (e) {
        handleShareScreen();
    });

    $('body').on('click', '#microphoneEffect', function (e) {
        const $this = $(this);

        let icon = feather.icons['mic'].toSvg(featherIconsConf);

        if (localTracks.micIsActive) {
            if ($this.hasClass('active')) {
                $this.removeClass('active');
                $this.addClass('disabled');

                icon = feather.icons['mic-off'].toSvg(featherIconsConf);

                toggleMicrophone(false)
            } else {
                $this.addClass('active');
                $this.removeClass('disabled');

                toggleMicrophone(true)
            }
        }

        $this.find('.icon').html(icon);
    });

    $('body').on('click', '#cameraEffect', function (e) {
        toggleCamera();
    });

    $('body').on('click', '#collapseBtn', function () {
        const $tabs = $('.agora-tabs');

        $tabs.toggleClass('show');
    });

    $('body').on('click', '#handleUsersJoin', function (e) {
        const $this = $(this);
        const notActive = $this.hasClass('dont-join-users');

        if (notActive) {
            $this.find('span').text(joinIsActiveLang);
        } else {
            $this.find('span').text(joiningIsDisabledLang);
        }

        $this.toggleClass('dont-join-users');

        $this.prop('disabled', true);

        $.get(`/panel/sessions/${sessionId}/toggleUsersJoinToAgora`, function (result) {
            if (result) {
                $.toast({
                    heading: result.heading,
                    text: result.text,
                    bgColor: (result.icon === 'error') ? '#f63c3c' : '#43d477',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: result.icon
                });
            }

            $this.prop('disabled', false);
        });
    });
})(jQuery)
