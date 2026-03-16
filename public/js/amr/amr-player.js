(function (win, fuc) {
    win.AmrPlayer = fuc();
})(window, function () {
    var station = function () {
        if( typeof AMR !== 'function' ){
            throw new Error('没有引入libamr');
        }
        this.init();
    };

    station.prototype.init = function () {
        this.originBlob = null;
        this.errorTarget = null;

        this.libAmr = new AMR({
            benchmark: false
        });
        this.container = new container();
    };

    station.prototype.load = function (url) {
        var that = this;
        return new Promise(function (resolve, reject) {
            if( that.container.hasKey(url) ){
                resolve.call(that, that.container.getKey(url));
            }else{
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url);
                xhr.responseType = 'blob';
                xhr.onload = function() {
                    that.originBlob = this.response;
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var U8Array = new Uint8Array(e.target.result);
                        that.container.setKey(url, new player(that.libAmr.decode(U8Array)));
                        resolve.call(that, that.container.getKey(url));
                    };
                    reader.onerror = function(e){
                        that.errorTarget = e;
                        reject.call(that, that);
                    };
                    reader.readAsArrayBuffer(that.originBlob);
                };
                xhr.onerror = function(e) {
                    that.errorTarget = e;
                    reject.call(that, that);
                };
                xhr.send();
            }
        });
    };

    station.prototype.getLoaded = function (url) {
        if( this.container.hasKey(url) ){
            return this.container.hasKey(url);
        }else{
            return false;
        }
    };

    /**
     * 容器
     */
    var container = function(){
        this.reset();
    };

    container.prototype.hasKey = function(key){
        return typeof this.loaded[key] !== 'undefined';
    };

    container.prototype.getKey = function(key){
        return this.loaded[key]
    };

    container.prototype.setKey = function(key, state){
        this.loaded[key] = state;
        return this;
    };

    container.prototype.reset = function(){
        this.loaded = {};
    };

    var player = function (data) {
        this.reset();
        this.Float32ArrayData = data;
    };

    /**
     *
     * @returns {AudioContext}
     */
    player.prototype.getAudioContext = function(){
        if( !this.AudioContext ){
            this.AudioContext = new AudioContext();
        }
        return this.AudioContext;
    };

    player.prototype.reset = function(){
        this.audioSrc = null;
        this.audioBuffer = null;
        this.AudioContext = null;
        this.isConnected = false;
        this.isPlaying = false;
    };

    player.prototype.connect = function(){
        if( !this.isConnected ){
            var ctx = this.getAudioContext();
            this.audioSrc  = ctx.createBufferSource();
            this.audioBuffer = ctx.createBuffer(1, this.Float32ArrayData.length, 8000);
            if(this.audioBuffer.copyToChannel) {
                this.audioBuffer.copyToChannel(this.Float32ArrayData, 0, 0)
            } else {
                var channelBuffer = this.audioBuffer.getChannelData(0);
                channelBuffer.set(this.Float32ArrayData);
            }
            this.audioSrc.buffer = this.audioBuffer;
            this.audioSrc.connect(ctx.destination);
            this.isConnected = true;
            var that = this;
            this.audioSrc.addEventListener('ended', function (e) {
                that.isPlaying = false;
                that.destroy();
            });
        }
        return this;
    };

    player.prototype.disconnect = function(){
        if( this.isConnected ){
            this.audioSrc = null;
            this.audioBuffer = null;
            this.isConnected = false;
        }
        return this;
    };

    player.prototype.play = function(){
        if( this.isConnected ){
            if( !this.isPlaying ){
                this.audioSrc.start();
                this.isPlaying = true;
                return true;
            }
        }else{
            console.info('not connected');
        }
        return false;
    };

    player.prototype.stop = function(){
        if( this.isConnected && this.isPlaying ){
            this.audioSrc.stop();
            return true;
        }else{
            console.info('not connected');
        }
        return false;
    };

    player.prototype.destroy = function () {
        this.stop();
        this.disconnect();
        this.reset();
    };
    return new station();
});

function playAMRs(amrFile) {
    AmrPlayer.load(amrFile).then(function (res) {
        res.connect();
        res.play();
    });
}