window.generateToken = function (callback) {
    const data = {
        channelName: channelName
    };

    $.post('/agora/token', data, function (result) {
        if(result && typeof callback === "function") {
            callback(result.token);
        }
    }).fail(err => {

    })
}
