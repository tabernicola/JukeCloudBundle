var playerManager = {
    // Storage for our vehicle types
    types: {},
    playerOptions: {},
    currentType:'',
    currentPlayer:null,
    initPlayer: function (type) {
        if (this.currentType != type) {
            var Player = this.types[type];
            if (this.currentPlayer){
                $("#player")[this.types[this.currentType]]('destroy');
            }
            this.currentType = type;
            this.currentPlayer=(Player ? $("#player")[Player](this.playerOptions[type]) : null);
            return this.currentPlayer;
        } else {
            return false;
        }
    },
    sendMessageToPlayer: function (type, msg, parameters) {
        var Player = this.types[type];
        return (Player ? $("#player")[Player](msg, parameters) : null);

    },
    registerPlayer: function (type, Player, options) {

        // only register classes that fulfill the vehicle contract
        //if ( proto.drive && proto.breakDown ) {
        this.types[type] = Player;
        this.playerOptions[type] = options
        //}

        return this;
    }
};


