var playerManager = (function () {
 
  // Storage for our vehicle types
  var types = {};
  var playerOptions = {};
  var currentType='';
 
  return {
      initPlayer: function ( type ) {
        if (currentType!=type){
            var Player = types[type];
            currentType=type;
            return (Player ? $("#player")[Player](playerOptions[type]) : null);
        } else{
            return false;
        }
      },
      sendMessageToPlayer: function(type, msg, parameters) {
        var Player = types[type];
        return (Player ? $("#player")[Player](msg, parameters) : null);
           
      },
 
      registerPlayer: function ( type, Player, options ) {
          var proto = Player.prototype;
 
          // only register classes that fulfill the vehicle contract
          //if ( proto.drive && proto.breakDown ) {
             types[type] = Player;
             playerOptions[type]=options
          //}
 
          return playerManager;
      }
  };
})();
        
        
